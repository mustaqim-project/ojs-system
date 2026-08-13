# OJS System - Open Journal System

Sistem manajemen jurnal akademik berbasis Laravel dengan fitur lengkap untuk editorial workflow, peer review, dan manajemen keuangan.

## 🚀 Fitur Utama

### Core Features

- ✅ **Multi-Journal Support** - Kelola banyak jurnal dalam satu sistem
- ✅ **Role-Based Access Control** - 16 roles dengan 55 permissions (Spatie Permission)
- ✅ **Submission Workflow** - 19-state machine untuk tracking artikel
- ✅ **Peer Review System** - Multi-round review dengan blind mode
- ✅ **Finance Management** - Invoice, payment verification, receipt generation
- ✅ **CMS & Content Management** - Pages, announcements, volumes, issues
- ✅ **Audit Trail** - Immutable logging untuk semua aktivitas
- ✅ **REST API** - Full API untuk integrasi dengan sistem lain
- ✅ **WhatsApp Notifications** - Via Fonnte API
- ✅ **DOI Integration** - DataCite/Crossref ready

### Technology Stack

- **Backend**: Laravel 10+
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum (API) + Spatie Permission
- **PDF Generation**: DomPDF
- **Notifications**: Mail + Database + WhatsApp (Fonnte)
- **Queue**: Database/Redis (untuk notifications)

## 📦 Installation

### 1. Clone Repository

```bash
git clone https://github.com/mustaqim-project/ojs-system.git
cd ojs-system
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE ojs_system;"

# Run migrations
php artisan migrate

# Seed roles & permissions
php artisan db:seed --class=RolePermissionSeeder

# Seed initial data
php artisan db:seed --class=DatabaseSeeder
```

### 5. Storage Setup

```bash
php artisan storage:link
```

### 6. Start Development Server

```bash
php artisan serve
```

Default login:

- Email: `admin@example.com`
- Password: `password`

## 🗄️ Database Schema

### Core Tables

- `users` - Users dengan extended profile
- `journals` - Multi-journal support
- `articles` - Submissions dengan versioning
- `settings` - Key-value settings per journal

### Review Tables

- `review_rounds` - Multi-round review tracking
- `review_assignments` - Reviewer assignments
- `review_responses` - Review results & rubric scores
- `editorial_decisions` - Editorial decisions history

### Finance Tables

- `invoices` - Invoice management
- `payments` - Payment proofs & verification
- `receipts` - PDF receipts
- `refunds` - Refund workflow

### Production Tables

- `production_tasks` - Copyediting, layout, proofreading
- `submission_versions` - Versioning history
- `submission_files` - File management dengan virus scan

### Publication Tables

- `volumes` - Journal volumes
- `issues` - Journal issues
- `issue_article` - Many-to-many relationship
- `article_galleys` - PDF/HTML/XML galleys
- `article_dois` - DOI registration tracking

### CMS Tables

- `cms_pages` - CMS pages dengan SEO
- `cms_page_versions` - Content versioning
- `announcements` - Scheduled announcements

### Security Tables

- `audit_trails` - Immutable audit log
- `login_history` - Login tracking

## 🔐 Roles & Permissions

### System Roles

- **super-admin** - Full system access
- **system-admin** - System administration

### Journal Roles

- **journal-manager** - Full journal management
- **managing-editor** - Editorial oversight
- **section-editor** - Section-level editing
- **reviewer** - Peer review
- **copy-editor** - Copy editing
- **layout-editor** - Layout editing
- **proofreader** - Proofreading
- **publisher** - Publication management
- **finance** - Payment & invoice management
- **marketing** - CMS & announcements
- **author** - Submission management
- **reader** - Public access

## 📡 API Endpoints

### Authentication

- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/register` - Register
- `POST /api/v1/auth/logout` - Logout
- `GET /api/v1/auth/me` - Get current user

### Submissions

- `GET /api/v1/articles` - List articles
- `GET /api/v1/articles/{id}` - Get article detail
- `POST /api/v1/submissions` - Create submission
- `PUT /api/v1/submissions/{id}` - Update submission
- `POST /api/v1/submissions/{id}/submit` - Submit for review
- `POST /api/v1/submissions/{id}/withdraw` - Withdraw submission

### Reviews

- `GET /api/v1/review-assignments` - My review assignments
- `POST /api/v1/review-assignments/{id}/respond` - Accept/decline review
- `POST /api/v1/review-assignments/{id}/review` - Submit review

### Finance

- `GET /api/v1/invoices` - List invoices
- `POST /api/v1/invoices/{id}/payments` - Upload payment proof
- `GET /api/v1/invoices/{id}/receipt` - Generate receipt

### Reports

- `GET /api/v1/reports/journals/{id}/stats` - Journal statistics
- `GET /api/v1/reports/journals/{id}/submissions` - Submission trends
- `GET /api/v1/reports/journals/{id}/reviews` - Review statistics

## 🔔 Notifications

### Channels

- **Mail** - Email notifications
- **Database** - In-app notifications
- **WhatsApp** - Via Fonnte API (optional)

### Notification Types

- Article Submitted
- Review Assigned
- Review Reminder (3 days before due)
- Review Escalation (2 days overdue)
- Payment Uploaded
- Payment Verified
- Review Completed

## ⏰ Scheduled Tasks

```bash
# Review reminders - daily at 8 AM
php artisan reviews:send-reminders

# Review escalations - daily at 9 AM
php artisan reviews:send-escalations

# Publish scheduled issues - hourly
php artisan issues:publish-scheduled
```

Add to crontab (Linux/Mac):

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Or use Laravel Horizon for queue management.

## 📁 Project Structure

```
app/
├── Console/
│   ├── Commands/          # Artisan commands
│   └── Kernel.php         # Scheduled tasks
├── Events/                # Application events
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/        # API controllers
│   │   └── Admin/         # Admin controllers
│   ├── Middleware/         # HTTP middleware
│   └── Requests/          # Form requests
├── Listeners/             # Event listeners
├── Models/                # Eloquent models
├── Notifications/         # Notification classes
│   └── Channels/          # Custom notification channels
├── Policies/              # Authorization policies
├── Providers/             # Service providers
├── Services/              # Business logic
└── Traits/                # Reusable traits

database/
├── migrations/            # Database migrations
└── seeders/               # Database seeders

resources/
├── views/
│   └── pdfs/              # PDF templates
└── ...

routes/
├── api.php                # API routes
├── author.php             # Author routes
├── reviewer.php           # Reviewer routes
└── web.php                # Web routes
```

## 🔧 Configuration

### Key Settings (via `settings` table)

- `apc_amount` - Default article processing charge
- `apc_currency` - Currency (IDR, USD, EUR)
- `review_due_days` - Default review deadline
- `invoice_due_days` - Payment due period
- `fonnte_api_key` - WhatsApp API key

### Environment Variables

```env
APP_NAME=OJS System
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ojs_system
DB_USERNAME=root
DB_PASSWORD=

FONNTE_API_KEY=your_fonnte_api_key

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Configure database connection
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed permissions: `php artisan db:seed --class=RolePermissionSeeder`
- [ ] Create storage symlink: `php artisan storage:link`
- [ ] Configure queue worker (Supervisor)
- [ ] Setup cron job for scheduled tasks
- [ ] Configure backup strategy
- [ ] Enable HTTPS
- [ ] Setup monitoring (Laravel Telescope/Horizon)

### Server Requirements

- PHP 8.1+
- MySQL 8.0+ / PostgreSQL 12+
- Composer 2+
- Node.js 16+ (for asset compilation)
- Redis (optional, for queue & cache)

## 📝 License

This project is licensed under the MIT License.

## 👥 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📞 Support

For support, email support@ojs-system.com or create an issue in the repository.

## 🙏 Credits

- [Laravel](https://laravel.com/)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [DomPDF](https://github.com/barryvdh/laravel-dompdf)
- [Fonnte](https://fonnte.com/)

---

**Status**: ✅ Phase 0-2 Complete (Foundation, Core MVP, Peer Review & Finance)
**Version**: 1.0.0
**Last Updated**: 2024
