# 📱 M-Pesa Integration Guide for Palsa POS

## 🚀 **M-Pesa Integration is Ready!**

Your Palsa POS system now includes complete M-Pesa integration with STK Push (Lipa na M-Pesa Online) functionality.

---

## 📋 **What You Need from Safaricom**

### **1. M-Pesa Developer Account**
- Visit: https://developer.safaricom.co.ke/
- Create an account and verify your identity
- Apply for M-Pesa API access

### **2. Required Credentials**
You'll receive these from Safaricom:
- **Consumer Key** - Your app's public identifier
- **Consumer Secret** - Your app's private key
- **Shortcode** - Your business number (Paybill or Till)
- **Passkey** - Security key for STK Push

### **3. Business Registration**
- Valid business registration certificate
- KRA PIN certificate
- Business permit
- Bank account details

---

## ⚙️ **Configuration Setup**

### **Step 1: Update Environment Variables**
Add these to your `.env` file:

```bash
# M-Pesa Configuration
MPESA_CONSUMER_KEY=your_actual_consumer_key
MPESA_CONSUMER_SECRET=your_actual_consumer_secret
MPESA_SHORTCODE=your_actual_shortcode
MPESA_PASSKEY=your_actual_passkey
MPESA_ENV=sandbox  # Change to 'production' when ready
MPESA_CALLBACK_URL=https://yourdomain.com/api/mpesa/callback
```

### **Step 2: Test Configuration**
```bash
# Test M-Pesa setup
curl -X GET http://localhost:8000/api/mpesa/test-config
```

---

## 🔄 **How M-Pesa Integration Works**

### **Payment Flow:**
1. **Customer places order** → Order created with PENDING status
2. **Customer chooses M-Pesa** → STK Push initiated
3. **Customer enters M-Pesa PIN** → Payment processed
4. **M-Pesa sends callback** → Order status updated to CONFIRMED
5. **Receipt generated** → Customer and admin notified

### **Technical Flow:**
```
Customer Order → STK Push Request → M-Pesa Prompt → 
Customer PIN → Payment Confirmation → Callback → 
Order Confirmation → Receipt Generation
```

---

## 🛠️ **API Endpoints**

### **Initiate Payment**
```http
POST /api/mpesa/initiate-payment
Content-Type: application/json

{
    "order_code": "ORD-2024-001",
    "phone_number": "254712345678"
}
```

### **Check Payment Status**
```http
POST /api/mpesa/check-status
Content-Type: application/json

{
    "checkout_request_id": "ws_CO_191220191020363925"
}
```

### **Test Configuration**
```http
GET /api/mpesa/test-config
```

---

## 📱 **Customer Experience**

### **Web Application Flow:**
1. Customer adds items to cart
2. Proceeds to checkout
3. Selects M-Pesa payment
4. Enters phone number
5. Receives STK Push on phone
6. Enters M-Pesa PIN
7. Gets confirmation and receipt

### **Mobile App Flow (API):**
1. App calls `/api/mpesa/initiate-payment`
2. Shows "Check your phone" message
3. Polls `/api/mpesa/check-status` for updates
4. Shows success/failure message
5. Displays receipt

---

## 🔧 **Admin Features**

### **Payment Monitoring:**
- Real-time payment status tracking
- Failed payment notifications
- Payment reconciliation reports
- M-Pesa transaction logs

### **Configuration Management:**
- Test M-Pesa connectivity
- View transaction statistics
- Configure callback URLs
- Monitor payment success rates

---

## 🧪 **Testing Guide**

### **Sandbox Testing:**
1. Use Safaricom sandbox credentials
2. Test phone number: `254708374149`
3. Test PIN: `1234`
4. All transactions are simulated

### **Test Scenarios:**
```bash
# Successful payment
curl -X POST http://localhost:8000/api/mpesa/initiate-payment \
  -H "Content-Type: application/json" \
  -d '{"order_code":"ORD-TEST-001","phone_number":"254708374149"}'

# Check status
curl -X POST http://localhost:8000/api/mpesa/check-status \
  -H "Content-Type: application/json" \
  -d '{"checkout_request_id":"ws_CO_191220191020363925"}'
```

---

## 🚨 **Security Features**

### **Built-in Security:**
- ✅ Request validation and sanitization
- ✅ Secure credential storage
- ✅ Callback URL verification
- ✅ Transaction logging and monitoring
- ✅ Error handling and recovery
- ✅ Rate limiting on payment endpoints

### **Production Security:**
- Use HTTPS for all callbacks
- Validate callback authenticity
- Monitor for suspicious activity
- Regular credential rotation
- Secure environment variables

---

## 📊 **Monitoring & Analytics**

### **Payment Metrics:**
- Success/failure rates
- Average transaction time
- Peak usage periods
- Customer payment preferences
- Revenue tracking

### **Alerts & Notifications:**
- Failed payment alerts
- High transaction volume warnings
- Service downtime notifications
- Daily payment summaries

---

## 🔄 **Production Deployment**

### **Pre-Production Checklist:**
- [ ] Safaricom production credentials obtained
- [ ] Business verification completed
- [ ] SSL certificate installed
- [ ] Callback URLs configured
- [ ] Testing completed successfully
- [ ] Monitoring setup configured

### **Go-Live Steps:**
1. Update `.env` with production credentials
2. Change `MPESA_ENV=production`
3. Update callback URLs to production domain
4. Test with small transaction
5. Monitor for 24 hours
6. Full rollout

---

## 🆘 **Troubleshooting**

### **Common Issues:**

**"Invalid credentials"**
- Verify consumer key/secret
- Check environment (sandbox vs production)
- Ensure credentials are for correct environment

**"STK Push failed"**
- Verify phone number format (254XXXXXXXXX)
- Check shortcode and passkey
- Ensure customer has sufficient balance

**"Callback not received"**
- Verify callback URL is accessible
- Check firewall settings
- Ensure HTTPS is working

**"Payment timeout"**
- Customer didn't enter PIN in time
- Network connectivity issues
- M-Pesa service temporary unavailable

### **Debug Commands:**
```bash
# Check M-Pesa configuration
docker-compose exec app php artisan tinker --execute="dd(config('mpesa'));"

# View M-Pesa logs
docker-compose exec app tail -f storage/logs/laravel.log | grep -i mpesa

# Test connectivity
curl -X GET http://localhost:8000/api/mpesa/test-config
```

---

## 📞 **Support Contacts**

### **Safaricom M-Pesa Support:**
- Email: apisupport@safaricom.co.ke
- Phone: +254 722 000 000
- Portal: https://developer.safaricom.co.ke/

### **Integration Support:**
- Check logs in `storage/logs/laravel.log`
- Monitor callback responses
- Use test endpoints for debugging

---

## 🎉 **You're All Set!**

Your Palsa POS system now has enterprise-grade M-Pesa integration that handles:
- ✅ Secure payment processing
- ✅ Real-time status updates
- ✅ Automatic order confirmation
- ✅ Receipt generation
- ✅ Error handling and recovery
- ✅ Comprehensive logging

**Just add your Safaricom credentials and you're ready to accept M-Pesa payments!**