# Debug Form Loading Issue

## 🔍 Current Status

- ✅ Block is added to Theme Customizer
- ✅ Form ID is set to "0" (correct default)
- ❌ Forms dropdown not appearing
- ❌ Form showing "Loading form..." indefinitely

## 🐛 Likely Issues

### Issue 1: Forms List Not Loading

The dropdown should appear in Theme Customizer (design mode), but it's not showing. This means:
- App Proxy `/apps/easy-form-builder/list` endpoint might not be working
- Forms list fetch is failing
- No forms exist in database

### Issue 2: App Proxy Configuration

Check if App Proxy is properly configured in Partner Dashboard.

## ✅ Step-by-Step Debugging

### Step 1: Check Browser Console

1. In Theme Customizer, open **Developer Tools** (F12)
2. Go to **Console** tab
3. Look for:
   - "Loading forms list for shop: ..."
   - Any error messages
   - Network errors

### Step 2: Test App Proxy Endpoint Directly

Test this URL in your browser (replace YOUR-STORE with your actual store domain):

```
https://YOUR-STORE.myshopify.com/apps/easy-form-builder/list?shop=YOUR-STORE.myshopify.com
```

**Expected Response:**
```json
[{"id":1,"name":"Contact Form"},{"id":2,"name":"Feedback Form"}]
```

**If you get 404 or error:**
- App Proxy not configured correctly
- Check Partner Dashboard → App Setup → App Proxy

**If you get empty array `[]`:**
- No forms in database with `status = 1`
- Create forms in your app first

### Step 3: Verify App Proxy Configuration

In Partner Dashboard:
1. Go to: **Easy Form Builder & Email** → **App Setup** → **App Proxy**
2. Verify:
   - **Subpath prefix**: `apps`
   - **Subpath**: `easy-form-builder`
   - **Proxy URL**: `https://codelocksolutions.com/form_builder/shopify/app-proxy.php`

### Step 4: Check Forms in Database

Run this SQL query:
```sql
SELECT id, form_name, status, store_client_id 
FROM forms 
WHERE status = 1;
```

Make sure:
- Forms exist
- `status = 1` (active)
- `store_client_id` matches the store's user ID

### Step 5: Check PHP Error Logs

Check your PHP error logs for any errors from `app-proxy.php`:
- Check `error_log` file in your project
- Check server error logs
- Look for database connection errors
- Look for missing function errors

### Step 6: Verify Shop Parameter

The App Proxy needs the `shop` parameter. Check if it's being passed correctly:
- In browser console, check the fetch URL
- Should include: `?shop=YOUR-STORE.myshopify.com`

## 🔧 Quick Fixes to Try

### Fix 1: Add Error Display in Block

The block should show errors if forms list fails. Check if error message appears.

### Fix 2: Check CORS/Headers

App Proxy might need specific headers. Check if your PHP is sending correct headers.

### Fix 3: Test with Direct URL

Try accessing the App Proxy directly:
```
https://codelocksolutions.com/form_builder/shopify/app-proxy.php?shop=YOUR-STORE.myshopify.com
```

Should return an error (route not found) but confirms the file is accessible.

## 📝 What to Check First

1. **Browser Console** - Most important! Check for JavaScript errors
2. **App Proxy Endpoint** - Test the `/apps/easy-form-builder/list` URL directly
3. **Forms in Database** - Verify forms exist with status = 1
4. **App Proxy Config** - Verify in Partner Dashboard

---

**Start with Step 1: Check Browser Console for errors!**

