<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Excepciones\ReglaDeNegocioException;
use App\Nucleo\ControladorBase;
use App\Repositorios\RetiroRepositorio;
use App\Servicios\CuentaServicio;

class RetirosControlador extends ControladorBase
{
    private CuentaServicio $cuentas;
    private RetiroRepositorio $retiros;

    public function __construct()
    {
        parent::__construct();
        $this->cuentas = new CuentaServicio();
        $this->retiros = new RetiroRepositorio();
    }

    public function formularioAccion(): void
    {
        $this->vista('retiros/formulario', ['titulo' => 'Retirar dinero']);
    }

    public function retirarAccion(): void
    {
        if (!$this->esPost()) {
            $this->redirigir('retiros/formulario');
        }

        // Solo se leen valor y contraseña; la cuenta sale de la sesión.
        $valor = $this->entradaPost('valor');
        $clave = $this->entradaPost('clave', false);

        try {
            $this->cuentas->retirar($this->cuentaActivaId(), $valor, $clave);
            $this->guardarMensaje('exito', 'Retiro realizado correctamente.');
            $destino = 'cuenta/panel';
        } catch (ReglaDeNegocioException $e) {
            $this->guardarMensaje('error', $e->getMessage());
            $destino = 'retiros/formulario';
        }

        $this->redirigir($destino);
    }

    // RF5: cantidad, detalle y suma calculados sobre la tabla retiros.
    public function historialAccion(): void
    {
        $cuentaId = $this->cuentaActivaId();
        $resumen = $this->retiros->resumenPorCuenta($cuentaId);

        $this->vista('retiros/historial', [
            'titulo' => 'Historial de retiros',
            'retiros' => $this->retiros->obtenerPorCuenta($cuentaId),
            'cantidad' => $resumen['cantidad'],
            'total' => $resumen['total'],
        ]);
    }
}
