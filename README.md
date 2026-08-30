# DinePOS — Production-Ready Restaurant Billing & Order Management System

A simple, aesthetic, minimal, extremely responsive, mobile-first, and PWA-ready **PHP 8.2+ & MySQL/MariaDB** restaurant billing and order management web application.

---

## 🌟 Key Features

1. **Mobile-First POS Interface**: Touch-optimized square card buttons for menu items with instant modal dialogs for portions, piece quantities, and weights.
2. **Multi-Tenant Architecture with Strict Data Isolation**: Super Admin creates and manages restaurants; Managers and Cashiers have strict server-side scoped access restricted solely to their restaurant's data.
3. **Daily Resetting Atomic Order Numbering**: Race-condition-safe daily sequence generation (`Order #1`, `Order #2`, `Order #3` resetting to `#1` each calendar day per restaurant timezone).
4. **Server-Side Authoritative Pricing**: Client-side prices are purely cosmetic; the backend securely re-fetches authoritative database prices, computes item line totals and order totals, and snapshots item names, variant names, units, and unit prices.
5. **Locked Orders for Cashiers**: Cashiers have full editing control over unsaved carts. Once saved, orders are permanently locked for cashiers. Only Managers can view complete historical records, edit orders, or soft-delete orders with an audit trail.
6. **Cryptographically Secure QR Code Receipts**: Public receipts are identified strictly by 256-bit random tokens (`bin2hex(random_bytes(32))`) stored as SHA-256 hashes. Predictable order IDs or sequential numbers cannot be guessed or accessed.
7. **Complete Financial Analytics & CSV Exports**: Server-side SQL aggregated reporting for Today, Yesterday, 7 Days, This Month, Last Month, 3 Months, 6 Months, Year, and Custom Date Ranges with 1-click CSV export.
8. **Security Hardening**:
   - 100% Prepared Statements / PDO
   - CSRF token validation on all mutating requests
   - XSS sanitization helper `e()` for all outputs
   - Brute-force protection: 5 failed login attempts lock account/IP for 15 minutes with generic error messages to prevent user enumeration
   - Bcrypt password hashing (`password_hash` / `password_verify`)
   - Secure session management with `session_regenerate_id(true)`, `HttpOnly`, and `SameSite=Lax` cookies.
9. **PWA Ready**: Includes `manifest.json`, Service Worker caching static assets, and home screen installation support.

---

## 📂 Architecture & Directory Structure

```
dinner/
├── config/
│   ├── app.php                # App config, timezone, security headers, helper functions
│   └── database.php           # PDO database configuration (MySQL & SQLite)
├── controllers/
│   ├── BaseController.php     # Base controller with auth guards, CSRF & JSON helpers
│   ├── AuthController.php     # Secure login/logout & brute-force rate limiter
│   ├── CashierController.php  # Cashier touch billing screen & today's summary
│   ├── ManagerController.php  # Manager dashboard, order history, reports, users, audit, settings
│   ├── ItemController.php     # Menu items & variants CRUD (portions, piece, weight)
│   ├── OrderController.php    # Order placement, atomic daily numbering, receipt view, edit/delete
│   ├── ExportController.php   # Daily, Monthly, and Custom Range CSV generator
│   ├── ReceiptController.php  # Public QR receipt lookup by cryptographic token hash
│   └── AdminController.php    # Super Admin restaurant & user management
├── models/
│   ├── Database.php           # PDO Singleton with transaction helpers
│   ├── Restaurant.php         # Restaurant multi-tenant model
│   ├── User.php               # User auth, password hashing, lockout logic
│   ├── Item.php               # Item model (piece, weight, portion)
│   ├── ItemVariant.php        # Portion/size variants & pricing
│   ├── Order.php              # Order creation, calculation, snapshots, soft deletion
│   ├── OrderItem.php          # Historical snapshots of item prices and units
│   ├── DailyCounter.php       # Atomic daily sequence counter
│   ├── AuditLog.php           # Immutable audit log recorder
│   └── RateLimiter.php        # Database-backed login brute-force protection
├── views/
│   ├── layouts/               # Header, Footer, Mobile Bottom Nav, Flash alerts
│   ├── auth/                  # Login screen
│   ├── cashier/               # Touch billing UI & today's summary
│   ├── manager/               # Dashboard, orders, items, reports, exports, staff, audit, settings
│   ├── admin/                 # Superadmin console, restaurants, users
│   ├── receipt/               # Printable thermal receipt & public customer view
│   └── errors/                # 403, 404, 500 error templates
├── public/
│   ├── index.php              # Front controller & request router
│   ├── manifest.json          # PWA Web App Manifest
│   ├── sw.js                  # PWA Service Worker
│   ├── robots.txt             # Search engine restrictions
│   └── .htaccess              # Apache URL rewriting & security headers
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css  # Core layout grid & UI system
│   │   └── app.css            # Custom aesthetic restaurant styles & @media print
│   ├── js/
│   │   ├── bootstrap.bundle.min.js # Modal & dropdown interaction bundle
│   │   ├── qrcode.min.js      # Zero-dependency client-side QR code generator
│   │   ├── billing.js         # Mobile touch cart & instant order save engine
│   │   └── app.js             # Core UI & PWA service worker registration
│   └── icons/                 # PWA application icons (192px, 512px, SVG)
├── database/
│   ├── schema.sql             # Complete MySQL/MariaDB database schema
│   ├── schema_sqlite.sql      # Complete SQLite database schema
│   ├── seeds.sql              # Demo data seed script
│   └── migrate.php            # Automated migration & seed CLI runner
├── storage/
│   ├── logs/                  # Application logs
│   ├── exports/               # Generated CSV export files
│   └── db/                    # SQLite database storage (if used)
├── .env.example               # Configuration template
└── README.md                  # Documentation
```

---

## 🚀 Quick Start & Installation

### Step 1: Clone or Open the Project
Ensure you are in the project root directory:
```bash
cd dinner
```

### Step 2: Environment Configuration
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

For zero-configuration testing out of the box, `.env` defaults to `DB_DRIVER="sqlite"`.
To connect to MySQL/MariaDB in production:
```ini
DB_DRIVER="mysql"
DB_HOST="127.0.0.1"
DB_PORT=3306
DB_NAME="dinner_pos"
DB_USER="your_mysql_user"
DB_PASS="your_mysql_password"
```

### Step 3: Run Database Migration & Seeder
Execute the automated migration CLI script:
```bash
php database/migrate.php
```

### Step 4: Start Local PHP Web Server
Start the built-in PHP development server targeting the `public/` directory:
```bash
php -S 127.0.0.1:8000 -t public/
```

Open your browser at `http://localhost:8000` (or your local development URL).

---

---

## 🔐 Initial Super Admin Setup (Zero Demo Data)

DinePOS installs with a clean database and zero pre-populated demo records.

### Creating Your Master Super Admin Account:
1. **Web Interface**: On first visit to your domain or `/login`, the system automatically detects a fresh database and launches the **Initial Setup Wizard** (`/setup`). Enter your desired Super Admin username and password.
2. **CLI / Terminal**: Alternatively, run:
   ```bash
   php database/create_admin.php <username> <password>
   ```

Once the Master Super Admin is created:
1. Sign in to the **Super Admin Console** (`/admin/dashboard`).
2. Create your restaurant under **Restaurants** → **+ Create Restaurant**.
3. Create your managers and cashiers under **Users** → **+ Create User**.
4. Managers can then log in to configure dishes, rates, and manage restaurant staff.


---

## 🔒 Security Architecture

- **Authoritative Price Enforcement**: Order prices sent in client requests are ignored; calculated on server.
- **Race Condition Prevention**: Daily sequence counters are locked inside database transactions using atomic row updates (`FOR UPDATE` / `ON CONFLICT DO UPDATE`).
- **Cryptographic Receipt Tokens**: Receipts are viewed via `/receipt/{token}`, matching against `hash('sha256', $token)` in the database.
- **Brute Force Lockout**: 5 failed login attempts trigger a 15-minute lock on the IP and username.
- **Session Hardening**: Sessions are regenerated upon login (`session_regenerate_id(true)`), cookies marked `HttpOnly` and `SameSite=Lax`.
# dinner
