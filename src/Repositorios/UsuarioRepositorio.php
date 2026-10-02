<?php

declare(strict_types=1);

namespace App\Repositorios;

use App\Modelos\Usuario;
use App\Nucleo\Conexion;
use PDO;

class UsuarioRepositorio
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    // Registro de acceso (clave_hash) asociado a una cuenta: lo usa el login
    // y la reconfirmación de contraseña.
    public function obtenerPorCuentaId(int $cuentaId): ?Usuario
    {
        $sql = "SELECT id, cuenta_id, usuario, clave_hash
                FROM usuarios
                WHERE cuenta_id = :cuenta_id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':cuenta_id' => $cuentaId
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ? $this->hidratar($fila) : null;
    }

    private function hidratar(array $fila): Usuario
    {
        return new Usuario(
            (int) $fila['id'],
            (int) $fila['cuenta_id'],
            (string) $fila['usuario'],
            (string) $fila['clave_hash']
        );
    }
}
