# ProFitnessGym v1

Complete Gym Management System built with PHP, MySQL, JavaScript and Bootstrap.

---

## Overview

ProFitnessGym is a web-based management system designed to streamline the daily operations of a gym.

The application centralizes client management, memberships, visitors, inventory, sales, financial reports and transfer payment validation within a single administrative platform.

The project was developed from scratch with a modular architecture using PHP and MySQL on the backend and JavaScript with Bootstrap on the frontend.

Current status: Final testing before production deployment.

---

## Features

### Authentication

- User authentication
- Session management
- Role-based access control

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
- Inventory control
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

- Client transfer portal
- Payment receipt upload
- Payment receipt validation
- Payment approval
- Payment rejection
- Payment history
- Bank account configuration

---

## Technologies

### Backend

- PHP 8
- MySQL
- MySQLi
- REST-style APIs
- PHP Sessions

### Frontend

- HTML5
- CSS3
- Bootstrap
- JavaScript (ES6+)
- Fetch API
- AJAX

### Libraries

- SweetAlert2
- Toastify.js
- Font Awesome

### Database

- MySQL
- Foreign Keys
- Prepared Statements
- Transactions
- INNER JOIN
- LEFT JOIN

### Version Control

- Git
- GitHub

---

## System Architecture

```
Browser
        │
        ▼
HTML + CSS + JavaScript
        │
    Fetch API
        │
        ▼
PHP APIs
        │
      MySQLi
        │
        ▼
MySQL Database
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
api/
css/
database/
docs/
img/
includes/
js/
php_action/
screenshots/
uploads/
```

---

## Main API Endpoints

- Login
- Create Client
- Update Membership
- Register Visitor
- Product Sales
- Upload Payment Receipt
- Confirm Payment Receipt
- Reject Payment Receipt
- Dashboard
- Reports

---

## Installation

Clone the repository.

```bash
git clone https://github.com/SaulDevOficial2004/ProFitnessGym-v1.git
```

1. Create the database and import ONLY the schema (no real data):

```bash
mysql -u root -p -e "CREATE DATABASE profitnessgym CHARACTER SET utf8mb4;"
mysql -u root -p profitnessgym < database/profitnessgym.sql
```

> Windows: run the imports from `cmd.exe`, NOT PowerShell, e.g.
> `cmd /c "mysql -u root -p profitnessgym < database/profitnessgym.sql"`.
> Piping with `Get-Content ... | mysql` in PowerShell recodes the file
> and corrupts non-ASCII text (e.g. `Dueño` arrives as `Due??o`).

2. Optional: load fictional demo data (2 users, 5 people, 3 memberships,
   5 products, 4 sales):

```bash
mysql -u root -p profitnessgym < database/seed_demo.sql
```

Demo credentials: admin `1000000001` / `demo1234`,
receptionist `1000000002` / `recep1234`.

3. Copy `.env.example` to `.env` and set your credentials:

```bash
cp .env.example .env
```

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=root
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
> inside the webroot.

4. Create the storage directory outside the document root:

```bash
mkdir -p ../gms-storage/comprobantes ../gms-storage/coaches ../gms-storage/logs
```

5. If you are migrating an existing install, move the uploaded files
   (the script is idempotent and never touches the database):

```bash
php bin/migrar_uploads.php
```

6. Configure your database credentials and run the project using
   Nginx + PHP-FPM and MySQL/MariaDB (see `deploy/nginx-gms.conf`).

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

Project status:

Ready for production testing.

---

## Author

Saúl de Jesús San Martín Martínez

Software Developer

GitHub

https://github.com/SaulDevOficial2004