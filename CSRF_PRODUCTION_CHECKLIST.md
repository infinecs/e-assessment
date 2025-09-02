# CSRF Production Deployment Checklist

## ✅ CSRF Protection Status
Your application already has comprehensive CSRF protection implemented!

## 🔧 Production Environment Configuration

### 1. Environment Variables (.env)
Ensure these are properly set in production:

```env
# Session Configuration
SESSION_DRIVER=file  # or redis/database for scaled deployments
SESSION_LIFETIME=120  # in minutes
SESSION_SECURE_COOKIE=true  # if using HTTPS
SESSION_DOMAIN=.yourdomain.com  # match your domain
SESSION_SAME_SITE=lax

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com  # must match actual domain
```

### 2. Session Storage Recommendations

#### For Single Server:
- `SESSION_DRIVER=file` (default) - OK for single server

#### For Load Balanced / Multiple Servers:
- `SESSION_DRIVER=redis` or `SESSION_DRIVER=database`
- Configure Redis/Database connection properly
- Ensure sticky sessions on load balancer OR shared session storage

### 3. HTTPS Configuration
If using HTTPS (recommended for production):
```env
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### 4. Server Configuration

#### Nginx Example:
```nginx
# Ensure proper session handling
location ~ \.php$ {
    # ... other config
    fastcgi_param PHP_VALUE "session.cookie_secure=1";  # for HTTPS
    fastcgi_param PHP_VALUE "session.cookie_httponly=1";
    fastcgi_param PHP_VALUE "session.cookie_samesite=Lax";
}
```

#### Apache Example:
```apache
# In .htaccess or virtual host
php_value session.cookie_secure 1
php_value session.cookie_httponly 1
php_value session.cookie_samesite Lax
```

### 5. Application Configuration

#### Cache Configuration:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Session Table (if using database sessions):
```bash
php artisan session:table
php artisan migrate
```

## 🚨 Common Production Issues & Solutions

### Issue 1: 419 Errors on Form Submission
**Cause**: Session/cookie domain mismatch
**Solution**: 
- Check `SESSION_DOMAIN` matches your actual domain
- Verify `APP_URL` is correct
- Clear browser cookies and test

### Issue 2: CSRF Errors After Deployment
**Cause**: Cached configuration with old values
**Solution**:
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Issue 3: Load Balancer Issues
**Cause**: Sessions not shared between servers
**Solution**:
- Use shared session storage (Redis/Database)
- OR configure sticky sessions on load balancer

### Issue 4: CDN Caching Issues
**Cause**: CSRF tokens being cached by CDN
**Solution**:
- Exclude `/csrf-token` endpoint from CDN caching
- Add cache-control headers for dynamic content

## 🔄 CSRF Token Refresh Implementation

Your application now includes:

1. **Automatic token refresh every 10 minutes** (reduced from 30 for production stability)
2. **Global CSRF error handler** in `public/js/csrf-handler.js`
3. **Enhanced error handling** with user-friendly messages
4. **Automatic retry mechanism** for failed CSRF requests

## 📊 Monitoring & Debugging

### Enable Logging:
Add to your controller or middleware:
```php
Log::info('CSRF Token Status', [
    'token' => session()->token(),
    'user_id' => auth()->id(),
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent()
]);
```

### Check Session Status:
```php
// In a test route
Route::get('/session-debug', function() {
    return [
        'session_id' => session()->getId(),
        'csrf_token' => session()->token(),
        'driver' => config('session.driver'),
        'lifetime' => config('session.lifetime'),
        'secure' => config('session.secure'),
        'domain' => config('session.domain')
    ];
});
```

## ✅ Deployment Steps

1. **Update environment variables** in production `.env`
2. **Clear and cache configuration**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   ```
3. **Test CSRF functionality** after deployment
4. **Monitor logs** for any 419 errors
5. **Verify session storage** is working correctly

## 🔐 Security Notes

- Never expose CSRF tokens in URLs or GET parameters
- Always use HTTPS in production
- Regularly rotate APP_KEY in production
- Monitor for unusual 419 error patterns
- Consider implementing rate limiting for CSRF endpoints

## 📱 Testing Checklist

- [ ] Login form works without 419 errors
- [ ] Registration form works without 419 errors  
- [ ] Quiz submission works without 419 errors
- [ ] Admin forms (category, topic, question, user, events) work without 419 errors
- [ ] AJAX requests include CSRF tokens
- [ ] Token refresh functionality works
- [ ] Error handling displays appropriate messages
- [ ] Sessions persist correctly across requests
