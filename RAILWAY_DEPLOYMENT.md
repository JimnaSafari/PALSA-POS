# Railway Deployment - Complete Palsa POS System

## 🚀 Deploy Full-Stack Laravel POS to Railway

Your Laravel application includes both the backend API and frontend Blade templates, making it a complete full-stack solution that can be deployed as a single service on Railway.

### **What You're Deploying:**
- ✅ Laravel Backend (API + Admin Panel)
- ✅ Blade Frontend (POS Interface + Dashboard)
- ✅ MySQL Database
- ✅ M-Pesa Integration
- ✅ Kenya Shillings Support

## 📋 **Deployment Steps**

### **1. Create Railway Project**
1. Go to [railway.app](https://railway.app)
2. Sign in with GitHub
3. Click **"New Project"**
4. Select **"Deploy from GitHub repo"**
5. Choose your **`PALSA-POS`** repository

### **2. Add MySQL Database**
1. In your Railway project dashboard
2. Click **"+ New"** → **"Database"** → **"Add MySQL"**
3. Railway will auto-generate database credentials

### **3. Configure Environment Variables**
In Railway dashboard, go to your Laravel service → **Variables** tab:

**Required Variables:**
```
APP_KEY=base64:C+kiffgn6xdlxsBUdYno57+AhYtdrqUFLSn1Zso0lcA=
APP_ENV=production
APP_DEBUG=false
RAILWAY_STATIC_URL=${{RAILWAY_STATIC_URL}}
```

**M-Pesa Variables (Optional):**
```
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_passkey
```

**Database Variables:**
These are automatically set by Railway when you add MySQL:
- `MYSQLHOST` ✅ (Auto-generated)
- `MYSQLPORT` ✅ (Auto-generated)
- `MYSQLDATABASE` ✅ (Auto-generated)
- `MYSQLUSER` ✅ (Auto-generated)
- `MYSQLPASSWORD` ✅ (Auto-generated)

### **4. Deploy**
Railway will automatically:
1. Use `Dockerfile.railway` for deployment
2. Install dependencies
3. Run database migrations
4. Start the application

### **5. Access Your Application**
After deployment, you'll get a Railway URL like:
`https://your-app-name.railway.app`

## 🌐 **Application URLs**

### **Frontend (Blade Templates):**
- **Login**: `https://your-app.railway.app/auth/login`
- **Dashboard**: `https://your-app.railway.app/dashboard`
- **Admin Panel**: `https://your-app.railway.app/admin`

### **API Endpoints:**
- **Products**: `https://your-app.railway.app/api/products`
- **Orders**: `https://your-app.railway.app/api/orders`
- **M-Pesa**: `https://your-app.railway.app/api/mpesa/stk-push`
- **Health Check**: `https://your-app.railway.app/health`

## 🧪 **Testing Your Deployment**

### **1. Health Check**
Visit: `https://your-app.railway.app/health`
Should return:
```json
{
  "status": "ok",
  "timestamp": "2024-12-19T10:30:00.000000Z",
  "app": "Palsa POS",
  "version": "1.0.0"
}
```

### **2. Frontend Access**
- Visit: `https://your-app.railway.app`
- Should redirect to login page
- Test login with your credentials

### **3. API Testing**
- Test: `https://your-app.railway.app/api/products`
- Should return JSON product data

## 🔧 **Features Available**

### **Admin Features:**
- ✅ Product Management
- ✅ Category Management
- ✅ Order Management
- ✅ User Management
- ✅ Sales Reports
- ✅ Inventory Tracking

### **POS Features:**
- ✅ Point of Sale Interface
- ✅ Product Search & Selection
- ✅ Shopping Cart
- ✅ Multiple Payment Methods
- ✅ Receipt Generation
- ✅ Real-time Stock Updates

### **Payment Integration:**
- ✅ Cash Payments
- ✅ M-Pesa STK Push
- ✅ Card Payments
- ✅ Payment Status Tracking

### **Kenyan Localization:**
- ✅ Kenya Shillings (KSh) Currency
- ✅ 16% VAT Calculation
- ✅ Kenyan Phone Number Format
- ✅ Nairobi Timezone

## 🔐 **Default Credentials**

After deployment, you can create admin users or use seeded data:
- **Email**: admin@example.com
- **Password**: password

## 📊 **Monitoring & Maintenance**

### **Railway Dashboard:**
- Monitor application logs
- Check resource usage
- View deployment history
- Manage environment variables

### **Application Logs:**
- Access via Railway dashboard
- Monitor errors and performance
- Track user activities

## 🚀 **Production Ready Features**

### **Security:**
- ✅ HTTPS enabled by default
- ✅ CSRF protection
- ✅ SQL injection prevention
- ✅ XSS protection

### **Performance:**
- ✅ Database query optimization
- ✅ Caching enabled
- ✅ Asset optimization
- ✅ CDN ready

### **Scalability:**
- ✅ Database connection pooling
- ✅ Queue system ready
- ✅ Horizontal scaling support

## 🎯 **Next Steps**

1. **Test all functionality**
2. **Configure M-Pesa credentials**
3. **Add your products and categories**
4. **Train your staff**
5. **Go live!**

---

## 🎉 **Success!**

Your complete Palsa POS system is now live on Railway!

**Single URL for everything:**
`https://your-app-name.railway.app`

This includes:
- Complete POS system
- Admin dashboard
- API endpoints
- M-Pesa payments
- Kenya Shillings support
- Production-ready deployment

Perfect for Kenyan businesses! 🇰🇪