# ITQoM Platform 🎓

**ITQoM Platform** adalah platform education bootcamp yang menyediakan pembelajaran komprehensif seputar development, UI/UX design, fullstack development, dan berbagai teknologi modern lainnya.

## 🌟 Features

- **📚 Comprehensive Learning Materials** - Materi pembelajaran lengkap untuk berbagai bidang teknologi
- **💻 Development Bootcamp** - Program intensif untuk pengembangan software
- **🎨 UI/UX Design Course** - Pembelajaran design interface dan user experience
- **⚡ Fullstack Development** - Training fullstack dengan teknologi terkini
- **📊 Progress Tracking** - Sistem monitoring kemajuan belajar
- **👥 Community Learning** - Platform kolaborasi antar peserta
- **📱 Responsive Design** - Akses mudah dari berbagai perangkat

## 🚀 Live Demo

Platform ini telah di-deploy dan dapat diakses di:
- **Production**: **[itqom-platform.tech](https://itqom-platform.tech)** (Custom Domain)
- **Heroku**: **[itqom-platform.herokuapp.com](https://itqom-platform.herokuapp.com)** (Heroku Default)

## 🛠️ Technology Stack

### Backend
- **Laravel** - PHP web application framework
- **PHP 8.4+** - Server-side programming language
- **Apache** - Web server
- **MySQL** - Database management (JawsDB)

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Frontend build tool
- **Axios** - HTTP client for API calls

### Cloud Services & Add-ons
- **Heroku** - Cloud platform deployment
- **Cloudinary** - Image and video management
- **JawsDB MySQL** - Managed MySQL database
- **Heroku Key-Value Store** - Redis-like data storage

### Development Tools
- **Git** - Version control
- **Composer** - PHP dependency management
- **NPM/Yarn** - JavaScript package manager
- **Laravel Artisan** - Command-line interface
- **Heroku CLI** - Deployment tools

## 📊 Performance Metrics

Berdasarkan monitoring terbaru:
- **Response Time**: ~67ms
- **Throughput**: <1 rps
- **Memory Usage**: ~15%
- **Uptime**: 99.9%

## 🏗️ Architecture

```
ITQoM Platform
├── Frontend (Vue.js + Tailwind CSS)
├── Backend (Laravel Framework)
├── Database (JawsDB MySQL)
├── Media Storage (Cloudinary)
└── Caching (Heroku Key-Value Store)
```

## 📦 Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 16+
- NPM or Yarn
- MySQL
- Git

### Local Development
```bash
# Clone repository
git clone  https://github.com/rehanalfarizu/itqom-education.git
cd itqom-education

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
# or
yarn install

# Setup environment variables
cp .env.example .env
# Edit .env with your database and service configurations

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Build frontend assets
npm run dev
# or for production
npm run build

# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server (for hot reload)
npm run dev
```

### Environment Variables
```env
# Application Configuration
APP_NAME=ITQoM Platform
APP_ENV=production
APP_KEY=your-app-key
APP_DEBUG=false
APP_URL=https://itqom-platform.tech

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-username
DB_PASSWORD=your-database-password

# Cloudinary Configuration
CLOUDINARY_URL=your-cloudinary-url
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret

# Redis Configuration (Heroku Key-Value Store)
REDIS_URL=your-redis-url

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password

# JWT Configuration (if using)
JWT_SECRET=your-jwt-secret
```

## 🚢 Deployment

### Heroku Deployment with Custom Domain
```bash
# Login to Heroku
heroku login

# Create Heroku app
heroku create itqom-platform

# Add required add-ons
heroku addons:create jawsdb:kitefin
heroku addons:create cloudinary:starter
heroku addons:create heroku-redis:mini

# Set environment variables
heroku config:set APP_KEY=$(php artisan --no-ansi key:generate --show)
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set APP_URL=https://itqom.tech

# Add Node.js buildpack for frontend assets
heroku buildpacks:add heroku/nodejs
heroku buildpacks:add heroku/php

# Create Procfile
echo "web: vendor/bin/heroku-php-apache2 public/" > Procfile

# Deploy
git add .
git commit -m "Deploy to Heroku"
git push heroku main

# Run migrations on Heroku
heroku run php artisan migrate --force

# Add custom domain (requires verified Heroku account)
heroku domains:add itqom.tech
heroku domains:add www.itqom.tech

# Get DNS target for domain configuration
heroku domains
```

### Domain Configuration
After adding the domain to Heroku, you need to configure your domain's DNS:

**DNS Records Required:**
```
Type: CNAME
Name: www
Target: itqom-platform.tech

Type: ALIAS/ANAME (or CNAME for subdomain)
Name: @
Target: itqom-platform.tech
```

**Note**: Replace `itqom-platform.tech` with the actual DNS target provided by Heroku after adding the domain.

## 🌐 Custom Domain Setup

Platform ini menggunakan custom domain **itqom.tech** untuk memberikan pengalaman yang lebih profesional.

### Domain Management
- **Primary Domain**: `itqom-platform.tech`
- **WWW Redirect**: `www.itqom-platform.tech` → `itqom-platform.tech`
- **SSL Certificate**: Otomatis melalui Heroku ACM (Automated Certificate Management)
- **CDN**: Cloudflare (opsional untuk performa optimal)

### SSL Certificate
Heroku secara otomatis menyediakan SSL certificate untuk custom domain:
```bash
# Enable Heroku ACM (Automated Certificate Management)
heroku certs:auto:enable

# Check SSL certificate status  
heroku certs
```

### Build Process
The deployment process includes:
1. **Composer install** - Install PHP dependencies
2. **NPM install** - Install JavaScript dependencies  
3. **NPM run build** - Build Vue.js + Tailwind CSS assets
4. **Laravel optimization** - Cache routes, config, and views
5. **Domain routing** - Configure custom domain routing
6. **SSL provisioning** - Automatic HTTPS certificate

## 📚 Course Modules

### 🔧 Development Track
- **Frontend Development** (HTML, CSS, JavaScript, React)
- **Backend Development** (PHP, Node.js, Python)
- **Database Management** (MySQL, PostgreSQL, MongoDB)
- **API Development** (REST, GraphQL)

### 🎨 UI/UX Track
- **Design Principles** (Color Theory, Typography, Layout)
- **User Research** (User Interviews, Usability Testing)
- **Prototyping Tools** (Figma, Adobe XD, Sketch)
- **Interaction Design** (Micro-interactions, Animations)

### ⚡ Fullstack Track
- **MEAN/MERN Stack**
- **LAMP Stack**
- **DevOps & Deployment**
- **Testing & Quality Assurance**

## 👥 Contributing

Kami sangat menghargai kontribusi dari komunitas! Berikut cara berkontribusi:

1. **Fork** repository ini
2. **Create** feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** perubahan (`git commit -m 'Add some AmazingFeature'`)
4. **Push** ke branch (`git push origin feature/AmazingFeature`)
5. **Create** Pull Request

### Development Guidelines
- Follow PSR-14 coding standards untuk PHP
- Use Vue 3 Composition API untuk components baru
- Gunakan Tailwind CSS utility classes, hindari custom CSS
- Tulis unit tests dengan PHPUnit dan Jest
- Update dokumentasi jika diperlukan
- Pastikan semua tests passed sebelum submit PR
- Follow Laravel naming conventions untuk routes, controllers, dan models

## 🧪 Testing

### Backend Testing (PHPUnit)
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Frontend Testing (Jest + Vue Test Utils)
```bash
# Run Vue component tests
npm run test

# Run tests in watch mode
npm run test:watch

# Run with coverage
npm run test:coverage
```

## 📦 Project Structure

```
itqom-platform/
├── app/                    # Laravel application logic
│   ├── Http/Controllers/   # API controllers
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/               # Vue.js components and pages
│   │   ├── components/   # Reusable Vue components
│   │   ├── pages/        # Page components
│   │   ├── stores/       # Pinia stores (state management)
│   │   └── app.js        # Main Vue application
│   ├── css/              # Tailwind CSS and custom styles
│   └── views/            # Blade templates
├── routes/
│   ├── api.php           # API routes
│   └── web.php           # Web routes
├── tests/                # PHPUnit tests
├── public/               # Public assets (built files)
├── package.json          # NPM dependencies
├── composer.json         # Composer dependencies
├── tailwind.config.js    # Tailwind CSS configuration
└── vite.config.js        # Vite build configuration
```

### Authentication (Laravel Sanctum)
```php
POST /api/auth/login           // User login
POST /api/auth/register        // User registration
POST /api/auth/logout          // User logout
GET  /api/user                 // Get authenticated user
POST /api/auth/forgot-password // Password reset request
POST /api/auth/reset-password  // Reset password
```

### Courses Management
```php
GET    /api/courses            // Get all courses (with pagination)
GET    /api/courses/{id}       // Get specific course details
POST   /api/courses            // Create new course (admin/instructor)
PUT    /api/courses/{id}       // Update course (admin/instructor)
DELETE /api/courses/{id}       // Delete course (admin only)
GET    /api/courses/{id}/modules // Get course modules
POST   /api/courses/{id}/enroll  // Enroll in course
```

### User Progress & Learning
```php
GET  /api/user/progress        // Get user learning progress
POST /api/user/progress        // Update lesson progress
GET  /api/user/courses         // Get enrolled courses
GET  /api/user/certificates    // Get earned certificates
POST /api/lessons/{id}/complete // Mark lesson as completed
GET  /api/dashboard            // Get dashboard data
```

## 🔒 Security

- **Laravel Sanctum** - API token authentication
- **CSRF Protection** - Cross-site request forgery protection
- **Authorization Policies** - Role-based access control (Student, Instructor, Admin)
- **Input Validation** - Laravel form request validation
- **SQL Injection Prevention** - Eloquent ORM protection
- **XSS Protection** - Blade template escaping
- **HTTPS Enforcement** - Secure data transmission
- **Rate Limiting** - API throttling to prevent abuse

## 📈 Monitoring & Analytics

Platform ini menggunakan berbagai tools monitoring:
- **Heroku Metrics** - Server performance monitoring
- **Application Logs** - Error tracking dan debugging
- **User Analytics** - Learning progress dan engagement metrics

## 🤝 Support

Jika Anda mengalami masalah atau memiliki pertanyaan:

- **📧 Email**: support@itqom.tech
- **🌐 Website**: [itqom.tech](https://itqom.tech)
- **💬 Discord**: [ITQoM Community](https://discord.gg/itqom)
- **📚 Documentation**: [docs.itqom.tech](https://docs.itqom.tech)
- **🐛 Issues**: [GitHub Issues](https://github.com/[username]/itqom-platform/issues)

## 📄 License

Project ini dilisensikan under [MIT License](LICENSE) - lihat file LICENSE untuk detail lengkap.

## 🙏 Acknowledgments

- **Heroku** untuk platform cloud hosting
- **Cloudinary** untuk media management
- **JawsDB** untuk managed MySQL service
- **Laravel** untuk framework PHP yang powerful
- **Vue.js** untuk frontend development yang modern
- **Tailwind CSS** untuk styling yang efisien

## 📢 Follow Us

**name: "Unzurna"** url: https://github.com/KingEery
**name: "Rehan Alfarizi"** url: https://github.com/rehanalfarizu
**name: "Yafa Putra"** url: https://github.com/yafaputra
**name: "Albar "** url: https://github.com/albarstring
**name: "Bregas"** url: https://github.com/siBregas

- **Dimas Dwi Setyawan** url: https://github.com/DimsDwi
- **Unzurna** url: https://github.com/KingEery
- **Rehan Alfarizi** url: https://github.com/rehanalfarizu
- **Yafa Putra** url: https://github.com/yafaputra
- **Albar** url: https://github.com/albarstring
- **Bregas** url: https://github.com/siBregas
- **Satrio** url: https://github.com/LazyYoow
---

**Built with ❤️ by ITQoM Team**

[![Deployed on Heroku](https://img.shields.io/badge/Deployed%20on-Heroku-430098.svg)](https://itqom.tech)
[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=flat&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![Cloudinary](https://img.shields.io/badge/Cloudinary-3448C5?style=flat&logo=cloudinary&logoColor=white)](https://cloudinary.com)
[![Custom Domain](https://img.shields.io/badge/Domain-itqom.tech-blue?style=flat&logo=internetexplorer&logoColor=white)](https://itqom.tech)

⭐ **Star** repository ini jika project ini membantu Anda!
