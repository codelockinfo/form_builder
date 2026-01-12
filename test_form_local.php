<?php
/**
 * Local Testing File - Access Forms Like Shopify
 * 
 * This file allows you to test forms locally using the same endpoint
 * that Shopify uses via App Proxy.
 * 
 * Usage:
 * 1. Make sure you have a form created in your database
 * 2. Get the form's public_id (6-digit ID) or database ID
 * 3. Get your shop domain (from your store registration)
 * 4. Access: http://localhost/form_builder/test_form_local.php?form_id=YOUR_FORM_ID&shop=YOUR_SHOP.myshopify.com
 */

// Get parameters
$form_id = isset($_GET['form_id']) ? trim($_GET['form_id']) : '';
$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';

// If no parameters, show form to enter them
if (empty($form_id) || empty($shop)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Local Form Tester - Form Builder</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f5f5f5;
                padding: 40px 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                margin-bottom: 10px;
            }
            .subtitle {
                color: #666;
                margin-bottom: 30px;
                font-size: 14px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                margin-bottom: 8px;
                color: #333;
                font-weight: 500;
            }
            input {
                width: 100%;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            input:focus {
                outline: none;
                border-color: #EB1256;
            }
            .btn {
                background: #EB1256;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                cursor: pointer;
                width: 100%;
            }
            .btn:hover {
                background: #d0104a;
            }
            .info {
                background: #f0f9ff;
                border-left: 4px solid #EB1256;
                padding: 15px;
                margin-top: 20px;
                border-radius: 4px;
            }
            .info h3 {
                color: #EB1256;
                margin-bottom: 10px;
                font-size: 16px;
            }
            .info p {
                color: #666;
                font-size: 14px;
                line-height: 1.6;
            }
            .info code {
                background: #e5e7eb;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🧪 Local Form Tester</h1>
            <p class="subtitle">Test your forms locally using the same endpoint as Shopify</p>
            
            <form method="GET" action="">
                <div class="form-group">
                    <label for="shop">Shop Domain *</label>
                    <input type="text" id="shop" name="shop" placeholder="your-store.myshopify.com" required>
                </div>
                
                <div class="form-group">
                    <label for="form_id">Form ID (Public ID or Database ID) *</label>
                    <input type="text" id="form_id" name="form_id" placeholder="123456 or 1" required>
                </div>
                
                <button type="submit" class="btn">Load Form</button>
            </form>
            
            <div class="info">
                <h3>ℹ️ How to Use</h3>
                <p><strong>Shop Domain:</strong> Enter your shop domain (e.g., <code>test-store.myshopify.com</code>)</p>
                <p><strong>Form ID:</strong> Enter either:</p>
                <ul style="margin-left: 20px; margin-top: 8px;">
                    <li>Public ID (6-digit number) - Recommended</li>
                    <li>Database ID (numeric ID from database)</li>
                </ul>
                <p style="margin-top: 15px;"><strong>Direct URL Format:</strong></p>
                <code style="display: block; margin-top: 5px; padding: 8px; background: #e5e7eb;">
                    <?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/test_form_local.php?shop=YOUR_SHOP.myshopify.com&form_id=YOUR_FORM_ID'; ?>
                </code>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Normalize shop domain
$shop = preg_replace('#^https?://#', '', $shop);
$shop = rtrim($shop, '/');
$shop = strtolower($shop);

// Build the app-proxy URL
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$proxy_url = $base_url . '/shopify/app-proxy.php?path=render&shop=' . urlencode($shop) . '&form_id=' . urlencode($form_id);

// Fetch the form HTML
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $proxy_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$form_html = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Preview - Local Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .header-info {
            color: #666;
            font-size: 14px;
        }
        .header-info code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
    <!-- Include form CSS if needed -->
    <link rel="stylesheet" href="assets/css/customstyle.css">
</head>
<body>
    <div class="header">
        <h1>📋 Form Preview - Local Test</h1>
        <div class="header-info">
            <p><strong>Shop:</strong> <code><?php echo htmlspecialchars($shop); ?></code></p>
            <p><strong>Form ID:</strong> <code><?php echo htmlspecialchars($form_id); ?></code></p>
            <p><strong>Proxy URL:</strong> <code><?php echo htmlspecialchars($proxy_url); ?></code></p>
            <p><strong>HTTP Status:</strong> <code><?php echo $http_code; ?></code></p>
        </div>
    </div>
    
    <div class="form-container">
        <?php if ($http_code == 200 && !empty($form_html)): ?>
            <?php echo $form_html; ?>
        <?php elseif ($http_code == 404): ?>
            <div class="error">
                <h2>❌ Form Not Found</h2>
                <p>The form with ID <strong><?php echo htmlspecialchars($form_id); ?></strong> was not found for shop <strong><?php echo htmlspecialchars($shop); ?></strong>.</p>
                <p>Please check:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>The form ID is correct</li>
                    <li>The shop domain is correct</li>
                    <li>The form belongs to this shop</li>
                    <li>The form status is active (status = 1)</li>
                </ul>
            </div>
        <?php elseif ($http_code == 400): ?>
            <div class="error">
                <h2>❌ Bad Request</h2>
                <p>Form ID is required. Please provide a valid form ID.</p>
            </div>
        <?php elseif ($http_code == 500): ?>
            <div class="error">
                <h2>❌ Server Error</h2>
                <p>An error occurred while loading the form. Check your PHP error logs.</p>
                <pre style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px; overflow-x: auto;"><?php echo htmlspecialchars($form_html); ?></pre>
            </div>
        <?php else: ?>
            <div class="error">
                <h2>❌ Error Loading Form</h2>
                <p>HTTP Status: <?php echo $http_code; ?></p>
                <pre style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px; overflow-x: auto;"><?php echo htmlspecialchars($form_html); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

