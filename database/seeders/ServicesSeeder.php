<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'fas fa-star',
                'en' => [
                    'title' => 'Loyalty Programs',
                    'short_description' => 'Customized loyalty programs to help brands reward their customers',
                    'description' => "We design and manage customized loyalty programs that help brands reward their customers through:\n\n• Flexible points and redemption systems\n• Cashback and exclusive offers\n• Digital loyalty cards\n• Behavior-based rewards",
                ],
                'ar' => [
                    'title' => 'برامج الولاء',
                    'short_description' => 'برامج ولاء مخصصة تساعد العلامات التجارية على مكافأة عملائها',
                    'description' => "نصمم ونُدير برامج ولاء مخصصة تساعد العلامات التجارية على مكافأة عملائها من خلال:\n\n• نظام نقاط واستبدال مرن\n• كاش باك وعروض حصرية\n• بطاقات رقمية (Digital Cards)\n• مكافآت مبنية على سلوك المستخدم",
                ],
            ],
            [
                'order' => 2,
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'fas fa-users',
                'en' => [
                    'title' => 'Affiliate Programs',
                    'short_description' => 'Fully integrated affiliate marketing system to expand your reach',
                    'description' => "We offer a fully integrated affiliate marketing system that enables businesses to expand their reach through:\n\n• Accurate performance and commission tracking\n• User-friendly dashboards\n• Transparent and fair commission structures\n• Efficient affiliate and partner management",
                ],
                'ar' => [
                    'title' => 'برامج التسويق بالعمولة',
                    'short_description' => 'نظام تسويق بالعمولة متكامل يمكّن الشركات من توسيع قاعدة عملائها',
                    'description' => "نوفر نظام تسويق بالعمولة متكامل يمكّن الشركات من توسيع قاعدة عملائها عبر:\n\n• تتبع دقيق للعمولات والأداء\n• لوحات تحكم سهلة الاستخدام\n• عمولات شفافة وعادلة\n• إدارة المسوقين والشركاء بكفاءة",
                ],
            ],
            [
                'order' => 3,
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'fas fa-credit-card',
                'en' => [
                    'title' => 'Digital Cards & Reward Solutions',
                    'short_description' => 'Easy-to-use digital cards and vouchers for your business',
                    'description' => "Providing easy-to-use digital cards and vouchers, including:\n\n• Gift Cards\n• Discount Cards\n• Promotional Cards\n• Tailored solutions based on business needs",
                ],
                'ar' => [
                    'title' => 'بطاقات رقمية وحلول مكافآت',
                    'short_description' => 'بطاقات مكافآت وقسائم رقمية قابلة للاستخدام بسهولة',
                    'description' => "تقديم بطاقات مكافآت وقسائم رقمية قابلة للاستخدام بسهولة:\n\n• Gift Cards\n• Discount Cards\n• Promotional Cards\n• حلول مخصصة حسب احتياج النشاط التجاري",
                ],
            ],
            [
                'order' => 4,
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'fas fa-chart-bar',
                'en' => [
                    'title' => 'Data Analytics & Reporting',
                    'short_description' => 'Make smarter decisions with detailed analytics and insights',
                    'description' => "We help businesses make smarter decisions by offering:\n\n• Detailed performance reports\n• Customer behavior analysis\n• Loyalty and sales insights",
                ],
                'ar' => [
                    'title' => 'إدارة وتحليل البيانات',
                    'short_description' => 'اتخاذ قرارات ذكية من خلال التحليلات والتقارير التفصيلية',
                    'description' => "نساعد الشركات على اتخاذ قرارات ذكية من خلال:\n\n• تقارير تفصيلية عن أداء الحملات\n• تحليل سلوك العملاء\n• قياس معدل الولاء والمبيعات",
                ],
            ],
            [
                'order' => 5,
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'fas fa-briefcase',
                'en' => [
                    'title' => 'Custom Business Solutions',
                    'short_description' => 'Tailored solutions to fit different business models',
                    'description' => "We deliver tailored solutions to fit different business models, including:\n\n• E-commerce stores\n• Mobile apps\n• Startups and enterprise-level companies",
                ],
                'ar' => [
                    'title' => 'حلول مخصصة للشركات',
                    'short_description' => 'حلول مصممة خصيصًا لتناسب طبيعة كل نشاط تجاري',
                    'description' => "نقدم حلولًا مصممة خصيصًا لتناسب طبيعة كل نشاط تجاري، سواء كان:\n\n• متجر إلكتروني\n• تطبيق\n• علامة تجارية ناشئة أو شركة كبيرة",
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            // Generate slug from English title
            $slug = Str::slug($serviceData['en']['title']);
            
            // Check if service already exists
            $existingService = Service::where('slug', $slug)->first();
            if ($existingService) {
                $this->command->info("Service '{$slug}' already exists. Skipping...");
                continue;
            }

            // Create service using DB to bypass fillable restrictions
            $serviceId = \DB::table('services')->insertGetId([
                'slug' => $slug,
                'title' => $serviceData['en']['title'], // Temporary title for migration
                'icon' => $serviceData['icon'],
                'order' => $serviceData['order'],
                'is_active' => $serviceData['is_active'],
                'is_featured' => $serviceData['is_featured'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $service = Service::find($serviceId);

            // Add English translation
            $service->translateOrNew('en')->title = $serviceData['en']['title'];
            $service->translateOrNew('en')->short_description = $serviceData['en']['short_description'];
            $service->translateOrNew('en')->description = $serviceData['en']['description'];
            $service->translateOrNew('en')->save();

            // Add Arabic translation
            $service->translateOrNew('ar')->title = $serviceData['ar']['title'];
            $service->translateOrNew('ar')->short_description = $serviceData['ar']['short_description'];
            $service->translateOrNew('ar')->description = $serviceData['ar']['description'];
            $service->translateOrNew('ar')->save();

            $this->command->info("Created service: {$serviceData['en']['title']}");
        }

        $this->command->info('Services seeded successfully!');
    }
}
