# Al-Matar Al-Thari - Digital Marketing Platform

A comprehensive Laravel-based digital marketing platform with multi-language support, loyalty points system, affiliate marketing, and QR code generation capabilities.

## 🚀 Features

- **Multi-Language Support**: Full English and Arabic localization with RTL support
- **User Management**: Role-based access control (Admin, Merchant, Customer)
- **Company Management**: Multi-company support with branches
- **Digital Cards**: QR code generation and management
- **Loyalty Points**: Points earning and redemption system
- **Affiliate Marketing**: Referral tracking and commission management
- **Coupon System**: Digital coupon generation and usage tracking
- **Notifications**: Real-time notification system
- **Analytics**: Comprehensive reporting and analytics
- **Responsive Design**: Bootstrap 5 based responsive templates

## 📋 Requirements

- PHP 8.1 or higher
- Composer 2.x
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 16.x or higher
- NPM 8.x or higher

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/al-matar-al-thari.git
cd al-matar-al-thari
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the environment file and configure it:

```bash
cp .env.example .env
```

Edit the `.env` file with your database credentials and other settings:

```env
APP_NAME="Al-Matar Al-Thari"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=al_matar_al_thari
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

Create the database and run migrations:

```bash
php artisan migrate
```

Seed the database with initial data:

```bash
php artisan db:seed
```

### 7. Storage Link

Create the storage symlink:

```bash
php artisan storage:link
```

### 8. Build Assets

```bash
npm run build
```

For development with hot reloading:

```bash
npm run dev
```

## 🚀 Running the Application

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Queue Worker (Optional)

For background jobs and notifications:

```bash
php artisan queue:work
```

### Scheduled Tasks (Optional)

For automated tasks like cleaning up old notifications:

```bash
php artisan schedule:work
```

## 🔧 Additional Setup

### Mail Configuration

For email functionality, configure your mail settings in `.env`. For local development, you can use Mailpit:

1. Install Mailpit: https://github.com/axllent/mailpit
2. Start Mailpit: `mailpit`
3. Access mail interface at `http://localhost:8025`

### Permissions

Ensure the following directories are writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### SSL Certificate (Local Development)

For HTTPS local development:

```bash
php artisan serve --host=localhost --port=8000 --tls
```

## 🌍 Multi-Language Usage

The platform supports English and Arabic languages:

- **English**: `http://localhost:8000/en`
- **Arabic**: `http://localhost:8000/ar`

Language can be switched using the language selector in the UI or by visiting the localized URLs.

## 🔑 Default Credentials

After running the database seeder, you can log in with:

- **Admin**: admin@example.com / password
- **Merchant**: merchant@example.com / password
- **Customer**: customer@example.com / password

## 📁 Project Structure

```
al-matar-al-thari/
├── app/
│   ├── Models/          # Eloquent models
│   ├── Http/            # Controllers and middleware
│   ├── Services/        # Business logic services
│   ├── Traits/          # Reusable model traits
│   └── Helpers/         # Helper functions
├── config/              # Configuration files
├── database/
│   ├── migrations/      # Database migrations
│   └── seeders/         # Database seeders
├── lang/                # Language files (en, ar)
├── resources/
│   ├── views/           # Blade templates
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
├── routes/              # Route definitions
└── storage/             # Storage files
```

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

Run specific tests:

```bash
php artisan test --filter=UserTest
```

## 🚀 Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `composer install --no-dev --optimize-autoloader`
4. Run `npm run build`
5. Run `php artisan config:cache`
6. Run `php artisan route:cache`
7. Run `php artisan view:cache`
8. Set up proper file permissions
9. Configure your web server (Apache/Nginx)

## 📝 Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Create a new controller
php artisan make:controller ControllerName

# Create a new model
php artisan make:model ModelName

# Create a new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Create a new seeder
php artisan make:seeder SeederName

# Run seeders
php artisan db:seed
```

## 🐛 Troubleshooting

### Common Issues

1. **Permission Denied**: Ensure storage and bootstrap/cache directories are writable
2. **Class Not Found**: Run `composer dump-autoload`
3. **Migration Errors**: Check database connection and credentials
4. **Asset Issues**: Run `npm run build` or `npm run dev`
5. **Locale Issues**: Ensure locales are configured in `config/localization.php`

### Debug Mode

Enable debug mode in `.env`:

```env
APP_DEBUG=true
```

Check Laravel logs in `storage/logs/laravel.log`

## 🤝 Support

For issues and questions:

1. Check the Laravel documentation: https://laravel.com/docs
2. Search existing issues on GitHub
3. Create a new issue with detailed information

## 📄 License

This project is licensed under the MIT License.

## 🙏 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please read the contributing guidelines before submitting PRs.

---

**Happy coding! 🎉**
