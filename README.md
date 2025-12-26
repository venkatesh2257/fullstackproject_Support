# Bidding Website - PHP/HTML/CSS Only

A complete bidding website built with PHP, HTML, and CSS only. No JavaScript frameworks or external libraries.

## Features

### 1. User Role
- View all active auctions
- Participate in bidding (requires active subscription)
- View their own bids and status
- Subscription management

### 2. Admin Role
- Full control over the system
- Manage products (approve/reject, set admin prices)
- Create and manage auctions
- View all users and merchants
- See all statistics and revenue

### 3. Merchant Role
- Add products with merchant price
- View their own products and status
- Cannot see admin prices (privacy maintained)
- Requires subscription to add products

## Installation

1. **Database Setup**
   - Create a MySQL database named `bidding_system`
   - Import the `database.sql` file to create all tables
   - Update database credentials in `config.php`

2. **Configuration**
   - Open `config.php` and update:
     - `DB_HOST` - Database host (usually 'localhost')
     - `DB_USER` - Database username
     - `DB_PASS` - Database password
     - `DB_NAME` - Database name
     - `SITE_URL` - Your website URL

3. **Default Admin Account**
   - Username: `admin`
   - Email: `admin@bidding.com`
   - Password: `admin123`

## File Structure

```
/
├── config.php                 # Database and site configuration
├── index.php                  # Home page with active auctions
├── login.php                  # User login page
├── register.php               # User registration
├── logout.php                 # Logout handler
├── auction.php                # Auction detail and bidding page
├── database.sql               # Database schema
├── includes/
│   ├── header.php            # Site header
│   └── footer.php            # Site footer
├── user/
│   ├── dashboard.php         # User dashboard
│   └── subscription.php      # User subscription plans
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── products.php          # Manage products
│   ├── auctions.php          # Manage auctions
│   ├── users.php             # View all users
│   └── merchants.php         # View all merchants
├── merchant/
│   ├── dashboard.php         # Merchant dashboard
│   └── subscription.php      # Merchant subscription plans
└── assets/
    └── css/
        └── style.css         # All styling
```

## User Flow

### For Users:
1. Register/Login
2. Subscribe to a plan (required to bid)
3. Browse active auctions
4. Place bids on auctions
5. View bid history

### For Merchants:
1. Register as merchant
2. Subscribe to a merchant plan (required to add products)
3. Add products with merchant price
4. Wait for admin approval
5. View product status

### For Admin:
1. Login with admin credentials
2. View pending products
3. Set admin prices (merchants cannot see these)
4. Approve/reject products
5. Create auctions from approved products
6. Manage all aspects of the system

## Subscription Plans

### User Plans:
- Basic: $29.99 for 30 days
- Premium: $49.99 for 60 days
- Annual: $199.99 for 365 days

### Merchant Plans:
- Basic: $49.99 for 30 days
- Premium: $99.99 for 60 days
- Annual: $399.99 for 365 days

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session-based authentication
- Role-based access control

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (optional)

## Notes

- All prices are stored in database
- Admin can set different prices than merchant prices
- Merchants cannot see admin prices (privacy feature)
- Subscription is required for users to bid
- Subscription is required for merchants to add products
- Admin has full control over all aspects

## Support

For issues or questions, please check the code comments or contact support.

