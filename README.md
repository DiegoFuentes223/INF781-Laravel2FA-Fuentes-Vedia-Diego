# INF781-Laravel2FA

Sistema de autenticación de dos factores (2FA) basado en TOTP (RFC 6238) implementado sobre Laravel 13 con base de datos PostgreSQL. El proyecto integra Google Authenticator mediante la librería `pragmarx/google2fa-laravel` y generación de códigos QR con `bacon/bacon-qr-code`, incluyendo códigos de respaldo (backup codes) hasheados con bcrypt como mecanismo alternativo de acceso.

Desarrollado como práctica de laboratorio para la asignatura **INF781 — Seguridad de Software** de la carrera de Ingeniería Informática, Universidad Autónoma Tomás Frías — Potosí, Bolivia.

---

## Características

- Registro e inicio de sesión con Laravel Breeze (stack Blade + Tailwind CSS)
- Activación y desactivación de 2FA por el propio usuario
- Generación de secreto TOTP y código QR en SVG
- Middleware personalizado que intercepta el acceso hasta completar la verificación OTP
- 8 códigos de respaldo hasheados con bcrypt generados al activar el 2FA
- Flujo alternativo de login mediante código de respaldo (un uso por código)
- Sesiones persistidas en PostgreSQL (`SESSION_DRIVER=database`)

---

## Stack tecnológico

| Componente | Versión |
|------------|---------|
| PHP | ^8.3 |
| Laravel Framework | ^13.0 |
| Laravel Breeze | ^2.4 |
| Laravel Tinker | ^3.0 |
| pragmarx/google2fa-laravel | 3.0 |
| bacon/bacon-qr-code | 3.1 |
| fakerphp/faker | ^1.23 |
| laravel/pail | ^1.2.5 |
| laravel/pint | ^1.27 |
| mockery/mockery | ^1.6 |
| nunomaduro/collision | ^8.6 |
| phpunit/phpunit | ^12.5.12 |

---

## Requisitos previos

- PHP 8.3 o superior con extensiones: `pdo_pgsql`, `mbstring`, `xml`, `gd`
- Composer 2.x
- Node.js 20.x LTS y NPM 10.x
- PostgreSQL 15 o superior
- Git 2.x
- Una app TOTP en tu smartphone: Google Authenticator, Authy, Microsoft Authenticator o FreeOTP

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/DiegoFuentes223/INF781-Laravel2FA-Fuentes-Vedia-Diego.git
cd INF781-Laravel2FA-Fuentes-Vedia-Diego
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias JavaScript

```bash
npm install
npm run build
```

### 4. Crear la base de datos PostgreSQL

```sql
psql -U postgres

CREATE USER laravel_2fa_user WITH PASSWORD 'secret2fa';
CREATE DATABASE laravel_2fa OWNER laravel_2fa_user;
GRANT ALL PRIVILEGES ON DATABASE laravel_2fa TO laravel_2fa_user;
\q
```

### 5. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con los datos de tu base de datos:

```env
APP_NAME="INF781 2FA"
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_2fa
DB_USERNAME=laravel_2fa_user
DB_PASSWORD=secret2fa

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

### 6. Ejecutar migraciones

```bash
php artisan migrate
```

### 7. Levantar el servidor de desarrollo

```bash
php artisan serve
```

Abre `http://localhost:8000` en el navegador.

---

## Uso

### Registro y login base
1. Abre `http://localhost:8000`
2. Haz clic en **Register** y crea una cuenta
3. Inicia sesión con email y contraseña

### Activar 2FA
1. En el dashboard haz clic en **Configurar Autenticación en Dos Factores**
2. Escanea el código QR con tu app autenticadora
3. Ingresa el código de 6 dígitos y presiona **Activar 2FA**
4. **Guarda los 8 códigos de respaldo** que aparecen — no se vuelven a mostrar

### Login con 2FA activo
1. Inicia sesión con email y contraseña
2. Ingresa el código OTP de tu app autenticadora
3. Accedes al dashboard

### Login con código de respaldo
1. En la pantalla de verificación OTP haz clic en **¿Perdiste tu dispositivo?**
2. Ingresa uno de tus códigos de respaldo en formato `XXXX-XXXX`
3. Cada código solo puede usarse una vez

---

## Estructura del proyecto

```
INF781-Laravel2FA/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TwoFactorController.php
│   │   │   ├── TwoFactorVerifyController.php
│   │   │   └── TwoFactorBackupController.php
│   │   └── Middleware/
│   │       └── TwoFactorMiddleware.php
│   └── Models/
│       ├── User.php
│       └── TwoFactorBackupCode.php
├── bootstrap/
│   └── app.php
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── YYYY_MM_DD_add_two_factor_to_users_table.php
│       └── YYYY_MM_DD_create_two_factor_backup_codes_table.php
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       └── two-factor/
│           ├── setup.blade.php
│           ├── verify.blade.php
│           └── backup.blade.php
├── routes/
│   └── web.php
├── .env.example
├── composer.json
└── README.md
```

---

## Flujo de autenticación

```
Login (email + password)
        ↓
¿two_factor_enabled = true?
        ↓ SÍ
/two-factor/verify
        ↓
¿Tienes tu dispositivo?
   ↓ SÍ              ↓ NO
Código OTP      Código de respaldo
   ↓                  ↓
session two_factor_verified = true
        ↓
    /dashboard
```
