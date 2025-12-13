-- SQL script to update Shopify Client ID
-- Run this script in your MySQL database
-- Your Client ID: de776739597a8c3590a04a65195d6f44

-- Option 1: Update if row with id=1 exists (most common case)
UPDATE thirdparty_apikey 
SET thirdparty_apikey = 'de776739597a8c3590a04a65195d6f44', 
    status = 1 
WHERE id = 1;

-- Option 2: If you need to update the first active row (uncomment if id=1 doesn't exist)
-- First, find the minimum id with status=1, then update it
-- SET @min_id = (SELECT MIN(id) FROM thirdparty_apikey WHERE status = 1);
-- UPDATE thirdparty_apikey 
-- SET thirdparty_apikey = 'de776739597a8c3590a04a65195d6f44'
-- WHERE id = @min_id AND status = 1;

-- Option 3: Insert new row if table is empty (uncomment if needed)
-- Note: Adjust column names based on your actual table structure
-- INSERT INTO thirdparty_apikey (thirdparty_apikey, status) 
-- VALUES ('de776739597a8c3590a04a65195d6f44', 1);

-- Verify the update - Check that your Client ID is now stored
SELECT id, thirdparty_apikey, status 
FROM thirdparty_apikey 
WHERE status = 1 
ORDER BY id ASC;

