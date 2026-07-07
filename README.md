# Running System 
# Run Mailpit
cd "$env:USERPROFILE\Downloads\mailpit-windows-amd64"
.\mailpit.exe

# Run Local Server 
C:\xampp\php\php.exe -S localhost:8000 -t public

# Access Database 
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -d ehealth_rbac

# List Tables On DB
\dt


# Show Structure of Table 
\d tablename  
eg
\d users (user table )

# Run System 
http://localhost:8000

# Run Mailpit
http://localhost:8025

# Test RBAC
http://localhost:8000/admin/manage_users.php

# Live App
https://ehealth-rbac.onrender.com/



# E-Health Record Management System with RBAC - PostgreSQL

A PHP + PostgreSQL web application for secure e-health record management using Role-Based Access Control.

## Roles
- Admin
- Doctor
- Nurse
- Patient

## Key Features
- Patient immediate registration approval
- Doctor/Nurse pending account verification
- License number and license file upload for doctors/nurses
- Automatic license number check using local `license_registry` table
- Admin manual approval/rejection
- Auto-generated IDs such as `PAT-2026-0001`, `DOC-2026-0001`, `NUR-2026-0001`
- Password hashing
- CAPTCHA after failed or suspicious login attempts
- Email-based two-factor authentication with 6-digit OTP codes
- Role-based dashboards
- Patient assignment to doctor/nurse
- Doctor diagnosis/treatment update
- Nurse vital signs/nursing notes
- Patient can view only own records
- Audit logs
- PostgreSQL database

## Local Setup

1. Create the database:

```sql
CREATE DATABASE ehealth_rbac;
```

2. Install PHP dependencies:

```bash
composer install
```

3. Import the base schema for a fresh install:

```bash
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -d ehealth_rbac -f database/schema.sql
```

4. If you already have an existing database, run the 2FA migration instead:

```bash
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -d ehealth_rbac -f database/update_03_two_factor_codes.sql
```

5. Update database settings in [app/config.php](/C:/Users/Elad%20Liuggy/Downloads/EHealthRBAC/app/config.php):

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'ehealth_rbac');
define('DB_USER', 'postgres');
define('DB_PASS', 'your-postgresql-password');
```

You can also set these as environment variables instead of editing the file directly:

```text
DB_HOST=localhost
DB_PORT=5432
DB_NAME=ehealth_rbac
DB_USER=postgres
DB_PASS=your-postgresql-password
BASE_URL=http://localhost:8000
```

6. For the cleanest local setup, copy [app/config.local.example.php](/C:/Users/Elad%20Liuggy/Desktop/EHealthRBAC/app/config.local.example.php) to `app/config.local.php` and put your local-only values there. That file is already ignored by Git.

Local config loading order is:

- Render or server environment variables first
- `app/config.local.php` second
- built-in defaults last

Example `app/config.local.php`:

```php
<?php
return [
    'BASE_URL' => 'http://localhost:8000',
    'DB_HOST' => 'localhost',
    'DB_PORT' => '5432',
    'DB_NAME' => 'ehealth_rbac',
    'DB_USER' => 'postgres',
    'DB_PASS' => 'your-local-postgresql-password',
    'MAIL_HOST' => '127.0.0.1',
    'MAIL_PORT' => '1025',
    'MAIL_USERNAME' => '',
    'MAIL_PASSWORD' => '',
    'MAIL_ENCRYPTION' => '',
];
```

7. Keep the default local Mailpit values in `app/config.php` or `app/config.local.php` for development:

```php
define('MAIL_FROM_ADDRESS', 'no-reply@ehealth.local');
define('MAIL_FROM_NAME', APP_NAME);
define('MAIL_HOST', '127.0.0.1');
define('MAIL_PORT', 1025);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_ENCRYPTION', '');
```

8. Start Mailpit before testing login:

```bash
cd %USERPROFILE%\Downloads\mailpit-windows-amd64
mailpit.exe
```

9. Start the PHP server from the project root:

```bash
C:\xampp\php\php.exe -S localhost:8000 -t public
```

10. Open the application:

```text
http://localhost:8000
```

## 2FA Email Configuration

- Install PHPMailer with `composer install` or `composer require phpmailer/phpmailer`.
- For local development, use a local mail catcher instead of a real inbox. Mailpit is the safest and easiest option.
- The default `app/config.php` mail settings now target Mailpit on `127.0.0.1:1025` with no SMTP auth.
- For production or real email testing, set the `MAIL_*` environment variables in Render to your SMTP provider.
- Use a real mailbox in `MAIL_FROM_ADDRESS` when you switch to external SMTP.
- If your provider requires SSL on port `465`, set `MAIL_PORT` to `465` and `MAIL_ENCRYPTION` to `ssl`.
- OTP codes expire after 10 minutes.
- Users get 3 verification attempts per login flow.
- Resending a code rotates the previous OTP and enforces a 60-second cooldown.

### Recommended split

- Local machine: Mailpit
- Render live app: real SMTP provider

This keeps local testing fast and reliable while still allowing real OTP delivery on the hosted demo.

### Recommended SMTP provider for Render

For a student demo, a transactional SMTP provider is usually more reliable than Gmail on cloud hosting. A good option is Brevo.

Typical Brevo SMTP values:

```text
MAIL_FROM_ADDRESS=your-verified-sender@example.com
MAIL_FROM_NAME=E-Health RBAC
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-smtp-login
MAIL_PASSWORD=your-brevo-smtp-key
MAIL_ENCRYPTION=tls
```

If you keep using Gmail:

- use an App Password, not your normal Gmail password
- expect stricter rate limits from cloud hosts like Render
- check Spam, Promotions, and delayed delivery when testing repeated OTP sends

## Local 2FA Testing With Mailpit

1. Download Mailpit from the official releases page:

```text
https://github.com/axllent/mailpit/releases/latest
```

2. On Windows, download `mailpit-windows-amd64.zip`, extract it, open the extracted folder, and run:

```bash
mailpit.exe
```

3. Open the Mailpit inbox UI:

```text
http://localhost:8025
```

4. Start the PHP app:

```bash
C:\xampp\php\php.exe -S localhost:8000 -t public
```

5. Log in to the app. The OTP email will appear in Mailpit instead of being sent to the public internet.

6. Copy the 6-digit OTP from Mailpit into `/verify_2fa.php` to complete login.

Mailpit defaults used by this project:

```php
define('MAIL_FROM_ADDRESS', 'no-reply@ehealth.local');
define('MAIL_HOST', '127.0.0.1');
define('MAIL_PORT', 1025);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_ENCRYPTION', '');
```

These mail settings can also be supplied with environment variables:

```text
MAIL_FROM_ADDRESS=no-reply@ehealth.local
MAIL_FROM_NAME=E-Health RBAC
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
```

## Login Test Flow

1. Open the app:

```text
http://localhost:8000/login.php
```

2. Open Mailpit:

```text
http://localhost:8025
```

3. Log in with a valid user account.

4. Confirm you are redirected to `/verify_2fa.php`.

5. Open the newest Mailpit email and copy the 6-digit OTP.

6. Paste the OTP into the verification form.

7. Confirm you are redirected to the correct role dashboard.

8. Optional security checks:
- Enter a wrong OTP 3 times and confirm the session returns to login.
- Click `Resend Code` and confirm a new OTP appears in Mailpit.
- Try using the old OTP after resend and confirm it fails.
- Log out and confirm the pending 2FA session is cleared.

## Quick Start Commands

Start the app:

```bash
cd %USERPROFILE%\Downloads\EHealthRBAC
C:\xampp\php\php.exe -S localhost:8000 -t public
```

Open in browser:

```text
http://localhost:8000
```

Test RBAC admin page:

```text
http://localhost:8000/admin/manage_users.php
```

Connect to PostgreSQL:

```bash
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -d ehealth_rbac
```

Useful PostgreSQL commands:

```sql
\dt
\d table_name
SELECT * FROM table_name;
\q
```

## Default Admin Login

For safety, do not keep a reusable admin password in the repository documentation.

If your local database was seeded with the default admin user, use the configured local admin account only for development and change its password immediately after first login.

The local admin email is:

```text
admin@ehealth.local
```

This local account works with Mailpit for OTP testing because Mailpit captures outgoing mail even when the address is not a real public inbox.

## Production Hosting

This project can be hosted in production on any server or VPS that supports:

- PHP 8+
- PostgreSQL
- Composer dependencies
- A web server such as Apache or Nginx

### Recommended approach

Use a VPS or cloud server where you control:

- the web root
- PHP version
- PostgreSQL connection settings
- writable upload folders
- HTTPS

Examples:

- DigitalOcean
- Hetzner
- Linode
- Contabo
- a cPanel host with PostgreSQL support and custom document root support

If you want the fastest path for this exact repository, use:

- Render with the included `render.yaml`
- Railway with the included `Dockerfile` and `railway.toml`

### Production deployment checklist

1. Upload the project to the server.
2. Set the web/document root to the `public/` folder.
3. Run `composer install --no-dev --optimize-autoloader`.
4. Create the PostgreSQL production database.
5. Import `database/schema.sql`.
6. Set production environment variables for database, base URL, and mail settings.
7. Make sure `public/uploads/licenses/` is writable by the web server.
8. Configure a real SMTP provider for OTP delivery.
9. Enable HTTPS.

Important:

- This project stores uploaded license files on the local filesystem.
- On Render and Railway, local files are not safely persistent across all deploy/restart scenarios unless you attach persistent storage.
- For a real deployment, use a mounted disk/volume or move uploads to object storage later.

### Production environment variables

Set these in your server panel, Apache/Nginx config, or process environment:

```text
BASE_URL=https://your-domain.com

DB_HOST=your-db-host
DB_PORT=5432
DB_NAME=your-db-name
DB_USER=your-db-user
DB_PASS=your-db-password

MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME=E-Health RBAC
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
TWO_FACTOR_ENABLED=true
```

### Important production notes

- Only `public/` should be web-accessible.
- Keep `app/`, `database/`, and other internal folders outside direct public access.
- Do not commit real production passwords to Git.
- Use a real domain in `BASE_URL`.
- Keep Mailpit only for local development.
- Use a real SMTP sender address in production.
- Back up your database before running updates or migrations.
- Keep local Mailpit settings out of Git by using `app/config.local.php`.
- For a demo-only hosted fallback, set `TWO_FACTOR_ENABLED=false` in Render to bypass OTP on the live app while keeping local Mailpit-based 2FA enabled.

### Apache example

Point your virtual host document root to:

```text
/path/to/project/public
```

Make sure `mod_rewrite` and PHP are enabled if your hosting setup requires them.

### Nginx example

Set the site root to:

```text
/path/to/project/public
```

Then forward PHP requests to PHP-FPM.

### Quick production sanity test

After deployment:

1. Open the home page.
2. Open `/login.php`.
3. Log in with a test user.
4. Confirm the OTP email is sent through your production SMTP provider.
5. Confirm uploads work for doctor or nurse registration.
6. Confirm the admin dashboard and protected pages still enforce RBAC correctly.

## Deploy On Render

This repository already includes:

- `Dockerfile`
- `render.yaml`

### Render steps

1. Push this project to GitHub.
2. Sign in to Render.
3. Choose `New` -> `Blueprint`.
4. Select your GitHub repository.
5. Render will detect `render.yaml`.
6. Create the web service and PostgreSQL database.
7. Set these values in Render if prompted:

```text
BASE_URL=https://your-render-domain.onrender.com
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
```

8. After the first deploy, open the PostgreSQL shell or connect externally and import:

```bash
psql "your-render-postgres-connection-string" -f database/schema.sql
```

9. If you want uploads to persist, attach a persistent disk and set:

```text
UPLOAD_DIR=/var/www/html/public/uploads/licenses/
UPLOAD_URL=/uploads/licenses/
```

If you attach a disk at a different path, update `UPLOAD_DIR` to that mounted folder.

### Render note

The included `render.yaml` links the web service to a managed Render PostgreSQL instance automatically.

## Deploy On Railway

This repository already includes:

- `Dockerfile`
- `railway.toml`

### Railway steps

1. Push this project to GitHub.
2. Sign in to Railway.
3. Create a new project.
4. Choose `Deploy from GitHub repo`.
5. Select this repository.
6. Add a PostgreSQL service in the same Railway project.
7. Railway will deploy the app using the included Dockerfile.

### Railway environment variables

Set these in the Railway service variables panel:

```text
BASE_URL=https://your-railway-domain.up.railway.app
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_NAME=${{Postgres.PGDATABASE}}
DB_USER=${{Postgres.PGUSER}}
DB_PASS=${{Postgres.PGPASSWORD}}

MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME=E-Health RBAC
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls

UPLOAD_DIR=/var/www/html/public/uploads/licenses/
UPLOAD_URL=/uploads/licenses/
```

### Railway database import

After Railway creates the PostgreSQL instance, connect to it and import:

```bash
psql "your-railway-postgres-connection-string" -f database/schema.sql
```

### Railway persistent uploads

If you want uploaded license files to survive rebuilds and restarts, attach a Railway volume and point `UPLOAD_DIR` to the mounted folder, for example:

```text
UPLOAD_DIR=/data/licenses/
```

If you do this, make sure the mounted folder exists and is writable by the app.
