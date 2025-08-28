# School Management System

A Laravel-based School Management System with role-based access control (Admin, Teacher, Student).

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js

### Installation (5 minutes)

```bash
# 1. Clone and install
git clone <repository-url>
cd school-management-system
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate
php artisan db:seed

# 4. Start server
php artisan serve
```

**Access:** http://localhost:8000

## 🔐 User Roles

### Admin
- Full system access
- Create/delete classrooms
- Manage all students

### Teacher
- View assigned classrooms
- Manage student grades
- Remove students from class

### Student
- View personal profile
- Update personal info
- View grades

## 🛠️ Development

```bash
# Start development server
php artisan serve
```

## 📊 Database

Uses SQLite by default. For MySQL/PostgreSQL:
1. Update `.env` file
2. Run `php artisan migrate:fresh`

---

**Ready to use! 🎉**
