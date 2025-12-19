# Dynamic Form Blocks Feature

This feature allows each form to appear as a separate section in the Shopify Theme Customizer's "Add section" panel. Each form will have its own block file with the name format: `{form_name} Easy Form Builder`.

## How It Works

1. **Block Generation**: A PHP script generates individual block files for each active form in the database.
2. **Block Files**: Each form gets its own `.liquid` block file in the `blocks/` directory.
3. **Naming**: Each block appears in the Theme Customizer as `{form_name} Easy Form Builder`.

## Setup Instructions

### Step 1: Generate Blocks for Existing Forms

Run the block generation script to create blocks for all existing forms:

**Via Command Line:**
```bash
cd shopify
php generate-form-blocks.php [shop_name]
```

- If `shop_name` is provided, blocks will be generated only for that shop
- If omitted, blocks will be generated for all active shops

**Via Web Browser:**
```
https://your-domain.com/form_builder/shopify/sync-form-blocks.php?shop=shop-name.myshopify.com
```

### Step 2: Deploy to Shopify

After generating blocks, you need to deploy your app extension to Shopify:

```bash
cd extensions/form-builder-block
shopify app deploy
```

Or use the Shopify CLI from your app root:
```bash
shopify app deploy
```

## Automatic Block Generation

### Option 1: Manual Sync (Recommended for Testing)

Call the sync endpoint after creating/updating forms:
```
POST /shopify/sync-form-blocks.php?shop=shop-name.myshopify.com
```

### Option 2: Automatic Sync (Requires Code Modification)

To automatically generate blocks when forms are created/updated, add this to your form creation/update functions in `user/cls_functions.php`:

```php
// After form creation/update, call:
$this->syncFormBlocks();
```

Then add this helper function to `cls_functions.php`:

```php
function syncFormBlocks() {
    $shop = $this->current_store_obj['shop_name'];
    $sync_url = CLS_SITE_URL . '/shopify/sync-form-blocks.php?shop=' . urlencode($shop);
    
    // Use curl or file_get_contents to trigger sync
    $ch = curl_init($sync_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    curl_close($ch);
}
```

## File Structure

```
extensions/form-builder-block/
├── blocks/
│   ├── form-block.liquid          # Original block (with dropdown)
│   ├── form-block-template.liquid  # Template for generating blocks
│   ├── form-1-contact-form.liquid # Generated block for form ID 1
│   ├── form-2-survey-form.liquid  # Generated block for form ID 2
│   └── ...
```

## Block Naming Convention

- **Filename**: `form-{id}-{sanitized-name}.liquid`
- **Display Name**: `{form_name} Easy Form Builder`

Example:
- Form ID: 5
- Form Name: "Contact Us"
- Filename: `form-5-contact_us.liquid`
- Display Name: "Contact Us Easy Form Builder"

## Cleanup

The generation script automatically removes block files for forms that:
- Have been deleted
- Have status = 0 (inactive)

## Troubleshooting

### Blocks Not Appearing in Theme Customizer

1. **Verify blocks were generated**: Check the `blocks/` directory for `.liquid` files
2. **Redeploy the app**: Run `shopify app deploy` after generating blocks
3. **Check form status**: Only forms with `status = 1` will generate blocks
4. **Clear cache**: Refresh the Theme Customizer

### Block Generation Fails

1. **Check file permissions**: Ensure the `blocks/` directory is writable
2. **Check PHP errors**: Review error logs for issues
3. **Verify database connection**: Ensure forms can be retrieved from database
4. **Check shop name**: Verify the shop exists and is active

## Notes

- Blocks are generated as static files - they won't update automatically when form names change
- You must run the sync script after renaming forms
- The original `form-block.liquid` with dropdown selection will still work
- New form-specific blocks will appear in the "Apps" section of the Theme Customizer

