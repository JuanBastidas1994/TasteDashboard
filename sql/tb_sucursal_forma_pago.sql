-- ============================================================
-- MIGRACIÓN: tb_sucursal_forma_pago
-- Ejecutar en orden. Detener si algún paso falla.
-- ============================================================

-- PASO 1: Crear la nueva tabla
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_sucursal_forma_pago (
    cod_sucursal_forma_pago INT AUTO_INCREMENT PRIMARY KEY,
    cod_sucursal            INT           NOT NULL,
    cod_forma_pago          VARCHAR(50)   NOT NULL,
    monto_maximo            DECIMAL(10,2) DEFAULT 0,
    descripcion             TEXT,
    is_delivery             TINYINT(1)    DEFAULT 1,
    is_pickup               TINYINT(1)    DEFAULT 1,
    estado                  VARCHAR(1)    DEFAULT 'A',
    UNIQUE KEY uq_suc_fp (cod_sucursal, cod_forma_pago)
);

-- PASO 2: Migrar datos existentes a todas las sucursales activas
-- Copia monto_maximo, descripcion, is_delivery, is_pickup desde
-- tb_empresa_forma_pago (nivel empresa) hacia cada sucursal.
-- ON DUPLICATE KEY UPDATE no hace nada si el registro ya existe.
-- ------------------------------------------------------------
INSERT INTO tb_sucursal_forma_pago
    (cod_sucursal, cod_forma_pago, monto_maximo, descripcion, is_delivery, is_pickup, estado)
SELECT
    s.cod_sucursal,
    efp.cod_forma_pago,
    COALESCE(efp.monto_maximo, 0),
    COALESCE(efp.descripcion, ''),
    COALESCE(efp.is_delivery, 1),
    COALESCE(efp.is_pickup, 1),
    'A'
FROM tb_sucursales s
INNER JOIN tb_empresa_forma_pago efp
    ON efp.cod_empresa = s.cod_empresa
    AND efp.estado != 'D'
WHERE s.estado IN ('A', 'I')
ON DUPLICATE KEY UPDATE
    cod_sucursal_forma_pago = cod_sucursal_forma_pago;  -- no-op, solo evita error de duplicado

-- PASO 3: Verificar resultado antes de borrar columnas
-- (opcional pero recomendado: asegúrate de que el conteo es correcto)
-- SELECT COUNT(*) as total_registros FROM tb_sucursal_forma_pago;

-- PASO 4: Limpiar columnas que ya no se usan en tb_empresa_forma_pago
-- IMPORTANTE: ejecutar DESPUÉS del paso 2 y DESPUÉS de verificar.
-- Si tu MySQL es < 8.0 y alguna columna no existe, comenta las líneas individuales.
-- ------------------------------------------------------------
ALTER TABLE tb_empresa_forma_pago
    DROP COLUMN monto_maximo,
    DROP COLUMN descripcion,
    DROP COLUMN posicion,
    DROP COLUMN is_delivery,
    DROP COLUMN is_pickup;
