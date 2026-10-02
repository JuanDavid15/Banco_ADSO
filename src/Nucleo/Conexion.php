<?php

declare(strict_types=1);

namespace App\Nucleo;

use PDO;
use PDOException;

class Conexion
{
    private static ?PDO $conexion = null;

    // Constructor privado: nadie puede hacer "new Conexion()"; la única
    // puerta de entrada es Conexion::obtener() (patrón Singleton).
    private function __construct()
    {
    }

    public static function obtener(): PDO
    {
        if (self::$conexion === null) {
            $configuracion = require __DIR__ . '/../../config/basedatos.php';

            $dsn = "mysql:host={$configuracion['host']};"
                . "port={$configuracion['puerto']};"
                . "dbname={$configuracion['nombre']};"
                . "charset=utf8mb4";

            try {
                self::$conexion = new PDO(
                    $dsn,
                    $configuracion['usuario'],
                    $configuracion['clave']
                );

                self::$conexion->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$conexion->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

                // Consultas preparadas reales: MySQL recibe la consulta y los
                // valores por separado (no se arma texto SQL en PHP).
                self::$conexion->setAttribute(
                    PDO::ATTR_EMULATE_PREPARES,
                    false
                );
            } catch (PDOException $e) {
                throw new PDOException(
                    'No se pudo conectar con la base de datos.'
                );
            }
        }

        return self::$conexion;
    }
}
