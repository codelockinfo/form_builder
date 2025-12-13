# Fix Form Loading Issue - Steps

## 🔍 Issues Found

1. **Invalid Form ID**: The form_id field had a hash value instead of numeric ID
2. **Missing Error Handling**: No console logging to debug issues
3. **No Validation**: Form ID wasn't validated as a number

## ✅ Fixes Applied

1. ✅ Added form ID validation (must be numeric)
2. ✅ Added console logging for debugging
3. ✅ Added better error messages
4. ✅ Clear invalid form IDs automatically
5. ✅ Validate forms list response

## 🚀 Next Steps

### Step 1: Deploy Updated Extension

```bash
shopify app deploy
```

Select: `form-builder-block`

### Step 2: Clear Invalid Form ID

In Theme Customizer:
1. Click on the **Easy Form Builder** block
2. In the right sidebar, **clear the Form ID field** (delete the hash value)
3. **Select a form from the dropdown** that appears in the block preview
4. The form ID will be set automatically

### Step 3: Test App Proxy Endpoints

Test these URLs directly in your browser (replace YOUR-STORE):

**List Forms:**
```
https://YOUR-STORE.myshopify.com/apps/easy-form-builder/list?shop=YOUR-STORE.myshopify.com
```

Should return:
```json
[{"id":1,"name":"Contact Form"},{"id":2,"name":"Feedback Form"}]
```

**Render Form:**
```
https://YOUR-STORE.myshopify.com/apps/easy-form-builder/render?form_id=1&shop=YOUR-STORE.myshopify.com
```

Should return: HTML of the form

### Step 4: Check Browser Console

1. Open Theme Customizer
2. Open browser Developer Tools (F12)
3. Go to **Console** tab
4. Look for:
   - "Loading forms list for shop: ..."
   - "Forms list received: ..."
   - Any error messages

### Step 5: Verify Forms Exist

Make sure you have forms in your database:
```sql
SELECT id, form_name, status FROM forms WHERE status = 1;
```

Forms must have `status = 1` to appear in the list.

## 🐛 Troubleshooting

### Issue: Forms list is empty

**Check:**
1. Forms exist in database with `status = 1`
2. App Proxy is configured correctly
3. Test the `/apps/easy-form-builder/list` endpoint directly
4. Check PHP error logs

### Issue: Form ID is still invalid

**Solution:**
1. Clear the Form ID field in Theme Customizer
2. Select a form from the dropdown
3. The ID will be set automatically as a number

### Issue: "Failed to load form"

**Check:**
1. Form ID is a valid number
2. Form exists in database
3. App Proxy endpoint is working
4. Check browser console for detailed errors
5. Check PHP error logs

### Issue: App Proxy returns 404

**Check:**
1. App Proxy is configured in Partner Dashboard
2. Subpath matches: `easy-form-builder`
3. Proxy URL is correct: `https://codelocksolutions.com/form_builder/shopify/app-proxy.php`
4. `.htaccess` routing is working

## 📝 After Deploying

1. **Refresh Theme Customizer** (hard refresh: Ctrl+F5)
2. **Clear the invalid Form ID**
3. **Select a form from dropdown**
4. **Check browser console** for any errors
5. **Form should load** in preview

---

**Deploy the updated extension and test again!**

