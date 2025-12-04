# CMS Seeders Documentation

This directory contains seeders for all CMS components.

## Available Seeders

### 1. BannerSeeder
Seeds banner/slider data for the homepage.
- **Model**: `App\Models\Banner`
- **Data**: Hero banners with titles, subtitles, descriptions, and CTA buttons

### 2. SectionSeeder
Seeds section configurations for different pages.
- **Model**: `App\Models\Section`
- **Data**: Homepage sections (hero, about, services, how-it-works, statistics, testimonials, portfolio, pricing-cta, faq, newsletter)

### 3. SectionItemSeeder
Seeds items/content for sections (depends on SectionSeeder).
- **Model**: `App\Models\SectionItem`
- **Data**: 
  - Progress items for About section
  - FAQ items for FAQ section
  - Portfolio items for Portfolio section

### 4. MenuSeeder
Seeds navigation menu items.
- **Model**: `App\Models\Menu`
- **Data**: Main menu and footer menu items

### 5. ServiceSeeder
Seeds service offerings.
- **Model**: `App\Models\Service`
- **Data**: 4 sample services (Garden landscaping, Soil making, Planting plants, Tree trimming)

### 6. BlogSeeder
Seeds blog posts/articles.
- **Model**: `App\Models\Blog`
- **Data**: 3 sample blog posts with content, tags, and categories

### 7. TestimonialSeeder
Seeds customer testimonials.
- **Model**: `App\Models\Testimonial`
- **Data**: 4 sample testimonials with ratings

### 8. StatisticSeeder
Seeds statistics/counter data.
- **Model**: `App\Models\Statistic`
- **Data**: 4 statistics (Total Customers, Total Companies, Completed Orders, Active Services)

### 9. HowItWorksStepSeeder
Seeds "How It Works" step-by-step guide.
- **Model**: `App\Models\HowItWorksStep`
- **Data**: 4 steps (Register, Browse, Engage, Enjoy)

### 10. CompanyPartnerSeeder
Seeds company/partner logos.
- **Model**: `App\Models\CompanyPartner`
- **Data**: 5 sample partner companies

### 11. PageSeeder
Seeds static pages.
- **Model**: `App\Models\Page`
- **Data**: About Us, Terms & Conditions, Privacy Policy pages

## Usage

### Run All CMS Seeders
```bash
php artisan db:seed --class=CmsSeeder
```

### Run Individual Seeders
```bash
# Run specific seeder
php artisan db:seed --class=BannerSeeder
php artisan db:seed --class=SectionSeeder
php artisan db:seed --class=ServiceSeeder
# ... etc
```

### Run All Seeders (Including CMS)
```bash
php artisan db:seed
```

## Seeder Dependencies

**Important**: Some seeders depend on others. Run them in this order:

1. `SectionSeeder` (must run before SectionItemSeeder)
2. `SectionItemSeeder` (depends on SectionSeeder)
3. All other seeders can run independently

The `CmsSeeder` handles this automatically.

## Notes

- All seeders use English (`en`) locale by default
- Multi-language support can be added by creating additional records with different `locale` values
- Image paths are set to `null` by default - upload images through the admin panel
- All seeders check for existing data to avoid duplicates (you may need to truncate tables first)

## Resetting and Re-seeding

To reset and re-seed all CMS data:

```bash
# Truncate tables (be careful in production!)
php artisan tinker
>>> DB::table('section_items')->truncate();
>>> DB::table('sections')->truncate();
>>> DB::table('banners')->truncate();
>>> DB::table('menus')->truncate();
>>> DB::table('services')->truncate();
>>> DB::table('blogs')->truncate();
>>> DB::table('testimonials')->truncate();
>>> DB::table('statistics')->truncate();
>>> DB::table('how_it_works_steps')->truncate();
>>> DB::table('company_partners')->truncate();
>>> DB::table('pages')->truncate();

# Then run seeders
php artisan db:seed --class=CmsSeeder
```

