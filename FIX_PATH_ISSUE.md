# Fix "404 Not Found" Error - Path Issue

## Problem
Your files are in `D:\Collabration\fullstackproject_Support\` but Apache is looking in a different folder (usually `htdocs` or `www`).

## Solution Options

### Option 1: Copy Files to htdocs (Easiest)

1. **Find your htdocs folder:**
   - XAMPP: Usually `C:\xampp\htdocs\`
   - WAMP: Usually `C:\wamp64\www\` or `C:\wamp\www\`
   - Laragon: Usually `C:\laragon\www\`

2. **Copy your project folder:**
   - Copy the entire `fullstackproject_Support` folder
   - Paste it into your `htdocs` or `www` folder
   - So it becomes: `C:\xampp\htdocs\fullstackproject_Support\`

3. **Access the site:**
   - URL: `http://localhost/fullstackproject_Support/index.php`
   - Or: `http://localhost/fullstackproject_Support/`

### Option 2: Create Symbolic Link (Advanced)

If you want to keep files in current location:

1. Open Command Prompt as Administrator
2. Run this command (adjust paths as needed):
   ```
   mklink /D "C:\xampp\htdocs\fullstackproject_Support" "D:\Collabration\fullstackproject_Support"
   ```

### Option 3: Change Apache Document Root

1. Open `httpd.conf` file (usually in `C:\xampp\apache\conf\`)
2. Find `DocumentRoot` and change it to:
   ```
   DocumentRoot "D:/Collabration/fullstackproject_Support"
   ```
3. Also change `<Directory>` to:
   ```
   <Directory "D:/Collabration/fullstackproject_Support">
   ```
4. Restart Apache

## Quick Test

After copying files, test:
- `http://localhost/fullstackproject_Support/`
- `http://localhost/fullstackproject_Support/index.php`

If you see the homepage, it's working!

