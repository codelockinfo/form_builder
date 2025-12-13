# Troubleshoot Form Loading - Step by Step

## 🔍 Current Issue

Form shows "Loading form..." but never loads. This means:
- Forms list dropdown isn't appearing
- App Proxy might not be working
- Forms might not exist in database

## ✅ Step 1: Check Browser Console (MOST IMPORTANT!)

1. In Theme Customizer, press **F12** to open Developer Tools
2. Go to **Console** tab
3. Look for these messages:
   - "Loading forms list for shop: ..."
   - "Forms list response status: ..."
   - "Forms list received: ..."
   - Any **red error messages**

**What to look for:**
- If you see "Failed to load forms: 404" → App Proxy not configured
- If you see "Failed to load forms: 500" → Server error, check PHP logs
- If you see "No forms found" → Forms don't exist in database
- If you see CORS errors → Headers issue

## ✅ Step 2: Test App Proxy Endpoint Directly

Open this URL in a new browser tab (replace YOUR-STORE):

```
https://YOUR-STORE.myshopify.com/apps/easy-form-builder/list?shop=YOUR-STORE.myshopify.com
```

**Expected Results:**

✅ **Success** - You see:
```json
[{"id":1,"name":"Contact Form"},{"id":2,"name":"Feedback Form"}]
```

❌ **404 Not Found** - App Proxy not configured:
- Go to Partner Dashboard → App Setup → App Proxy
- Verify subpath is `easy-form-builder`
- Verify Proxy URL is correct

❌ **500 Error** - Server error:
- Check PHP error logs
- Check database connection
- Verify shop exists in database

❌ **Empty Array `[]`** - No forms:
- Create forms in your app
- Verify forms have `status = 1`

## ✅ Step 3: Verify App Proxy Configuration

In Partner Dashboard:
1. Go to: **Easy Form Builder & Email** → **App Setup** → **App Proxy**
2. Verify these settings:
   - **Subpath prefix**: `apps`
   - **Subpath**: `easy-form-builder` (must match exactly!)
   - **Proxy URL**: `https://codelocksolutions.com/form_builder/shopify/app-proxy.php`

3. If not configured, click **Configure** and set them

## ✅ Step 4: Check Forms in Database

Run this SQL query:
```sql
SELECT id, form_name, status, store_client_id 
FROM forms 
WHERE status = 1;
```

**Requirements:**
- At least one form must exist
- `status` must be `1` (active)
- `store_client_id` must match the store's user ID

## ✅ Step 5: Check PHP Error Logs

Check your error logs:
- `error_log` file in project root
- Server error logs
- Look for errors from `app-proxy.php`

The updated code now logs:
- Shop parameter
- Store user ID
- Forms query results
- Any errors

## ✅ Step 6: Verify Shop is Installed

Make sure the app is installed on the store:
1. Go to: **Shopify Admin** → **Apps**
2. Look for: **Easy Form Builder & Email**
3. If not installed, install it first

## 🔧 Quick Fixes

### Fix 1: App Proxy Not Working

If you get 404 when testing the endpoint:
1. Check App Proxy is configured in Partner Dashboard
2. Wait a few minutes after configuring (can take time to propagate)
3. Try the endpoint again

### Fix 2: No Forms Found

If you get empty array `[]`:
1. Go to your app: `https://codelocksolutions.com/form_builder/user/?store=YOUR-STORE`
2. Create a form
3. Make sure form status is active
4. Test the endpoint again

### Fix 3: Store Not Found

If you get "Store not found":
1. Verify the shop is installed in your app
2. Check `user_shops` table has the store
3. Verify `store_client_id` matches

## 📝 What to Report

When asking for help, provide:
1. **Browser console errors** (screenshot or copy text)
2. **App Proxy endpoint test result** (what you see when testing the URL)
3. **Forms count** from database query
4. **App Proxy configuration** (screenshot if possible)

---

**Start with Step 1: Check Browser Console - this will tell you exactly what's wrong!**

