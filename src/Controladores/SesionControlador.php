<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Excepciones\CredencialesInvalidasException;
use App\Nucleo\ControladorBase;
use App\Servicios\AutenticacionServicio;

class SesionControlador extends ControladorBase
{
    private AutenticacionServicio $autenticacion;

    public function __construct()
    {
        parent::__construct();
        $this->autenticacion = new AutenticacionServicio();
    }

    // El login es la única pantalla que se puede ver sin sesión.
    protected function requiereSesion(): bool
    {
        return false;
    }

    public function loginAccion(): void
    {
        if ($this->haySesion()) {
            $this->redirigir('cuenta/panel');
        }

        $this->vista('login/formulario', [
            'titulo' => 'Iniciar sesión',
            'numeroCuenta' => '',
        ]);
    }

    public function autenticarAccion(): void
    {
        if (!$this->esPost()) {
            $this->redirigir('sesion/login');
        }

        $numeroCuenta = $this->entradaPost('numero_cuenta');
        $clave = $this->entradaPost('clave', false);

        try {
            $cuentaId = $this->autenticacion->autenticar($numeroCuenta, $clave);
        } catch (CredencialesInvalidasException $e) {
            $this->vista('login/formulario', [
                'titulo' => 'Iniciar sesión',
                'numeroCuenta' => $numeroCuenta,
                'mensaje' => ['tipo' => 'error', 'texto' => $e->getMessage()],
            ]);
            return;
        }

        // En la sesión solo se guarda el id de la cuenta: ni la contraseña ni el saldo.
        $this->iniciarSesionDeCuenta($cuentaId);
        $this->redirigir('cuenta/panel');
    }

    public function salirAccion(): void
    {
        $this->cerrarSesion();
        $this->redirigir('sesion/login');
    }
}
