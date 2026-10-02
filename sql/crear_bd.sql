CREATE DATABASE IF NOT EXISTS db_banco_adso
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE db_banco_adso;

CREATE TABLE clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE cuentas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_cuenta VARCHAR(20) NOT NULL UNIQUE,
    cliente_id INT UNSIGNED NOT NULL,
    saldo DECIMAL(12,2) NOT NULL DEFAULT 0.00,

    CONSTRAINT fk_cuentas_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave_hash VARCHAR(255) NOT NULL,

    CONSTRAINT fk_usuarios_cuenta
        FOREIGN KEY (cuenta_id)
        REFERENCES cuentas(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE retiros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_retiros_cuenta
        FOREIGN KEY (cuenta_id)
        REFERENCES cuentas(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE transferencias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_origen_id INT UNSIGNED NOT NULL,
    cuenta_destino_id INT UNSIGNED NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transferencias_origen
        FOREIGN KEY (cuenta_origen_id)
        REFERENCES cuentas(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_transferencias_destino
        FOREIGN KEY (cuenta_destino_id)
        REFERENCES cuentas(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);