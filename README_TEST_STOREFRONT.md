# Test Storefront - Form Builder Testing Tool

## Overview
This is a local testing page that simulates a Shopify storefront environment. Use it to test your forms before deploying to Hostinger.

## How to Use

### 1. Open the Test Page
- Open `test_storefront.html` in your web browser
- Or access it via: `http://localhost/form_builder/test_storefront.html`

### 2. Configure Settings
- **Shop Domain**: Enter your Shopify store domain (e.g., `cls-rakshita.myshopify.com`)
- **Form ID**: Enter the 6-digit Public ID of your form
- **Base URL**: Select the appropriate base URL:
  - **Production (Hostinger)**: For testing against live server
  - **Local (XAMPP)**: For testing against local XAMPP server
  - **Local (127.0.0.1)**: Alternative local testing

### 3. Load Form
- Click "Load Form" button
- The form will be fetched from the server and displayed
- Check the Debug Console for detailed logs

### 4. Test Form Submission
- Fill out the form fields
- Click the Submit button
- Watch the Debug Console for submission logs
- Check if data is saved to database

## Features

### Debug Console
- Real-time console logging
- Color-coded messages (log, error, success)
- Timestamps for each log entry
- Scrollable output
- Clear console button

### Error Handling
- Shows clear error messages if form fails to load
- Displays server response errors
- Logs all network requests

### Testing Checklist
- [ ] Form loads correctly
- [ ] All form fields are visible
- [ ] Submit button is clickable
- [ ] Form submission works
- [ ] Success message appears
- [ ] Form resets after submission
- [ ] Data is saved to database
- [ ] No JavaScript errors in console

## Troubleshooting

### Form Not Loading
1. Check if Base URL is correct
2. Verify Form ID is correct (6-digit Public ID)
3. Check Shop Domain is correct
4. Look at Debug Console for error messages
5. Check server logs for PHP errors

### Submit Button Not Working
1. Check Debug Console for JavaScript errors
2. Verify scripts are loading (check Network tab)
3. Check if jQuery is loaded
4. Verify form_id is present in form HTML
5. Check browser console for additional errors

### Data Not Saving
1. Check Debug Console for submission logs
2. Verify AJAX request is being sent
3. Check server response in Debug Console
4. Verify database connection
5. Check PHP error logs on server

## Deployment to Hostinger

After testing locally:

1. **Upload Files**
   - Upload `test_storefront.html` to your Hostinger server
   - Ensure all JavaScript files are uploaded
   - Verify file permissions

2. **Update Base URL**
   - Change Base URL to Production URL
   - Test form loading
   - Test form submission

3. **Verify Database**
   - Check `form_submissions` table
   - Verify data is being saved correctly
   - Check for any database errors

## Notes

- This test page uses Fetch API for form submission
- It includes inline JavaScript that works without jQuery
- All console logs are captured and displayed
- Network requests are logged for debugging
- Form validation errors are displayed

## Support

If you encounter issues:
1. Check the Debug Console for detailed error messages
2. Check browser console (F12) for additional errors
3. Check server error logs
4. Verify all files are uploaded correctly

