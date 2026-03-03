## DM-Log — Digital Maintenance Logbook

DM-Log is a modern rebuild of the original DM-Log application, implemented with **Laravel**, **Livewire**, and **Tailwind CSS**. It provides a centralized place for tracking issues, departments, and maintenance activity, with exports and reporting for operational insights.

### Tech stack

- **Backend**: Laravel (PHP)
- **Frontend**: Livewire + Blade + Tailwind CSS
- **Database**: MySQL / MariaDB (or any Laravel-supported SQL database)

### Core features

- **Authentication**: Login, logout, password reset, and profile management.
- **Issues**: Create, edit, close/reopen, search, filter, and export issues.
- **Departments & Issue Types**: CRUD management with protection against deleting in-use records.
- **User Management**: Roles, permissions, and admin tools for managing staff.
- **Reports**: Index, monthly, yearly, and logbook reports.
- **Graphs & Statistics**: Issue graphs and dashboard statistics.

### Requirements

- PHP 8.2+
- Composer
- Node.js 20+ and npm
- A SQL database supported by Laravel (MySQL/MariaDB recommended)

### Getting started

1. **Clone the repository**

```bash
git clone <your-repo-url> dmlog
cd dmlog
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install frontend dependencies**

```bash
npm install
```

4. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

Update the `.env` file to match your database and mail configuration.

5. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

This will create tables and seed base data (roles, permissions, departments, issue types, and users).

6. **Run the application**

In one terminal:

```bash
php artisan serve
```

In another terminal (for Vite / frontend assets):

```bash
npm run dev
```

Then open the app at `http://localhost:8000`.

### Testing

```bash
php artisan test
```

### License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
