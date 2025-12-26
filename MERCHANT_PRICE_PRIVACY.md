# Merchant Price Privacy Implementation

## What Was Changed

### Problem
Merchants could see admin prices in active auctions, which should be hidden from them.

### Solution
Now merchants only see their own `merchant_price` for products they own. All other users see the `admin_price` (current bid price).

## Implementation Details

### 1. **Home Page (index.php)**
- Added logic to check if logged-in user is a merchant and owns the product
- If yes: Shows `merchant_price`
- If no: Shows `current_price` (admin price)

### 2. **Auction Detail Page (auction.php)**
- Checks if current user is the product owner merchant
- If merchant owns product:
  - Shows `merchant_price` for Current Bid and Starting Price
  - Displays "(Your Price)" label
  - Prevents merchant from bidding on their own product
- If not owner:
  - Shows `current_price` (admin price)
  - Normal bidding functionality

### 3. **User Dashboard (user/dashboard.php)**
- Same logic applied to active auctions list
- Merchants see their `merchant_price` for their own products
- Others see `current_price`

## How It Works

### Scenario Example:
1. **Merchant "roza"** sells product at **$100** (merchant_price)
2. **Admin** sets auction price to **$200** (admin_price)
3. **Auction goes live**

### What Each User Sees:

| User Type | Product Owner? | Sees Price |
|-----------|---------------|------------|
| Merchant "roza" | ✅ Yes | **$100** (merchant_price) |
| Other Merchant | ❌ No | **$200** (admin_price) |
| Regular User | ❌ No | **$200** (admin_price) |
| Admin | ❌ No | **$200** (admin_price) |

## Key Features

✅ **Merchants never see admin prices** (except for their own products where they see merchant_price)  
✅ **Merchants cannot bid on their own products**  
✅ **All bidding functionality uses admin_price** (behind the scenes)  
✅ **Price privacy maintained** - merchants only see what they set  

## Testing

1. Login as merchant who owns a product
2. Check home page - should see merchant_price
3. View auction detail - should see merchant_price with "(Your Price)" label
4. Try to bid - should see message "This is your product. You cannot bid on your own product."
5. Login as different user - should see admin_price everywhere

## Files Modified

- `index.php` - Home page auction display
- `auction.php` - Auction detail page
- `user/dashboard.php` - User dashboard active auctions

