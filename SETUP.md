# Setup Guide - Bidding Website

## Step-by-Step Installation

### Step 1: Database Setup
1. Open phpMyAdmin or MySQL command line
2. Create a new database:
   ```sql
   CREATE DATABASE bidding_system;
   ```
3. Import the `database.sql` file:
   - In phpMyAdmin: Select the database → Import → Choose `database.sql`
   - Or via command line: `mysql -u root -p bidding_system < database.sql`

### Step 2: Configure Database Connection
1. Open `config.php`
2. Update these lines with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');        // Your MySQL username
   define('DB_PASS', '');            // Your MySQL password
   define('DB_NAME', 'bidding_system');
   ```
3. Update the site URL:
   ```php
   define('SITE_URL', 'http://localhost/fullstackproject_Support');
   ```

### Step 3: File Permissions
- Make sure PHP has read/write permissions to the directory
- No special file permissions needed for basic functionality

### Step 4: Test the Installation
1. Start your web server (XAMPP, WAMP, or built-in PHP server)
2. Navigate to: `http://localhost/fullstackproject_Support/`
3. You should see the home page with active auctions

### Step 5: Login as Admin
- URL: `http://localhost/fullstackproject_Support/login.php`
- Email: `admin@bidding.com`
- Password: `admin123`

## Default Accounts

### Admin Account
- **Username:** admin
- **Email:** admin@bidding.com
- **Password:** admin123
- **Role:** Admin (full access)

## Testing the System

### Test User Flow:
1. Register a new user account
2. Login as user
3. Go to subscription page and purchase a plan
4. Browse auctions and place bids

### Test Merchant Flow:
1. Register as merchant
2. Login as merchant
3. Purchase merchant subscription
4. Add products with merchant prices
5. Wait for admin approval

### Test Admin Flow:
1. Login as admin
2. View pending products
3. Set admin prices (merchants can't see these)
4. Approve products
5. Create auctions
6. Manage everything

## Important Notes

1. **Subscription Required:**
   - Users need subscription to place bids
   - Merchants need subscription to add products

2. **Price Privacy:**
   - Merchants set their own prices (merchant_price)
   - Admin sets different prices (admin_price)
   - Merchants CANNOT see admin prices

3. **Product Flow:**
   - Merchant adds product → Status: Pending
   - Admin reviews and sets admin price
   - Admin approves → Status: Approved
   - Admin creates auction from approved product

4. **Auction Flow:**
   - Admin creates auction from approved product
   - Auction status: Upcoming → Active → Ended
   - Users with subscription can bid
   - Highest bid wins when auction ends

## Troubleshooting

### Database Connection Error
- Check database credentials in `config.php`
- Ensure MySQL service is running
- Verify database name is correct

### Page Not Found
- Check file paths are correct
- Ensure web server is pointing to correct directory
- Check `.htaccess` if using Apache

### Session Issues
- Ensure `session_start()` is called before any output
- Check PHP session configuration
- Clear browser cookies if needed

### CSS Not Loading
- Check `assets/css/style.css` exists
- Verify file paths in HTML
- Check browser console for 404 errors

## File Structure Verification

Make sure you have these directories:
```
/
├── config.php
├── index.php
├── login.php
├── register.php
├── logout.php
├── auction.php
├── database.sql
├── includes/
│   ├── header.php
│   └── footer.php
├── user/
│   ├── dashboard.php
│   └── subscription.php
├── admin/
│   ├── dashboard.php
│   ├── products.php
│   ├── auctions.php
│   ├── users.php
│   └── merchants.php
├── merchant/
│   ├── dashboard.php
│   └── subscription.php
└── assets/
    └── css/
        └── style.css
```

## Next Steps

1. Customize the design in `assets/css/style.css`
2. Add more subscription plans in subscription pages
3. Configure email notifications (optional)
4. Add payment gateway integration (optional)
5. Set up cron jobs for auction status updates (optional)

## Support

If you encounter any issues:
1. Check PHP error logs
2. Enable error display in `config.php` (for development only)
3. Verify all files are uploaded correctly
4. Check database tables are created properly

