# Render Deployment Guide - Palsa POS System

## 🚀 Deploy to Render (Recommended Alternative to Railway)

Render is often more reliable for Laravel applications and provides excellent PHP support.

### **Why Render?**
- ✅ **Better Laravel Support** - Native PHP environment
- ✅ **More Reliable** - Less deployment issues
- ✅ **Easier Database Setup** - Managed MySQL included
- ✅ **Better Logging** - Clearer error messages
- ✅ **Free Tier Available** - Great for testing

## 📋 **Deployment Steps**

### **1. Create Render Account**
1. Go to [render.com](https://render.com)
2. Sign up with GitHub
3. Connect your GitHub account

### **2. Manual Setup (Recommended)**

#### **Step 1: Create Database First**
1. **New +** → **"PostgreSQL"** (MySQL not available on free tier)
2. **Name**: `palsa-pos-db`
3. **Database Name**: `palsa_pos`
4. **User**: `palsa_user`
5. **Region**: Choose closest to you
6. **Plan**: Free tier
7. **Create Database**

#### **Step 2: Create Web Service**
1. **New +** → **"Web Service"**
2. **Connect Repository**: Select `PALSA-POS`
3. **Settings**:
   - **Name**: `palsa-pos`
   - **Runtime**: `Docker`
   - **Dockerfile Path**: `./Dockerfile.render`
   - **Build Command**: 
     ```bash
     composer install --no-dev --optimize-autoloader
     ```
   - **Start Command**: 
     ```bash
     php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
     ```

### **4. Environment Variables**
In your web service settings, add these environment variables:

**Required Variables:**
```
APP_NAME=Palsa POS
APP_ENV=production
APP_KEY=base64:C+kiffgn6xdlxsBUdYno57+AhYtdrqUFLSn1Zso0lcA=
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
LOG_CHANNEL=stack
LOG_LEVEL=error
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

**Database Variables (from your PostgreSQL service):**
```
DB_CONNECTION=pgsql
DB_HOST=[Your PostgreSQL Host]
DB_PORT=[Your PostgreSQL Port]
DB_DATABASE=[Your Database Name]
DB_USERNAME=[Your PostgreSQL User]
DB_PASSWORD=[Your PostgreSQL Password]
```

**Optional M-Pesa Variables:**
```
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_passkey
MPESA_ENV=sandbox
```

### **5. Deploy**
1. Click **"Create Web Service"**
2. Render will build and deploy automatically
3. Wait for deployment to complete (5-10 minutes)

## 🌐 **Your Application URLs**

After deployment:
- **Main App**: `https://your-app-name.onrender.com`
- **Health Check**: `https://your-app-name.onrender.com/health`
- **Test PHP**: `https://your-app-name.onrender.com/test.php`
- **Database Test**: `https://your-app-name.onrender.com/test-db`

## 🧪 **Testing Your Deployment**

### **1. Basic Tests**
- **Health Check**: Should return JSON with status "ok"
- **PHP Test**: Should show PHP version and server info
- **Database**: Should connect successfully

### **2. Application Tests**
- **Login**: Test authentication system
- **Dashboard**: Verify data loading
- **POS**: Test product management
- **API**: Test API endpoints

## 🔧 **Render Advantages Over Railway**

### **Better for Laravel:**
- ✅ **Native PHP Support** - Optimized for PHP applications
- ✅ **Built-in Artisan** - Laravel commands work perfectly
- ✅ **Better Error Handling** - Clearer error messages
- ✅ **Persistent Storage** - File sessions and cache work reliably

### **Easier Database:**
- ✅ **Managed MySQL** - No complex setup required
- ✅ **Automatic Backups** - Built-in database backups
- ✅ **Connection Pooling** - Better performance
- ✅ **Easy Scaling** - Upgrade database easily

### **Better Debugging:**
- ✅ **Live Logs** - Real-time application logs
- ✅ **Shell Access** - Debug directly on server
- ✅ **Environment Inspector** - Check variables easily
- ✅ **Build Logs** - Clear deployment process

## 🔍 **Troubleshooting**

### **Common Issues:**

#### **Build Fails:**
- Check composer.json syntax
- Verify PHP version compatibility
- Check for missing dependencies

#### **Database Connection:**
- Verify database service is running
- Check environment variables
- Test connection with `/test-db` endpoint

#### **Application Errors:**
- Check Render logs in dashboard
- Test individual endpoints
- Verify file permissions

### **Debug Commands:**
```bash
# In Render shell (if available)
php artisan --version
php artisan config:show
php artisan route:list
```

## 📊 **Performance Benefits**

### **Render vs Railway:**
- ✅ **Faster Cold Starts** - PHP applications start quicker
- ✅ **Better Memory Management** - More efficient resource usage
- ✅ **Persistent Connections** - Database connections stay alive
- ✅ **CDN Integration** - Static assets served faster

## 💰 **Pricing**

### **Free Tier:**
- ✅ **Web Service** - 750 hours/month (enough for testing)
- ✅ **MySQL Database** - 1GB storage
- ✅ **Custom Domain** - Free SSL certificates
- ✅ **GitHub Integration** - Automatic deployments

### **Paid Plans:**
- **Starter**: $7/month - Production ready
- **Standard**: $25/month - High performance
- **Pro**: $85/month - Enterprise features

## 🎯 **Next Steps After Deployment**

1. **Test All Features** - Verify complete functionality
2. **Configure M-Pesa** - Add payment credentials
3. **Set Up Monitoring** - Enable error tracking
4. **Custom Domain** - Add your own domain
5. **SSL Certificate** - Automatic HTTPS
6. **Backup Strategy** - Regular database backups

---

## 🎉 **Success!**

Your Palsa POS system will be running on Render with:
- **Complete Laravel Application**
- **MySQL Database**
- **M-Pesa Integration**
- **Kenya Shillings Support**
- **Production-Ready Performance**

Render typically provides a much smoother deployment experience for Laravel applications compared to Railway! 🚀