USE db_banco_adso;

SELECT * FROM usuarios;
SELECT * FROM transferencias;
SELECT * FROM clientes;
SELECT * FROM cuentas;
SELECT * FROM retiros; -- Retiros y transferencias permanecen vacias. 
-- (esto es para hacer los movimientos en el proyecto...SI DEJARA DE SALTAR ERRORES!!!)

-- 1. primero se insertan los usuarios en una tabla sin llaves foraneas
-- o sino te dara yupi en el cmd o en el Wordbench
INSERT INTO clientes (id, documento, nombre) VALUES
(1, '10203040', 'Carlos Alberto Mendoza'),
(2, '20304050', 'Ana María Rodríguez'),
(3, '30405060', 'Juan Fernando Gómez'),
(4, '40506070', 'Luisa Fernanda Restrepo'),
(5, '50607080', 'Diego Alejandro Marín');

-- 2. Ahora insertados los clientes, ahora podemos poner las cuentas
INSERT INTO cuentas (numero_cuenta, cliente_id, saldo) VALUES
('AHO-992341', 1, 1500000.00), -- Cuenta de Carlos
('AHO-112345', 2, 4250000.50), -- Cuenta de Ana
('COR-778844', 3, 300000.00),  -- Cuenta de Juan
('AHO-556611', 4, 8900000.00), -- Cuenta de Luisa
('COR-223344', 5, 0.00);       -- Cuenta de Diego (Saldo en 0)

-- 3. Y por ultimo pero no menos importante, metemos los usuarios a la plataforma
-- para lo del login (tuve que crear los hashes de algunas de mis cuentas)
INSERT INTO usuarios (cuenta_id, usuario, clave_hash) VALUES
(1, 'carlos.mendoza', '$2y$10$VSTXal3IPII7KRWMxa8TI.ji3guuA8FRHpM4wzsOyEDeTRyr/bLzW'),
(2, 'ana.rodriguez', '$2y$10$g1rRX/UowyvXQoqnu0Ha0e.9gHXs7zcZ2xfwZMpnybdkw7TxyO3ta'),
(3, 'juan.gomez99', '$2y$10$FnVMGYZA1pQTYNbOOKmLAeneVnw.tnen4EUt65Crjj375e6AxKPNu'),
(4, 'luisa.restrepo', '$2y$10$Xvgifp.NCPAIIBFPgU5RCemSiIb3.hz2WOL6KBR75B17k59n/q2rC'),
(5, 'diego.marin', '$2y$10$5l2aSHltm1gsHA7e4rrSSuxkCJi.spktpdU5sVRl8Rrye2hvyg7DS');
-- Contraseñas actualizadas a bcrypt $2*$ para adaptarlo a los requerimientos, 
-- este sembrado de datos fue hecho por y para humanos ^o^
-- (Aun siguen siendo las mismas contraseñas de algunas de mis cuentas)

-- 4. Algunas verificaciones para asegurar el correcto sembrado de datos

-- Verificacion: las 5 filas deben mostrar un clave_hash de 60 caracteres.
SELECT usuario, LENGTH(clave_hash) AS largo_hash, LEFT(clave_hash, 7) AS inicio FROM usuarios;