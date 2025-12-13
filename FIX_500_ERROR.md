# Fix 500 Error - App Proxy Not Working

## 🔍 Problem Identified

The console shows:
- **500 Internal Server Error** on `/apps/easy-form-builder/list`
- Response is **HTML** (theme page) instead of **JSON**
- This means the request isn't reaching your PHP handler

## ✅ Fixes Applied

1. ✅ Updated `index.php` to handle `easy-form-builder` subpath
2. ✅ Updated `.htaccess` to route `easy-form-builder` requests
3. ✅ Added better error handling in `app-proxy.php`
4. ✅ Added store validation

## 🚀 Next Steps

### Step 1: Verify Store is Installed

The 500 error might mean the store isn't registered in your database. Check:

```sql
SELECT * FROM user_shops WHERE shop_name = 'dashboardmanage.myshopify.com';
```

**If no results:**
- The app needs to be installed on the store first
- Go to: `https://dashboardmanage.myshopify.com/admin/oauth/authorize?client_id=YOUR_CLIENT_ID&scope=read_content,write_content&redirect_uri=https://codelocksolutions.com/form_builder/index.php`

### Step 2: Test App Proxy Directly

Test this URL in browser (replace with your store):
```
https://dashboardmanage.myshopify.com/apps/easy-form-builder/list?shop=dashboardmanage.myshopify.com
```

**Expected:**
- JSON response: `[{"id":1,"name":"Form Name"}]`
- OR error JSON: `{"error":"Store not found",...}`

**If you still get HTML:**
- Routing still not working
- Check `.htaccess` is being processed
- Check `index.php` routing

### Step 3: Check PHP Error Logs

Check your error logs for:
- `error_log` file in project root
- Server error logs
- Look for "App Proxy - List Forms Request" messages

### Step 4: Verify App Proxy Configuration

In Partner Dashboard:
- **Subpath**: `easy-form-builder` (must match exactly)
- **Proxy URL**: `https://codelocksolutions.com/form_builder/shopify/app-proxy.php`

## 🔧 Common Issues

### Issue: Store Not Found

**Error**: `{"error":"Store not found"}`

**Solution:**
1. Install app on the store
2. Or manually add store to `user_shops` table

### Issue: Still Getting HTML Response

**Solution:**
1. Check `.htaccess` is enabled on server
2. Try accessing directly: `https://codelocksolutions.com/form_builder/shopify/app-proxy.php?shop=dashboardmanage.myshopify.com`
3. Should return: `{"error":"Route not found"}` (confirms file is accessible)

### Issue: Routing Not Working

**Solution:**
1. Check Apache mod_rewrite is enabled
2. Verify `.htaccess` file exists and is readable
3. Test with direct URL to app-proxy.php

## 📝 Testing Checklist

- [ ] Store exists in `user_shops` table
- [ ] App Proxy configured in Partner Dashboard
- [ ] `.htaccess` routing updated
- [ ] `index.php` routing updated
- [ ] Test App Proxy endpoint directly
- [ ] Check PHP error logs
- [ ] Refresh Theme Customizer

---

**Most Likely Issue**: Store `dashboardmanage.myshopify.com` is not installed/registered in your app database.

**Check this first**: Run SQL query to see if store exists!

