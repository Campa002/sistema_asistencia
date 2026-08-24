# Despliegue en servidor — Sistema de Gestión de Asistencia (EEST N°1)

## 1. Requisitos reales del servidor

Confirmados por auditoría directa del código (nada inventado):

- **PHP** ≥ 8.0 (desarrollado y probado con 8.0.30). Sin tipado estricto incompatible con 8.1+, pero no probado en 8.1+.
- **Extensiones PHP requeridas**:
  - `pdo_mysql` — toda la app (`config/database.php`).
  - `mbstring` — usado en varios controllers (`mb_strtoupper`, `mb_substr`, iniciales de avatares, etc.).
  - `curl` — `models/EmailService.php` (envío de emails transaccionales vía API de Resend).
  - `gd` — usado por Dompdf (`vendor/dompdf/dompdf/src/Adapter/GD.php`) para procesar imágenes dentro de los PDF de Admin → Reportes. **Debe estar habilitada** en el `php.ini` del servidor (`extension=gd`).
  - `json`, `session`, `openssl` — estándar, normalmente ya vienen habilitadas.
- **MySQL / MariaDB**: el dump actual fue generado desde MariaDB 10.4.32. Cualquier MySQL 5.7+/8+ o MariaDB 10.4+ es compatible con el esquema (InnoDB, utf8mb4, sin features exóticas).
- **Composer**: **NO es necesario ejecutar `composer install` en el servidor** — `vendor/` (Dompdf y dependencias, ~13 MB) ya está commiteado en el repositorio. Si en algún momento se decide sacar `vendor/` del control de versiones, ahí sí pasaría a ser un requisito real del deploy.
- **mod_rewrite**: **NO se usa** — el ruteo es 100% por query string (`index.php?page=...`), no hay ninguna regla de reescritura de URL en el proyecto. No hace falta habilitarlo.
- **mod_mime**: sí lo usa `public/.htaccess` (agregado en esta tarea) para que `manifest.webmanifest` se sirva con el Content-Type correcto (`application/manifest+json`), requisito real de las PWA. Viene habilitado por defecto en Apache casi siempre.
- **Permisos de carpetas** (escritura para el usuario del servidor web):
  - `backups/` (raíz del proyecto) — Admin → Gestión Técnica escribe backups ahí (`AdminGestionTecnicaController.php`).
  - `vendor/dompdf/dompdf/lib/fonts/` — Dompdf cachea métricas de fuentes ahí la primera vez que genera un PDF.

## 2. Configurar base URL, DB y entorno sin tocar código

- **Base URL**: ya es 100% dinámica (`config/constants.php` arma `BASE_URL` a partir de `$_SERVER['HTTP_HOST']` y la carpeta real del script). No requiere ningún cambio para mudarse de dominio/carpeta.
- **Credenciales de base de datos** (cambio hecho en esta tarea, `config/database.php`): se resuelven en este orden —
  1. Variables de entorno `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET` (recomendado en el servidor real; se definen con `SetEnv` en el VirtualHost de Apache, o en el panel del hosting).
  2. `config/database.local.php` (no versionado — copiar desde `config/database.example.php`), solo si no se pueden usar variables de entorno.
  3. Si no hay ninguna de las dos, usa los valores actuales de XAMPP local (`root` sin contraseña) — así el entorno local sigue funcionando exactamente igual que antes.
- **Email transaccional** (ya existía, sin cambios): mismo patrón con `RESEND_API_KEY` vía variable de entorno o `config/email.local.php` (ver `config/email.example.php`).

## 3. Procedimiento para crear la base en un servidor nuevo

```sql
CREATE DATABASE eest_asistencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'eest_user'@'localhost' IDENTIFIED BY 'CONTRASEÑA_REAL_AQUI';
GRANT ALL PRIVILEGES ON eest_asistencia.* TO 'eest_user'@'localhost';
FLUSH PRIVILEGES;
```

Importar el dump (por línea de comandos o phpMyAdmin → Importar):

```bash
mysql -u eest_user -p eest_asistencia < sql/eest_asistencia.sql
```

Luego configurar `DB_HOST`/`DB_NAME`/`DB_USER`/`DB_PASS` como se indica en la sección 2, y verificar la conexión abriendo `index.php?page=login` (si hay error de conexión, PDO lo tira como excepción visible — confirma credenciales/host).

## 4. Estado real del dump SQL (`sql/eest_asistencia.sql`)

Verificado en esta tarea comparando columna por columna contra la base viva real:

- **Esquema**: coincide exactamente con la base de datos real (28/28 tablas, mismas columnas, mismos tipos, mismas claves/FKs — las únicas diferencias detectadas eran de formato de dump entre `mysqldump` y phpMyAdmin, no diferencias reales). **No requirió ningún ajuste.**
- **Datos**: el dump es un volcado completo (estructura + datos reales), generado el 21/08/2026 — desactualizado ~3 días respecto a hoy. No se regeneró automáticamente en esta tarea porque decidir si el dump del repositorio debe llevar el dataset completo actual o pasar a ser solo esquema + datos mínimos de arranque es una decisión de alcance/privacidad que le corresponde al usuario, no algo para asumir. Si se pide, se puede regenerar con:
  ```bash
  "C:\xampp\mysql\bin\mysqldump.exe" -u root eest_asistencia > sql/eest_asistencia.sql
  ```
- Existe también `sql/eest_asistencia_viejo.sql` (231 KB) que parece un dump obsoleto de una versión anterior del esquema. No se tocó ni se borró (no fue pedido), pero antes de subir el proyecto a un repositorio/servidor conviene revisar si todavía hace falta — puede generar confusión sobre cuál dump usar para un setup nuevo.

## 5. Cosas que NO deben subirse al servidor / repositorio público

- `config/email.local.php` y `config/database.local.php` (si se crean) — ya protegidos por `.gitignore` (creado en esta tarea; antes no existía ningún `.gitignore` en el proyecto).
- `backups/` (contenido generado, no código).
- Cualquier script de prueba (`test_*.php`) — se verificó que no queda ninguno en el repositorio; los usados durante esta tarea y tareas previas se escribieron siempre fuera del proyecto, en una carpeta de scratchpad temporal.

## 6. Limitación conocida, fuera de alcance de esta tarea

`controllers/AdminGestionTecnicaController.php` (backup de BD desde Admin → Gestión Técnica) tiene hardcodeada la ruta de Windows `C:\xampp\mysql\bin\mysqldump.exe` para invocar `mysqldump` vía `proc_open()`. En un servidor Linux esa ruta no existe y la función de backup fallaría. **No se modificó** porque es un controller de Admin (`Admin*Controller.php`), explícitamente fuera de alcance de esta tarea ("Admin ya cerrado, no tocar"). Se deja documentado para que el usuario decida cuándo abordarlo.
