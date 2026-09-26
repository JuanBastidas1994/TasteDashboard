-- Fidelizacion: el esquema SIMPLE pasa a ser el esquema por defecto.
-- Las columnas tipo_fidelizacion y meta_puntos ya las usan las APIs (helpers/fidelizacion/logicaSimple.php).
ALTER TABLE tb_empresa_fidelizacion_puntos
    MODIFY COLUMN tipo_fidelizacion ENUM('clasico','simple') NOT NULL DEFAULT 'simple',
    MODIFY COLUMN meta_puntos DECIMAL(10,2) NOT NULL DEFAULT 20.00;

-- La caducidad de puntos, dinero y saldo nunca puede ser menor a 30 dias.
UPDATE tb_empresa_fidelizacion_puntos SET cant_dias_caducidad_puntos = 30 WHERE cant_dias_caducidad_puntos < 30;
UPDATE tb_empresa_fidelizacion_puntos SET cant_dias_caducidad_dinero = 30 WHERE cant_dias_caducidad_dinero < 30;
UPDATE tb_empresa_fidelizacion_puntos SET cant_dias_caducidad_saldo = 30 WHERE cant_dias_caducidad_saldo < 30;
