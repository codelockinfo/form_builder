# Form Builder Block - Shopify Theme Extension

This is a Shopify Theme App Extension that allows merchants to add forms created in your PHP app to any page via the Theme Customizer.

## 📦 What This Extension Does

1. **Appears in Theme Customizer** → App Blocks section
2. **Shows Form Dropdown** → Lists all forms created in your app
3. **Renders Selected Form** → Displays form HTML on storefront
4. **Uses App Proxy** → Fetches forms from your PHP backend

## 🗂️ File Structure

```
form-builder-block/
├── shopify.extension.toml    # Extension configuration
├── blocks/
│   └── form-block.liquid     # Main block template
└── README.md                 # This file
```

## 🚀 Deployment

### Using Shopify CLI

```bash
# From project root
shopify app deploy

# Select: form-builder-block
```

### Manual Upload (Not Recommended)

Shopify requires CLI deployment. Manual uploads won't work.

## ⚙️ Configuration

### App Proxy Setup

Required in Shopify Partner Dashboard:
- **Subpath prefix**: `apps`
- **Subpath**: `form-builder`
- **Proxy URL**: `https://YOUR-DOMAIN.com/shopify/app-proxy.php`

### Block Settings

The block has these settings (configured in schema):
- `form_id` - Form ID to display
- `form_width` - Form width percentage (50-100%)
- `form_alignment` - Left, Center, or Right

## 🔧 How It Works

1. **Theme Editor Mode** (`request.design_mode`):
   - Fetches form list via App Proxy
   - Shows dropdown to select form
   - Saves selection to block settings
   - Shows preview

2. **Storefront Mode**:
   - Reads `form_id` from block settings
   - Fetches form HTML via App Proxy
   - Renders form on page

## 📝 Customization

### Styling

Edit the `<style>` section in `form-block.liquid` to customize appearance.

### Form Submission

Currently forms are rendered but submission handling needs to be implemented in your PHP app.

## ✅ Requirements

- App Proxy configured and working
- Forms exist in database with `status = 1`
- Extension deployed via Shopify CLI
- Extension enabled on store

## 🐛 Troubleshooting

See `SHOPIFY_CLI_SETUP.md` for detailed troubleshooting guide.

