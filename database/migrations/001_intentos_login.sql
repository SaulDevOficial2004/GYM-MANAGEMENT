-- Fase 3: Rate limiting persistente
CREATE TABLE intentos_login (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  identificador VARCHAR(50) NOT NULL,
  ip VARBINARY(16) NOT NULL,
  exitoso TINYINT(1) NOT NULL DEFAULT 0,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_id_fecha (identificador, creado_en),
  KEY idx_ip_fecha (ip, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
