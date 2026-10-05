# Base de datos: migraciones y seeders

Runner: [`bin/migrate.php`](../bin/migrate.php) (PHP 7.3+, sin Composer, lee la conexión del `.env`).

```
database/
  schema/schema.sql      Estructura completa (192 tablas + vistas). GENERADO, no editar a mano.
  migrations/*.sql       Un cambio por archivo, en orden por nombre (AAAA_MM_DD_NNNNNN_nombre.sql).
  seeders/*.sql          Catálogos del sistema (roles, estados, formas de pago, couriers, menú...).
  seeders/dev/*.sql      Solo desarrollo (usuario admin / admin123).
```

La tabla `schema_migrations` registra qué migraciones ya corrieron en cada BD.

## Comandos

| Comando | Qué hace |
|---|---|
| `php bin/migrate.php` | Corre las migraciones pendientes. Si la BD está vacía, primero carga `schema.sql`. |
| `php bin/migrate.php --pretend` | Muestra lo que correría, sin ejecutar. |
| `php bin/migrate.php status` | Lista ejecutadas `[x]` y pendientes `[ ]`; avisa si un archivo cambió después de correr. |
| `php bin/migrate.php make nombre_del_cambio` | Crea el archivo de migración con la fecha y secuencia correctas. |
| `php bin/migrate.php seed [--dev] [archivo]` | Carga los seeders (idempotentes, `INSERT IGNORE`). |
| `php bin/migrate.php fresh --seed --dev` | Borra todo y recrea la BD. Bloqueado con `APP_ENV=production`. |
| `php bin/migrate.php baseline [--until=archivo]` | Marca migraciones como ejecutadas sin correrlas (BDs con cambios aplicados a mano). |
| `php bin/migrate.php schema:dump` | Regenera `schema.sql` desde la BD actual. |
| `php bin/migrate.php seed:export archivo.sql tabla [--where="..."] [--blank=col1,col2]` | Agrega filas de una tabla a un seeder. |

## Dev nuevo: BD desde cero

1. Crear una BD vacía (utf8mb4) y apuntar `DB_DATABASE` en `.env`, con `APP_ENV=local`.
2. `php bin/migrate.php fresh --seed --dev` (o, si la BD ya está vacía: `php bin/migrate.php` y luego `php bin/migrate.php seed --dev`).
3. Entrar al dashboard con `admin` / `admin123`.

## Flujo para un cambio de BD

1. `php bin/migrate.php make add_columna_x_tb_productos`
2. Escribir el SQL en el archivo creado. Un cambio por archivo: en MySQL los `ALTER`/`CREATE` hacen commit implícito y no hay rollback.
3. `php bin/migrate.php` en local.
4. `php bin/migrate.php schema:dump` y commitear la migración **y** `schema.sql` juntos.
5. En el deploy (Forge) se corre `php bin/migrate.php`, que aplica solo lo pendiente.

## Primera vez en una BD existente (producción, staging, copias locales)

Esa BD ya tiene tablas pero no `schema_migrations`, así que el runner se niega a migrar hasta hacer el baseline:

- Si **todas** las migraciones de `database/migrations/` ya están aplicadas: `php bin/migrate.php baseline`
- Si solo hasta cierta migración: `php bin/migrate.php baseline --until=2026_09_25_000001_fidelizacion_esquema_simple_default.sql` y luego `php bin/migrate.php` corre el resto.

Siempre revisar primero con `status` y `--pretend`.
