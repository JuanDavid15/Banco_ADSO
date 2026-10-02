<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Excepciones\ReglaDeNegocioException;
use App\Nucleo\ControladorBase;
use App\Repositorios\TransferenciaRepositorio;
use App\Servicios\CuentaServicio;

class TransferenciasControlador extends ControladorBase
{
    private CuentaServicio $cuentas;
    private TransferenciaRepositorio $transferencias;

    public function __construct()
    {
        parent::__construct();
        $this->cuentas = new CuentaServicio();
        $this->transferencias = new TransferenciaRepositorio();
    }

    public function formularioAccion(): void
    {
        $this->vista('transferencias/formulario', ['titulo' => 'Transferir dinero']);
    }

    public function transferirAccion(): void
    {
        if (!$this->esPost()) {
            $this->redirigir('transferencias/formulario');
        }

        // El formulario no trae cuenta origen: es la de la sesión.
        $cuentaDestino = $this->entradaPost('cuenta_destino');
        $valor = $this->entradaPost('valor');
        $clave = $this->entradaPost('clave', false);

        try {
            $this->cuentas->transferir(
                $this->cuentaActivaId(),
                $cuentaDestino,
                $valor,
                $clave
            );
            $this->guardarMensaje('exito', 'Transferencia realizada correctamente.');
            $destino = 'cuenta/panel';
        } catch (ReglaDeNegocioException $e) {
            $this->guardarMensaje('error', $e->getMessage());
            $destino = 'transferencias/formulario';
        }

        $this->redirigir($destino);
    }

    // RF7: transferencias ENVIADAS por la cuenta de la sesión.
    public function historialAccion(): void
    {
        $cuentaId = $this->cuentaActivaId();
        $resumen = $this->transferencias->resumenEnviadasPorCuenta($cuentaId);

        $this->vista('transferencias/historial', [
            'titulo' => 'Historial de transferencias',
            'transferencias' => $this->transferencias->obtenerEnviadasPorCuenta($cuentaId),
            'cantidad' => $resumen['cantidad'],
            'total' => $resumen['total'],
        ]);
    }
}
