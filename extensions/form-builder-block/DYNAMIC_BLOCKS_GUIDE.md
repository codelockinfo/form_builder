# Dynamic Form Blocks - Complete Guide

## How Dynamic Blocks Work

### The Challenge
Shopify requires block files to exist in your app extension **before deployment**. Blocks cannot be generated dynamically in the Theme Customizer. However, we've created a system that **automatically generates blocks** when forms are created/updated.

### The Solution
1. **Auto-Generation**: When a form is created/updated, blocks are automatically generated
2. **Deployment**: Blocks need to be deployed to Shopify for them to appear
3. **Workflow**: Create form → Blocks generate → Deploy app → Blocks appear

## Current Status

✅ **Fixed**: Sync script bug (FORM_NAME replacement)
✅ **Working**: Auto-sync on form creation/update
✅ **Ready**: Blocks generate automatically

## Testing the System

### Step 1: Generate Blocks for Existing Forms

Access via browser:
```
http://localhost/form_builder/shopify/sync-form-blocks.php?shop=dashboardmanage.myshopify.com
```

Or via command line:
```bash
cd shopify
php sync-form-blocks.php dashboardmanage.myshopify.com
```

### Step 2: Verify Blocks Were Generated

Check the blocks directory:
```
extensions/form-builder-block/blocks/
```

You should see files like:
- `form-18-log_in.liquid`
- `form-19-regester_to_xyz.liquid`

### Step 3: Verify Block Content

Open a generated block file and check:
- `{{ FORM_ID }}` should be replaced with actual form ID (e.g., `18`)
- `{{ FORM_NAME }}` should be replaced with form name
- `{{ FORM_NAME_DISPLAY }}` should be replaced with display name (max 25 chars)

### Step 4: Deploy to Shopify

```bash
cd extensions/form-builder-block
shopify app deploy
```

## Troubleshooting

### Issue: Blocks show `{{ FORM_NAME_DISPLAY }}` placeholder

**Cause**: Blocks weren't generated or template variables weren't replaced

**Solution**:
1. Run sync script: `sync-form-blocks.php?shop=your-shop.myshopify.com`
2. Check generated files in `blocks/` directory
3. Verify placeholders are replaced
4. Redeploy app extension

### Issue: Blocks not appearing in Theme Customizer

**Cause**: Blocks weren't deployed or deployment failed

**Solution**:
1. Verify blocks exist in `blocks/` directory
2. Check block names are under 25 characters
3. Deploy: `shopify app deploy`
4. Check deployment logs for errors

### Issue: Auto-sync not working

**Cause**: `autoSyncFormBlocks()` not being called or failing silently

**Solution**:
1. Check PHP error logs
2. Manually trigger sync via web URL
3. Verify `CLS_SITE_URL` is correct
4. Check file permissions on blocks directory

## Workflow for Store Owners

### When Creating a New Form:

1. **Create form** in admin panel
   - Blocks auto-generate in background
   - Check `blocks/` directory to verify

2. **Deploy app extension** (one-time or periodic)
   ```bash
   shopify app deploy
   ```

3. **New form appears** in Theme Customizer under "Apps" section

### When Updating Form Name:

1. **Update form name** in admin panel
   - Blocks auto-regenerate
   - Old block file is updated

2. **Redeploy app extension**
   ```bash
   shopify app deploy
   ```

3. **Updated name appears** in Theme Customizer

## Best Practices

1. **Batch Deployments**: Instead of deploying after every form, deploy periodically (daily/weekly)
2. **Verify Before Deploy**: Check `blocks/` directory before deploying
3. **Monitor Sync**: Check sync script output for errors
4. **Test Locally**: Generate blocks locally before deploying

## API Endpoints

### Sync Blocks
```
GET /shopify/sync-form-blocks.php?shop=shop-name.myshopify.com
```

### Webhook Sync (for automation)
```
POST /shopify/webhook-sync-blocks.php?shop=shop-name.myshopify.com
```

## Files Structure

```
extensions/form-builder-block/
├── blocks/
│   ├── form-block.liquid              # Original block (with dropdown)
│   ├── form-block-template.liquid     # Template for generation
│   ├── form-18-log_in.liquid         # Generated block (example)
│   └── form-19-regester_to_xyz.liquid # Generated block (example)
└── shopify.extension.toml
```

## Next Steps

1. ✅ Fix sync script bug (DONE)
2. ✅ Test block generation (DO THIS NOW)
3. ✅ Deploy blocks to Shopify
4. ✅ Verify blocks appear in Theme Customizer

## Important Notes

- **Blocks are generated dynamically** when forms are created
- **Blocks must be deployed** to appear in Theme Customizer
- **This is a Shopify limitation** - files must exist before deployment
- **Auto-sync works** but deployment is still manual (can be automated with CI/CD)

