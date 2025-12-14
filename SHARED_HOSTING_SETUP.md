# Shared Hosting Setup Guide

## Storage Link Issue (symlink disabled)

If you're getting the error `Call to undefined function Illuminate\Filesystem\symlink()` when running `php artisan storage:link` on shared hosting, this is because the `symlink()` PHP function is disabled on your server.

## Solution: Use Custom Command

We've created a custom command that works without symlinks using PHP routing:

```bash
php artisan storage:link-shared-hosting
```

This command:
1. Creates a `public/storage` directory
2. Adds an `.htaccess` file to route requests through `index.php`
3. Creates an `index.php` file that serves files from `storage/app/public`

## How It Works

Instead of using a symbolic link, requests to `/storage/*` are routed through `public/storage/index.php`, which then serves the files from `storage/app/public`.

## Usage

On your shared hosting server, run:

```bash
php artisan storage:link-shared-hosting
```

This is a one-time setup. After running this command, all files in `storage/app/public` will be accessible via `/storage/` URLs, just like with a symlink.

## Verification

After running the command, verify it works by:
1. Upload a test file to `storage/app/public/test.txt`
2. Access it via: `https://yourdomain.com/storage/test.txt`
3. If the file loads, the setup is successful!

## Vite Manifest Issue - SOLVED!

**You don't need npm anymore!** We've created static CSS/JS files that work without any build process.

### Solution: Use Static Files (No npm Required) ✅

The application now automatically uses static files from:
- `public/css/app.css` (Custom CSS)
- `public/js/app.js` (Custom JavaScript)
- Alpine.js and GSAP from CDN

**These files are already created and ready to use!**

### How It Works:

1. The layouts check for Vite manifest first (if you have npm build)
2. If not found, they automatically use static files from `public/css/` and `public/js/`
3. Alpine.js and GSAP are loaded from CDN (no installation needed)

### No Action Required!

The static files are already in place. Just upload the project to your server and it will work automatically.

### Alternative Options (If needed):

#### Option 1: Build Locally and Upload (Original method)

1. On your local machine, run:
   ```bash
   npm install
   npm run build
   ```

2. Upload the `public/build` directory to your server

#### Option 2: Build on Server (if Node.js is available)

1. SSH into your server
2. Navigate to project directory
3. Run:
   ```bash
   npm install
   npm run build
   ```

## Notes

- This solution works on shared hosting where `symlink()` is disabled
- Files are served directly from `storage/app/public` via PHP
- Performance is slightly slower than symlinks, but works reliably on all shared hosts
- No additional server configuration needed
- Vite assets should be built before deployment

## Alternative: Contact Hosting Provider

If you have access to your hosting provider's support, you can ask them to enable the `symlink()` function in PHP, then use the standard Laravel command:

```bash
php artisan storage:link
```
