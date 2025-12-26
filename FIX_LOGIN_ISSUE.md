# Fix Admin Login Issue

## Problem
The admin password hash in the database might be incorrect, causing "Invalid email or password" error.

## Solution

### Option 1: Run Fix Script (Recommended)

1. **Open in browser:**
   ```
   http://localhost/fullstackproject_Support/fix_admin_password.php
   ```

2. **This will:**
   - Generate correct password hash for "admin123"
   - Update the admin password in database
   - Create admin user if it doesn't exist

3. **Then login with:**
   - Email: `admin@bidding.com`
   - Password: `admin123`

### Option 2: Manual Fix via phpMyAdmin

1. Open phpMyAdmin
2. Select `bidding_system` database
3. Click on `users` table
4. Find the admin user (email: admin@bidding.com)
5. Click "Edit"
6. In the password field, run this SQL query:

```sql
UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy' 
WHERE email = 'admin@bidding.com';
```

This hash is for password: `admin123`

### Option 3: Create New Admin via SQL

Run this in phpMyAdmin SQL tab:

```sql
-- Delete old admin if exists
DELETE FROM users WHERE email = 'admin@bidding.com';

-- Create new admin with correct password hash
INSERT INTO users (username, email, password, role, subscription_status) 
VALUES ('admin', 'admin@bidding.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin', 'active');
```

Password: `admin123`

## After Fixing

1. Go to: `http://localhost/fullstackproject_Support/login.php`
2. Login with:
   - Email: `admin@bidding.com`
   - Password: `admin123`
3. You should be redirected to Admin Dashboard

## Delete Fix Script After Use

For security, delete `fix_admin_password.php` after using it!

