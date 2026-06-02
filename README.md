# food_delivery_final  

A simple **PHP**‑based food‑delivery web application that showcases core e‑commerce functionality, order management, and email notifications using **PHPMailer**.

---

## Overview  

`food_delivery_final` is a learning‑project / starter kit for building a food‑ordering platform. It includes:

* A MySQL database schema (`Database/food_db.sql`) with tables for restaurants, menus, users, and orders.  
* Backend logic written in PHP (procedural / OOP) to handle product browsing, cart management, checkout, and order tracking.  
* Email integration via **PHPMailer** for order confirmations, password resets, and promotional messages.  

The repository ships with the full PHPMailer source tree, so you can use it out‑of‑the‑box without pulling additional dependencies.

---

## Features  

| ✅ | Feature |
|---|---------|
| ✔️ | User registration & authentication |
| ✔️ | Restaurant & menu browsing |
| ✔️ | Shopping cart with quantity updates |
| ✔️ | Secure checkout with payment placeholder |
| ✔️ | Order history & status tracking |
| ✔️ | Automated email notifications (order receipt, password reset) |
| ✔️ | Configurable SMTP settings (via `.env` or `config.php`) |
| ✔️ | SQL dump for quick database setup |

---

## Tech Stack  

| Layer | Technology |
|-------|------------|
| **Language** | PHP 8.0+ |
| **Database** | MySQL / MariaDB |
| **Email** | PHPMailer (bundled) |
| **Web Server** | Apache / Nginx (compatible with PHP-FPM) |
| **Styling** | Bootstrap 5 (optional) |
| **Version Control** | Git |

---

## Installation  

> **Prerequisites**  
> * PHP 8.0 or newer with the `pdo_mysql` extension  
> * Composer (for optional dependency management)  
> * MySQL server  

### 1. Clone the repository  

```bash
git clone https://github.com/your-username/food_delivery_final.git
cd food_delivery_final
```

### 2. Set up the database  

```bash
# Create a new database (e.g., food_delivery)
mysql -u root -p -e "CREATE DATABASE food_delivery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the schema and sample data
mysql -u root -p food_delivery < Database/food_db.sql
```

### 3. Configure environment variables  

Create a `.env` file in the project root (or edit `config.php`) with your own values:

```dotenv
DB_HOST=localhost
DB_NAME=food_delivery
DB_USER=YOUR_DB_USER
DB_PASS=YOUR_DB_PASSWORD

# SMTP settings for PHPMailer
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=YOUR_SMTP_USERNAME
SMTP_PASS=YOUR_SMTP_PASSWORD
SMTP_FROM=no-reply@example.com
SMTP_FROM_NAME=Food Delivery
```

> **Tip:** Use a library like `vlucas/phpdotenv` (installed via Composer) to load the file automatically.

### 4. Install Composer dependencies (optional)  

PHPMailer is already bundled, but if you prefer to manage it via Composer:

```bash
composer install
```

### 5. Set proper permissions  

```bash
# Example for Apache
sudo chown -R