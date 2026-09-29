-- GMS · Datos ficticios de demostración (Fase 7).
-- Uso: mysql -u root -p profitnessgym < database/seed_demo.sql
-- Credenciales demo: admin 1000000001/demo1234, recepcionista 1000000002/recep1234.

INSERT INTO roles (id, nombre) VALUES
(1, 'Administrador'),
(2, 'Recepcionista'),
(3, 'Dueño')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO usuarios (id, rol_id, nombre, telefono, password, activo) VALUES
(1, 1, 'Demo Admin', '1000000001', '$2y$10$HbGfX6PK9kynQEIjDbuAmObeko.rXzTWMYtB4V5LqKNAu32kaOeN6', 1),
(2, 2, 'Demo Recepcionista', '1000000002', '$2y$10$swNmO3hAbyk4v1cjLCqlPeteuKke82HrLuwQeRKQz9R.Gk1E81r1O', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), password = VALUES(password), activo = 1;

INSERT INTO membresias (id, nombre, descripcion, precio, promocion, precio_promocion, dias, activo) VALUES
(1, 'Semanal Demo', 'Plan semanal de demostración', 200.00, 0, NULL, 7, 1),
(2, 'Mensual Demo', 'Plan mensual de demostración', 450.00, 1, 399.00, 30, 1),
(3, 'Anual Demo', 'Plan anual de demostración', 4000.00, 0, NULL, 365, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO personas (id, nombre, folio, fecha_ini, fecha_fin, estatus, membresia_id) VALUES
(1, 'Ana Demo Uno', 'CLI-A1B2C3', CURDATE() - INTERVAL 40 DAY, CURDATE() - INTERVAL 10 DAY, 1, 2),
(2, 'Beto Demo Dos', 'CLI-D4E5F6', CURDATE() - INTERVAL 5 DAY, CURDATE() + INTERVAL 25 DAY, 1, 2),
(3, 'Carla Demo Tres', 'CLI-789ABC', CURDATE() - INTERVAL 400 DAY, CURDATE() - INTERVAL 35 DAY, 1, 3),
(4, 'David Demo Cuatro', 'CLI-DEF012', CURDATE() - INTERVAL 2 DAY, CURDATE() + INTERVAL 5 DAY, 1, 1),
(5, 'Elena Demo Cinco', 'CLI-345678', CURDATE() - INTERVAL 60 DAY, CURDATE() - INTERVAL 30 DAY, 2, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO productos (id, nombre, descripcion, precio, stock, activo) VALUES
(1, 'Agua Demo', 'Botella de agua 600ml', 20.00, 100, 1),
(2, 'Toalla Demo', 'Renta de toalla', 25.00, 50, 1),
(3, 'Proteína Demo', 'Bote de proteína 1kg', 850.00, 20, 1),
(4, 'Guantes Demo', 'Guantes de entrenamiento', 320.00, 15, 1),
(5, 'Shaker Demo', 'Vaso mezclador', 120.00, 0, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), stock = VALUES(stock);

INSERT INTO configuracion_transferencias (id, banco, clabe, titular) VALUES
(1, 'Banco Demo', '000000000000000001', 'Gimnasio Demo SA de CV')
ON DUPLICATE KEY UPDATE banco = VALUES(banco);

INSERT INTO ventas (usuario_id, tipo, descripcion, referencia_id, total, fecha_venta) VALUES
(1, 'MEMBRESIA', 'Mensual Demo', 2, 399.00, NOW() - INTERVAL 5 DAY),
(1, 'PRODUCTO', 'Venta de Agua Demo x2', 1, 40.00, NOW() - INTERVAL 1 DAY),
(2, 'VISITA', 'Visita de prueba', NULL, 50.00, NOW()),
(1, 'TOALLA', 'Renta de toalla', NULL, 25.00, NOW());
