# 🏪 Palsa POS - Complete Point of Sale System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)
[![Kenya](https://img.shields.io/badge/Kenya-Ready-green.svg)](https://kenya.com)
[![M-Pesa](https://img.shields.io/badge/M--Pesa-Integrated-green.svg)](https://developer.safaricom.co.ke)

A comprehensive, production-ready Point of Sale (POS) system built with Laravel 11, optimized for the Kenyan market with M-Pesa integration and Kenya Shillings currency support.

## 🚀 **Features**

### 🏪 **Core POS Functionality**
- ✅ **Product Management** - Complete CRUD with SKU/barcode support
- ✅ **Real-time Inventory** - Automatic stock tracking and alerts
- ✅ **Order Processing** - Full order lifecycle management
- ✅ **Shopping Cart** - Advanced cart with real-time validation
- ✅ **Customer Management** - User profiles and purchase history
- ✅ **Receipt Generation** - PDF receipts with proper formatting

### 🇰🇪 **Kenyan Market Optimization**
- ✅ **Kenya Shillings (KSh)** - Complete currency formatting
- ✅ **M-Pesa Integration** - STK Push automation
- ✅ **16% VAT** - Kenyan tax calculations
- ✅ **Nairobi Timezone** - EAT timezone support
- ✅ **Kenyan Phone Numbers** - Proper formatting and validation
- ✅ **Local Products** - Sample Kenyan products included

### 💳 **Payment Systems**
- ✅ **M-Pesa** - Full STK Push integration (Safaricom)
- ✅ **Airtel Money** - API ready
- ✅ **T-Kash** - Telkom integration ready
- ✅ **Equitel** - Equity Bank integration
- ✅ **Bank Transfers** - Major Kenyan banks
- ✅ **Cash Payments** - Manual confirmation

### 📊 **Business Intelligence**
- ✅ **Real-time Dashboard** - Live sales and inventory metrics
- ✅ **Sales Reports** - Daily, monthly, custom periods
- ✅ **Inventory Reports** - Stock levels and movements
- ✅ **Customer Analytics** - Purchase patterns and insights
- ✅ **Profit/Loss Reports** - Financial analysis

### 🔧 **Technical Features**
- ✅ **Laravel 11** - Latest framework version
- ✅ **Docker Ready** - Complete containerization
- ✅ **API Endpoints** - Mobile app ready
- ✅ **Role-based Access** - Admin, SuperAdmin, Customer
- ✅ **Security** - Enterprise-grade protection
- ✅ **Testing Suite** - Comprehensive tests
- ✅ **Automated Tasks** - Scheduled maintenance

## 🛠️ **Quick Start**

### **Prerequisites**
- Docker & Docker Compose
- Git

### **Installation**

1. **Clone the repository**
   ```bash
   git clone https://github.com/Ceteway/PALSA-POS.git
   cd PALSA-POS
   ```

2. **Start with Docker**
   ```bash
   docker-compose up -d
   ```

3. **Run setup**
   ```bash
   # Windows
   ./complete-setup.bat
   
   # Linux/Mac
   chmod +x complete-setup.sh && ./complete-setup.sh
   ```

4. **Access the application**
   - **Web App**: http://localhost:8000
   - **Admin Panel**: http://localhost:8000/admin
   - **API**: http://localhost:8000/api

### **Default Credentials**
- **SuperAdmin**: superadmin@gmail.com / admin123
- **Admin**: admin@palsapos.com / admin123
- **Customer**: customer@example.com / password

## 🇰🇪 **M-Pesa Setup**

1. **Get Safaricom Credentials**
   - Visit: https://developer.safaricom.co.ke/
   - Apply for M-Pesa API access

2. **Update Environment**
   ```bash
   # Update .env file
   MPESA_CONSUMER_KEY=your_consumer_key
   MPESA_CONSUMER_SECRET=your_consumer_secret
   MPESA_SHORTCODE=your_shortcode
   MPESA_PASSKEY=your_passkey
   MPESA_ENV=sandbox  # or 'production'
   ```

3. **Test Configuration**
   ```bash
   curl http://localhost:8000/api/mpesa/test-config
   ```

## 📱 **API Documentation**

### **Authentication**
```http
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
```

### **Products**
```http
GET /api/products
GET /api/products/{id}
POST /api/admin/products
PUT /api/admin/products/{id}
```

### **Orders**
```http
GET /api/customer/orders
POST /api/customer/orders
GET /api/admin/orders
```

### **M-Pesa Payments**
```http
POST /api/mpesa/initiate-payment
POST /api/mpesa/check-status
```

## 🏗️ **Architecture**

### **Backend Stack**
- **Framework**: Laravel 11
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Queue**: Redis
- **Storage**: Local/S3 ready

### **Frontend Stack**
- **CSS**: Bootstrap 5 + Custom
- **JavaScript**: Vanilla JS + Alpine.js
- **Build**: Vite

### **Infrastructure**
- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx
- **PHP**: 8.2 with OPcache
- **Process Manager**: Supervisor

## 📊 **System Requirements**

### **Minimum Requirements**
- **RAM**: 2GB
- **Storage**: 5GB
- **CPU**: 2 cores
- **Network**: Stable internet for M-Pesa

### **Recommended (Production)**
- **RAM**: 4GB+
- **Storage**: 20GB+ SSD
- **CPU**: 4+ cores
- **Network**: High-speed internet
- **SSL**: Required for M-Pesa

## 🔒 **Security Features**

- ✅ **Input Validation** - Comprehensive request validation
- ✅ **CSRF Protection** - Cross-site request forgery protection
- ✅ **SQL Injection** - Eloquent ORM protection
- ✅ **XSS Protection** - Output sanitization
- ✅ **File Upload Security** - Safe file handling
- ✅ **Role-based Access** - Granular permissions
- ✅ **API Authentication** - Sanctum token-based auth

## 📈 **Performance**

- ✅ **Database Optimization** - Proper indexing and queries
- ✅ **Caching** - Redis for sessions and cache
- ✅ **OPcache** - PHP bytecode caching
- ✅ **Image Optimization** - Automatic image processing
- ✅ **CDN Ready** - Asset optimization
- ✅ **Queue System** - Background job processing

## 🧪 **Testing**

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test suite
docker-compose exec app php artisan test --testsuite=Feature

# Run with coverage
docker-compose exec app php artisan test --coverage
```

## 📋 **Production Deployment**

### **1. Environment Setup**
```bash
# Copy production environment
cp .env.production .env
# Update with your production values
```

### **2. SSL Certificate**
```bash
# Add SSL certificates to docker/nginx/ssl/
```

### **3. Deploy**
```bash
# Production deployment
docker-compose -f docker-compose.production.yml up -d

# Run deployment script
./deploy.sh production
```

### **4. Monitoring**
```bash
# Check system status
./production-status.bat
```

## 🔧 **Maintenance**

### **Daily Tasks (Automated)**
- ✅ **8 AM**: Daily sales reports
- ✅ **Every 6 hours**: Low stock alerts
- ✅ **3 AM**: Cache clearing
- ✅ **1 AM**: Database backups

### **Manual Commands**
```bash
# Clear caches
docker-compose exec app php artisan optimize:clear

# Run migrations
docker-compose exec app php artisan migrate

# Generate reports
docker-compose exec app php artisan pos:daily-sales-report

# Check low stock
docker-compose exec app php artisan pos:low-stock-alerts
```

## 📚 **Documentation**

- 📖 **[Complete System Guide](COMPLETE_SYSTEM_GUIDE.md)** - Full system operation
- 📱 **[M-Pesa Integration Guide](MPESA_INTEGRATION_GUIDE.md)** - Payment setup
- 🇰🇪 **[Kenyan Market Guide](KENYAN_MARKET_GUIDE.md)** - Local optimization
- 💰 **[Currency Guide](KENYAN_CURRENCY_GUIDE.md)** - KES implementation
- ✅ **[Production Checklist](PRODUCTION_CHECKLIST.md)** - Deployment guide

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 **Acknowledgments**

- **Laravel Team** - Amazing framework
- **Safaricom** - M-Pesa API
- **Bootstrap Team** - UI framework
- **Docker Team** - Containerization
- **Kenyan Developers** - Local market insights

## 📞 **Support**

- **Documentation**: Check the guides in `/docs`
- **Issues**: Create a GitHub issue
- **Email**: support@palsapos.com
- **Community**: Join our Discord server

## 🎯 **Roadmap**

### **Version 2.0 (Coming Soon)**
- [ ] Mobile App (React Native)
- [ ] Advanced Analytics
- [ ] Multi-store Support
- [ ] Inventory Forecasting
- [ ] Customer Loyalty Program
- [ ] Advanced Reporting

### **Version 2.1**
- [ ] WhatsApp Integration
- [ ] SMS Notifications
- [ ] Barcode Scanner App
- [ ] Offline Mode
- [ ] Multi-language Support

---

## 🏆 **Built for Kenya, Ready for the World**

Palsa POS is specifically designed for the Kenyan market but built with global standards. Whether you're running a small shop in Nairobi or a large retail chain, Palsa POS scales with your business.

**Start your digital transformation today!** 🚀

---

**Made with ❤️ in Kenya**