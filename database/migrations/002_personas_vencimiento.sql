-- Fase 7: índice para consultas de vencimientos
-- (dashboard: fecha_fin < NOW() AND estatus = 1 ORDER BY fecha_fin).
CREATE INDEX idx_personas_vencimiento
    ON personas (estatus, fecha_fin);
