# Gym Management System (GMS)

Complete Gym Management System built with PHP, MySQL, JavaScript and Bootstrap.

---

## Overview

GMS is a web-based management system designed to streamline the daily operations of a gym.

The application centralizes client management, memberships, visitors, inventory, sales, financial reports and transfer payment validation within a single administrative platform.

The project was developed from scratch with a modular architecture using PHP and MySQL on the backend and JavaScript with Bootstrap on the frontend, and later went through a full security hardening pass: CSRF protection, persistent rate limiting, signed public links, file storage isolation, transactional integrity with fail-closed audit logging, security headers/CSP, and an automated test suite.

Current status: Hardened, tested, ready for production deployment.

---

## Features

### Authentication

- User authentication with bcrypt password hashing
- Session management with idle/absolute timeout and periodic session ID rotation
- Persistent, database-backed rate limiting on login (per phone and per IP)
- Role-based access control (Administrador, Dueño, Recepcionista)

### Client Management

- Register clients
- Update client information
- Disable clients
- Search clients
- Membership history

### Membership Management

- Create memberships
- Update memberships
- Automatic membership renewal
- Automatic expiration calculation
- Membership history

### Visitor Management

- Register visitors
- Visitor history

### Coaches Management

- Register coaches
- Update coaches
- Enable and disable coaches

### Products

- Product management
- Inventory control with row-level locking on sale (`SELECT ... FOR UPDATE`)
- Stock management
- Product sales

### Sales

- Membership sales
- Product sales
- Visitor payments
- Towel rentals

### Reports

- Daily reports
- Monthly reports
- Annual reports
- Sales history

### Transfer Payment Module

- Public client portal, accessed only via time-limited HMAC-signed links (no bare folio lookup)
- Payment receipt upload, rate-limited per IP and per client
- Payment receipt validation
- Payment approval / rejection
- Payment history
- Bank account configuration

---

## Security

This project went through a full hardening pass. Summary of what's in place:

- **CSRF protection** on every mutating endpoint, validated server-side against the session token (`X-CSRF-Token` header, read fresh on every request rather than cached).
- **HTTP method enforcement** — all mutating API endpoints reject anything other than `POST` (405).
- **Persistent rate limiting** (database-backed, not session-based) on login and on the public client portal, so it can't be bypassed by dropping cookies.
- **Session hardening** — `HttpOnly`, `Secure`, `SameSite=Strict` cookies, idle/absolute timeout, periodic ID rotation, and full server-side invalidation on logout.
- **Least-privilege database access** — the app connects with a dedicated MySQL user (`gms_app`) limited to `SELECT/INSERT/UPDATE/DELETE`, never `root`.
- **Signed public links** — the client transfer portal is reachable only through HMAC-signed, time-limited URLs; folios are never enough on their own.
- **File storage isolated from the webroot** — uploaded receipts live outside the document root and are served only through an authorization-checked endpoint with path-traversal protection. The app refuses to start if storage is misconfigured to fall inside the webroot.
- **Transactional integrity with fail-closed audit logging** — every state-changing operation (sales, memberships, receipts, staff management) runs inside a database transaction; if the audit log write fails, the whole operation rolls back rather than completing untracked.
- **Security headers & CSP** — `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, `Strict-Transport-Security`, and a Content-Security-Policy restricting scripts/styles to a known set of hosts; no inline event handlers anywhere in the codebase.
- **Subresource Integrity (SRI)** on all third-party CDN assets.
- **No secrets or real data in the repository** — the committed SQL dump is schema-only; real data never leaves the production server.

---

## Testing & Quality

- **PHPUnit** — unit and feature tests, including a parameterized role-authorization matrix covering every API endpoint.
- **PHPStan** — static analysis.
- **PHP-CS-Fixer** — PSR-12 style enforcement.
- **CI** — GitHub Actions runs the full suite (PHPStan, CS-Fixer, PHPUnit) on every push.

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse
vendor/bin/php-cs-fixer fix --dry-run --diff
```

---

## Technologies

### Backend

- PHP 8
- MySQL / MariaDB
- MySQLi with prepared statements and transactions
- PSR-4 autoloading (Composer), repository layer for data access
- REST-style APIs
- PHP Sessions

### Frontend

- HTML5
- CSS3
- Bootstrap
- JavaScript (ES6+)
- Fetch API (wrapped with CSRF-aware `apiFetch`)
- AJAX

### Libraries

- SweetAlert2
- Toastify.js
- Font Awesome

### Database

- MySQL / MariaDB
- Foreign Keys
- Prepared Statements
- Transactions with row-level locking
- INNER JOIN
- LEFT JOIN
- Indexed lookups for membership expiration queries

### Infrastructure

- Nginx + PHP-FPM
- Security headers & Content-Security-Policy
- Self-hosted (Termux / Android), Cloudflare Tunnel

### Version Control

- Git
- GitHub

---

## System Architecture

```
Browser
        │
        ▼
HTML + CSS + JavaScript (CSP-compliant, no inline handlers)
        │
    Fetch API (apiFetch, CSRF token attached)
        │
        ▼
Nginx (security headers, CSP, denied sensitive paths)
        │
        ▼
PHP APIs (role-guarded, method-enforced, rate-limited)
        │
      MySQLi (prepared statements, transactions)
        │
        ▼
MySQL / MariaDB
```

---

## Screenshots

### Login

![](screenshots/01-login.png)

---

### Dashboard

![](screenshots/02-dashboard-overview.png)

![](screenshots/03-dashboard-product-sale.png)

![](screenshots/04-dashboard-towel-rental.png)

![](screenshots/05-dashboard-new-visit.png)

![](screenshots/06-dashboard-bank-settings.png)

![](screenshots/07-dashboard-reports.png)

![](screenshots/08-dashboard-membership-renewal.png)

![](screenshots/09-dashboard-disable-member.png)

---

### Clients

![](screenshots/10-persons.png)

![](screenshots/11-persons-create.png)

---

### Visitors

![](screenshots/12-visitors.png)

---

### Inactive Members

![](screenshots/13-inactive-members.png)

---

### Coaches

![](screenshots/14-coaches.png)

![](screenshots/15-coaches-create.png)

---

### Memberships

![](screenshots/16-memberships.png)

![](screenshots/17-memberships-create.png)

---

### Products

![](screenshots/18-products.png)

![](screenshots/19-products-create.png)

---

### Reports

![](screenshots/20-reports.png)

![](screenshots/21-reports-history.png)

---

### Client Transfer Portal

![](screenshots/22-clients-transfer-portal.png)

---

### Upload Payment Receipt

![](screenshots/23-upload-payment-receipt.png)

---

### Payment Review

![](screenshots/24-payment-review.png)

![](screenshots/25-payment-review-modal.png)

---

### Client Payment History

![](screenshots/26-dashboard-recept-client.png)

![](screenshots/27-payment-history.png)

---

## Project Structure

```
api/            Role-guarded, method-enforced, rate-limited JSON endpoints
config/         Environment-based configuration (not committed)
css/
database/       Schema-only dump, migrations, and a fictional demo seed
deploy/         Nginx config and post-deploy verification checklist
docs/
img/
includes/       Auth, CSRF, rate limiting, signed links, bootstrap
js/             Frontend, CSRF-aware fetch wrapper
php_action/
screenshots/
src/            PSR-4 repository layer
tests/          PHPUnit unit and feature tests (incl. role matrix)
uploads/        Legacy path only — uploads now live outside the webroot
```

---

## Main API Endpoints

- Login (rate-limited, generic error messages)
- Create Client
- Update Membership
- Register Visitor
- Product Sales (transactional, row-locked stock)
- Upload Payment Receipt (signed link + rate-limited)
- Confirm Payment Receipt (transactional)
- Reject Payment Receipt
- Dashboard
- Reports

---

## Installation

Clone the repository.

```bash
git clone https://github.com/SaulDevOficial2004/GYM-MANAGEMENT.git
```

1. Install dependencies:

```bash
composer install
```

2. Create the database and a least-privilege application user, then
   import ONLY the schema (no real data):

```bash
mysql -u root -p -e "CREATE DATABASE profitnessgym CHARACTER SET utf8mb4;"
mysql -u root -p -e "CREATE USER 'gms_app'@'localhost' IDENTIFIED BY 'your-strong-password';"
mysql -u root -p -e "GRANT SELECT, INSERT, UPDATE, DELETE ON profitnessgym.* TO 'gms_app'@'localhost';"
mysql -u gms_app -p profitnessgym < database/profitnessgym.sql
mysql -u gms_app -p profitnessgym < database/migrations/001_intentos_login.sql
mysql -u gms_app -p profitnessgym < database/migrations/002_personas_vencimiento.sql
```

> Windows: run the imports from `cmd.exe`, NOT PowerShell, e.g.
> `cmd /c "mysql -u gms_app -p profitnessgym < database/profitnessgym.sql"`.
> Piping with `Get-Content ... | mysql` in PowerShell recodes the file
> and corrupts non-ASCII text (e.g. `Dueño` arrives as `Due??o`).

3. Optional: load fictional demo data (2 users, 5 people, 3 memberships,
   5 products, 4 sales):

```bash
mysql -u gms_app -p profitnessgym < database/seed_demo.sql
```

Demo credentials: admin `1000000001` / `demo1234`,
receptionist `1000000002` / `recep1234`.

4. Copy `.env.example` to `.env` and set your credentials:

```bash
cp .env.example .env
```

```env
DB_HOST=localhost
DB_USER=gms_app
DB_PASS=your-strong-password
DB_NAME=profitnessgym
APP_ENV=local
APP_KEY=
STORAGE_PATH=../gms-storage
TURNSTILE_SECRET=
TURNSTILE_SITEKEY=
APP_URL=
```

> `STORAGE_PATH` must resolve OUTSIDE the document root (absolute path
> recommended). The app aborts startup with a log error if it falls
> inside the webroot — this is enforced, not just documented.
>
> `APP_URL` should be left empty in local/dev (the app falls back to the
> request's own host) and set explicitly in production, so signed
> client-portal links are never generated against the wrong domain.

5. Create the storage directory outside the document root:

```bash
mkdir -p ../gms-storage/comprobantes ../gms-storage/coaches ../gms-storage/logs
```

6. If you are migrating an existing install, move the uploaded files
   (the script is idempotent and never touches the database):

```bash
php bin/migrar_uploads.php
```

7. Configure Nginx + PHP-FPM using `deploy/nginx-gms.conf` (security
   headers, CSP, and denied paths are already defined there), then
   verify the deployment:

```bash
nginx -t
nginx -s reload
bash deploy/checklist-post-deploy.sh https://your-domain
```

8. Run the test suite to confirm everything is green before going live:

```bash
vendor/bin/phpunit
```

---

## Current Status

Completed modules:

- Authentication
- Dashboard
- Client Management
- Membership Management
- Visitor Management
- Coaches Management
- Product Management
- Inventory
- Sales
- Reports
- Transfer Payments
- Payment Validation
- Administrative Configuration

Security hardening completed:

- CSRF protection, method enforcement, persistent rate limiting
- Session hardening and least-privilege database access
- Signed public portal links, isolated file storage
- Transactional integrity with fail-closed audit logging
- Security headers, CSP, SRI, no secrets/real data in the repository
- Automated test suite (PHPUnit, PHPStan, PHP-CS-Fixer) with CI

Project status:

Ready for production deployment.

---

## Author

Saúl de Jesús San Martín Martínez

Software Developer

GitHub

https://github.com/SaulDevOficial2004
