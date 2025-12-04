# CMS System Documentation

## Overview

This project has been completely redesigned with a modern UI and a fully dynamic CMS (Content Management System). All website content can now be managed from the admin dashboard without touching code.

## Architecture

### Database Structure

The CMS system uses the following database tables:

1. **banners** - Homepage banners/sliders
2. **sections** - Dynamic page sections (hero, features, testimonials, etc.)
3. **menus** - Dynamic navigation menus (header, footer, sidebar)
4. **blogs** - Blog posts/news articles
5. **services** - Service listings
6. **pages** - Dynamic pages with SEO fields
7. **site_settings** - Global site settings (logo, colors, social links, footer, etc.)

### Models

All models are located in `app/Models/`:
- `Banner.php`
- `Section.php`
- `Menu.php`
- `Blog.php`
- `Service.php`
- `Page.php` (enhanced)
- `SiteSetting.php` (enhanced)

### Controllers

Admin CMS controllers are located in `app/Http/Controllers/Admin/`:
- `BannerController.php` - Full CRUD for banners
- `SectionController.php` - Full CRUD for sections
- `MenuController.php` - Full CRUD for menus
- `BlogController.php` - Full CRUD for blog posts
- `ServiceController.php` - Full CRUD for services
- `PageController.php` - Full CRUD for pages (existing, enhanced)
- `SiteSettingsController.php` - Site settings management (existing, enhanced)

## Admin Routes

All CMS routes are prefixed with `/admin` and require admin authentication:

```
/admin/banners - Banner management
/admin/sections - Section management
/admin/menus - Menu management
/admin/blogs - Blog management
/admin/services - Service management
/admin/pages - Page management
/admin/site/settings - Site settings
```

## Frontend Rendering

### Layout System

The project now includes a modern Tailwind-based layout at `resources/views/layouts/modern.blade.php` with:
- Smooth animations using GSAP
- Responsive design (mobile, tablet, desktop)
- Dynamic menu rendering from CMS
- Dynamic footer content from CMS
- SEO meta tags from CMS

### Dynamic Content Rendering

#### Banners
Banners are rendered dynamically on the homepage:
```php
$banners = \App\Models\Banner::active()
    ->forLocale(app()->getLocale())
    ->ordered()
    ->get();
```

#### Sections
Sections are rendered by page and type:
```php
$sections = \App\Models\Section::visible()
    ->forPage('home')
    ->forLocale(app()->getLocale())
    ->ordered()
    ->get();
```

#### Menus
Menus are automatically loaded in the layout:
```php
$menus = \App\Models\Menu::forMenu('header')
    ->forLocale(app()->getLocale())
    ->active()
    ->rootItems()
    ->ordered()
    ->get();
```

## Admin Interface

### Banner Management

**Location:** `/admin/banners`

**Features:**
- Create, edit, delete banners
- Upload desktop and mobile images
- Set order/priority
- Toggle active/inactive
- Multi-language support
- Button configuration (text, link, style)

**Fields:**
- Title, Subtitle, Description
- Image (desktop)
- Mobile Image
- Button Text, Button Link, Button Style
- Order
- Active Status
- Locale

### Section Management

**Location:** `/admin/sections`

**Features:**
- Create, edit, delete sections
- Assign to specific pages (home, about, contact, etc.)
- Multiple section types (content, hero, features, testimonials, CTA, gallery, stats)
- Flexible JSON data field for custom content
- Image upload support
- Multi-language support
- Visibility toggle
- Ordering

**Fields:**
- Name (unique identifier)
- Type (content, hero, features, testimonials, cta, gallery, stats)
- Title, Subtitle, Content
- Image
- Images (array)
- Data (JSON for flexible content)
- Page (which page this section belongs to)
- Order
- Visible Status
- Locale

### Menu Management

**Location:** `/admin/menus`

**Features:**
- Create, edit, delete menu items
- Hierarchical menu structure (parent/child)
- Multiple menu locations (header, footer, sidebar)
- Icon support
- Route or URL support
- Open in new tab option
- Multi-language support
- Ordering

**Fields:**
- Name (menu location: header, footer, sidebar)
- Label (display text)
- URL or Route
- Icon
- Parent ID (for submenus)
- Order
- Active Status
- Open in New Tab
- Locale

### Blog Management

**Location:** `/admin/blogs`

**Features:**
- Create, edit, delete blog posts
- Rich content editor
- Featured image upload
- Author assignment
- Publish date scheduling
- Featured post toggle
- SEO fields (meta title, description, keywords)
- Tags and categories (JSON)
- View counter
- Multi-language support

**Fields:**
- Title, Slug
- Excerpt, Content
- Featured Image
- Author Name, Author ID
- Published At
- Is Published, Is Featured
- Locale
- Meta Title, Meta Description, Meta Keywords
- Tags, Categories (JSON arrays)
- Views

### Service Management

**Location:** `/admin/services`

**Features:**
- Create, edit, delete services
- Icon or image support
- Features list (JSON)
- Pricing information (JSON)
- SEO fields
- Featured service toggle
- Multi-language support
- Ordering

**Fields:**
- Title, Slug
- Short Description, Description
- Icon, Image
- Order
- Active Status, Featured Status
- Locale
- Meta Title, Meta Description, Meta Keywords
- Features (JSON array)
- Pricing (JSON)

### Page Management

**Location:** `/admin/pages`

**Features:**
- Create, edit, delete pages
- Rich content editor
- Featured image
- Template selection
- SEO fields
- Show in menu option
- Multi-language support
- Associated sections (JSON)

**Fields:**
- Slug, Title
- Content
- Featured Image
- Excerpt
- Template
- Sections (JSON array)
- Order
- Show in Menu, Menu Label
- Locale
- Meta Title, Meta Description, Meta Keywords
- Is Published

### Site Settings

**Location:** `/admin/site/settings`

**Features:**
- Brand name and logo
- Color scheme (primary, secondary)
- Contact information
- Social media links
- Footer content
- SEO defaults
- Hero section content

**Fields:**
- Brand Name
- Primary Color, Secondary Color
- Logo Path, Favicon Path
- Contact Email, Phone, Address
- Hero Title, Hero Subtitle
- Meta Title, Meta Description, Meta Keywords
- Social Media URLs (Facebook, Twitter, Instagram, LinkedIn, YouTube, TikTok)
- Footer Text, Footer Copyright
- Social Links (JSON array for additional platforms)
- Footer Links (JSON array)
- Additional Settings (JSON for custom settings)

## Frontend Components

### Modern Layout

The new `modern.blade.php` layout includes:

1. **Fixed Navigation Bar**
   - Sticky header with backdrop blur
   - Dynamic menu from CMS
   - Language switcher
   - Responsive mobile menu
   - Scroll effects

2. **Footer**
   - Dynamic footer menus from CMS
   - Social media links from site settings
   - Contact information
   - Copyright text

3. **Animations**
   - GSAP-powered animations
   - Scroll-triggered animations
   - Smooth transitions
   - Fade-in, slide-in effects

### Using the Modern Layout

In your Blade views, extend the modern layout:

```blade
@extends('layouts.modern')

@section('meta_title', 'Page Title')
@section('meta_description', 'Page description')
@section('meta_keywords', 'keywords, here')

@section('content')
    <!-- Your content here -->
    <div data-animate="fade-in-up" data-delay="0.1">
        <!-- This will animate on scroll -->
    </div>
@endsection
```

## Animation System

### GSAP Integration

GSAP is integrated via `resources/js/app.js` with:
- ScrollTrigger plugin
- Automatic animation detection via `data-animate` attribute
- Smooth easing functions

### Available Animations

Add `data-animate` attribute to elements:

- `fade-in` - Fade in effect
- `fade-in-up` - Fade in from bottom
- `slide-in-left` - Slide in from left
- `slide-in-right` - Slide in from right

Example:
```html
<div data-animate="fade-in-up" data-delay="0.2">
    Content that animates
</div>
```

## Multi-Language Support

All CMS models support multi-language content:

1. Each record has a `locale` field
2. Models include `forLocale()` scope
3. Frontend automatically loads content for current locale
4. Admin can create content for each language separately

## File Structure

```
app/
├── Models/
│   ├── Banner.php
│   ├── Section.php
│   ├── Menu.php
│   ├── Blog.php
│   ├── Service.php
│   ├── Page.php (enhanced)
│   └── SiteSetting.php (enhanced)
├── Http/
│   └── Controllers/
│       └── Admin/
│           ├── BannerController.php
│           ├── SectionController.php
│           ├── MenuController.php
│           ├── BlogController.php
│           ├── ServiceController.php
│           └── PageController.php

resources/
├── views/
│   ├── layouts/
│   │   └── modern.blade.php (new modern layout)
│   └── admin/
│       └── cms/
│           ├── banners/
│           ├── sections/
│           ├── menus/
│           ├── blogs/
│           └── services/

database/
└── migrations/
    ├── create_banners_table.php
    ├── create_sections_table.php
    ├── create_menus_table.php
    ├── create_blogs_table.php
    ├── create_services_table.php
    ├── enhance_site_settings_table.php
    └── enhance_pages_table.php
```

## Setup Instructions

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Install Dependencies**
   ```bash
   npm install
   npm run build
   ```

3. **Create Admin Views**
   The admin views need to be created in `resources/views/admin/cms/`:
   - `banners/index.blade.php`, `create.blade.php`, `edit.blade.php`
   - `sections/index.blade.php`, `create.blade.php`, `edit.blade.php`
   - `menus/index.blade.php`, `create.blade.php`, `edit.blade.php`
   - `blogs/index.blade.php`, `create.blade.php`, `edit.blade.php`
   - `services/index.blade.php`, `create.blade.php`, `edit.blade.php`

4. **Update Public Controller**
   Update `app/Http/Controllers/PublicController.php` to use CMS content:
   - Load banners for homepage
   - Load sections for each page
   - Use dynamic menus

## Best Practices

1. **Always set locale** when creating CMS content
2. **Use unique names** for sections to avoid conflicts
3. **Order matters** - Set appropriate order values for proper display
4. **Image optimization** - Compress images before upload
5. **SEO fields** - Always fill meta title and description for better SEO
6. **Test responsiveness** - Check content on mobile, tablet, and desktop

## Future Enhancements

Potential improvements:
- Media library/manager for better image management
- WYSIWYG editor for rich content
- Content versioning
- Draft/Preview functionality
- Content scheduling
- Analytics integration
- A/B testing for banners
- Content templates

## Support

For issues or questions about the CMS system, refer to:
- Model files for available fields and methods
- Controller files for validation rules
- Migration files for database structure

