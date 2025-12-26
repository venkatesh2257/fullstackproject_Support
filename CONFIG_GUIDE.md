# Database Configuration Guide

## How to Find Your Database Credentials

### Method 1: Check phpMyAdmin Login
1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Look at the login screen - it shows the default username
3. If you can login without a password, then password is empty
4. If you set a password, use that password

### Method 2: Check Your Server Configuration

#### For XAMPP:
- **Host:** localhost
- **Username:** root
- **Password:** (usually empty/blank)
- **Database:** bidding_system

#### For WAMP:
- **Host:** localhost
- **Username:** root
- **Password:** (usually empty/blank)
- **Database:** bidding_system

#### For MAMP (Mac):
- **Host:** localhost
- **Username:** root
- **Password:** root
- **Database:** bidding_system

#### For Laragon:
- **Host:** localhost
- **Username:** root
- **Password:** (usually empty/blank)
- **Database:** bidding_system

### Method 3: Test Connection
If you're not sure, try these steps:

1. **Test with current settings** (most likely to work):
   - DB_HOST: localhost
   - DB_USER: root
   - DB_PASS: '' (empty)
   - DB_NAME: bidding_system

2. **If connection fails**, check:
   - Is MySQL service running? (Check XAMPP/WAMP control panel)
   - Did you create the database correctly?
   - Try logging into phpMyAdmin with the same credentials

## Current config.php Settings

Your current settings should work for most local setups:

```php
define('DB_HOST', 'localhost');      // Usually correct
define('DB_USER', 'root');           // Default username
define('DB_PASS', '');               // Empty = no password (default)
define('DB_NAME', 'bidding_system'); // Your database name
```

## If You Need to Change Credentials

1. Open `config.php`
2. Update lines 3-6 with your credentials:

```php
define('DB_HOST', 'localhost');        // Change if different
define('DB_USER', 'your_username');    // Your MySQL username
define('DB_PASS', 'your_password');    // Your MySQL password
define('DB_NAME', 'bidding_system');   // Your database name
```

## Testing the Connection

After updating, test by:
1. Opening `http://localhost/fullstackproject_Support/index.php`
2. If you see the homepage, connection is working!
3. If you see an error, check:
   - MySQL service is running
   - Database name is correct
   - Username/password are correct

## Common Issues

### "Connection failed" Error
- **Solution:** Make sure MySQL service is running in XAMPP/WAMP control panel

### "Access denied" Error
- **Solution:** Check username and password in config.php match your MySQL credentials

### "Unknown database" Error
- **Solution:** Make sure database name is exactly `bidding_system` (case-sensitive)

