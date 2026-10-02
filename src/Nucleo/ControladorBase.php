<?php

declare(strict_types=1);

namespace App\Nucleo;

abstract class ControladorBase
{
    private const CLAVE_CUENTA = 'cuenta_id';
    private const CLAVE_MENSAJE = 'mensaje';

    // Verificación de sesión CENTRALIZADA: corre al crear cualquier controlador,
    // antes de ejecutar la acción. Una acción nueva no puede olvidarla.
    public function __construct()
    {
        if ($this->requiereSesion() && !$this->haySesion()) {
            $this->redirigir('sesion/login');
        }
    }

    // Por defecto todo controlador exige sesión; el de login lo desactiva.
    protected function requiereSesion(): bool
    {
        return true;
    }

    protected function haySesion(): bool
    {
        return isset($_SESSION[self::CLAVE_CUENTA]) && is_int($_SESSION[self::CLAVE_CUENTA]);
    }

    // ÚNICO punto del sistema que decide sobre qué cuenta se opera: el id sale
    // de $_SESSION (lo guardó el login), nunca de $_GET, $_POST ni de un campo oculto.
    protected function cuentaActivaId(): int
    {
        return (int) $_SESSION[self::CLAVE_CUENTA];
    }

    protected function iniciarSesionDeCuenta(int $cuentaId): void
    {
        // Nuevo identificador de sesión al autenticar (evita fijación de sesión).
        session_regenerate_id(true);
        $_SESSION[self::CLAVE_CUENTA] = $cuentaId;
    }

    protected function cerrarSesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $parametros['path'],
                'domain' => $parametros['domain'],
                'secure' => $parametros['secure'],
                'httponly' => $parametros['httponly'],
                'samesite' => $parametros['samesite'],
            ]);
        }

        session_destroy();
    }

    protected function esPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    // Lee un campo de $_POST siempre como texto (si llega un arreglo, devuelve '').
    // Las contraseñas se piden con $recortar = false para no alterarlas.
    protected function entradaPost(string $campo, bool $recortar = true): string
    {
        $valor = $_POST[$campo] ?? '';

        if (!is_string($valor)) {
            return '';
        }

        return $recortar ? trim($valor) : $valor;
    }

    protected function vista(string $nombre, array $datos = []): void
    {
        $datos['autenticado'] = $this->haySesion();
        $datos['mensaje'] ??= $this->tomarMensaje();

        (new Vista())->render($nombre, $datos);
    }

    protected function redirigir(string $ruta): never
    {
        header('Location: index.php?ruta=' . $ruta);
        exit;
    }

    // Mensaje de una sola vida: se guarda antes de redirigir (patrón
    // POST/Redirect/GET) y se borra al mostrarse, así recargar no lo repite.
    protected function guardarMensaje(string $tipo, string $texto): void
    {
        $_SESSION[self::CLAVE_MENSAJE] = ['tipo' => $tipo, 'texto' => $texto];
    }

    private function tomarMensaje(): ?array
    {
        $mensaje = $_SESSION[self::CLAVE_MENSAJE] ?? null;
        unset($_SESSION[self::CLAVE_MENSAJE]);

        return is_array($mensaje) ? $mensaje : null;
    }
}
