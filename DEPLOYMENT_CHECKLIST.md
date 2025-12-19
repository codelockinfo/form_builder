# Deployment Checklist - OAuth Fix

## Files That Need to Be Uploaded to Live Server

The following files have been fixed and need to be uploaded to your live server:

### 1. `cls_shopifyapps/cls_shopify.php`
**Changes:**
- Fixed `curlParseHeaders()` function to safely parse HTTP headers (line 140-165)
- Fixed `curlHttpApiRequest()` to safely split headers and body (line 89-107)
- Added error logging to `getEntrypassword()` function (line 24-50)

**Why:** The old code was trying to access array index 2 that might not exist, causing "Undefined array key 2" error.

### 2. `index.php`
**Changes:**
- Enhanced OAuth callback error handling
- Added comprehensive logging throughout OAuth flow
- Fixed shop parameter extraction

**Why:** Better debugging and error handling for OAuth flow.

### 3. `collection/mongo_mysql/mysql/common_function.php`
**Changes:**
- Fixed `registerNewClientApi()` to properly decode JSON from `post_data()`
- Added extensive logging for store registration
- Added verification step after store creation

**Why:** The `post_data()` function returns JSON string, not array. Need to decode it first.

## Upload Instructions

1. **Upload these files via FTP/SFTP:**
   - `cls_shopifyapps/cls_shopify.php`
   - `index.php`
   - `collection/mongo_mysql/mysql/common_function.php`

2. **Verify file permissions:**
   - Files should be readable (644 or 755)
   - Make sure PHP can execute them

3. **Test the installation:**
   - Try installing the app on a test store
   - Check `oauth-debug.log` for detailed logs
   - Verify store is saved to database

## Expected Behavior After Upload

After uploading the fixed files:

1. **OAuth Callback should work:**
   - No more "Undefined array key 2" error
   - Access token should be obtained successfully
   - Store should be registered in database

2. **Logs will show:**
   ```
   getEntrypassword: Requesting access token for shop: ...
   getEntrypassword: Raw response: ...
   getEntrypassword: Successfully obtained access token
   OAuth Callback: Store successfully registered: ...
   ```

3. **Store registration:**
   - Store will be saved to `user_shops` table
   - `store_user_id` will be generated
   - Store will be accessible via app

## Troubleshooting

If issues persist after upload:

1. **Check error logs:**
   - `oauth-debug.log` in project root
   - PHP error logs on server

2. **Verify file upload:**
   - Make sure files were uploaded completely
   - Check file modification dates

3. **Clear any caches:**
   - OPcache (if enabled)
   - Any PHP accelerators

4. **Test manually:**
   - Use `/shopify/test-store-registration.php?shop=test-store.myshopify.com`
   - Check database directly

## Current Status

✅ **Fixed locally** - All fixes have been applied to local files
⏳ **Pending upload** - Files need to be uploaded to live server
🔍 **Ready for testing** - After upload, test OAuth flow

