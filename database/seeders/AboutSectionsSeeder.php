<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use Illuminate\Support\Str;

class AboutSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        // About Section (Full Width at Top)
        $about = Section::firstOrCreate(
            ['name' => 'about_us', 'page' => 'about'],
            [
                'type' => 'content',
                'order' => 0,
                'columns_per_row' => 1,
                'is_visible' => true,
                'page' => 'about',
            ]
        );
        
        foreach ($locales as $locale) {
            $about->translateOrNew($locale)->title = $locale === 'ar' ? 'من نحن' : 'About Us';
            $about->translateOrNew($locale)->subtitle = $locale === 'ar' ? 'تعرف علينا' : 'Who We Are';
            $about->translateOrNew($locale)->content = $locale === 'ar' 
                ? 'نحن ملتزمون بتقديم حلول موثوقة وفعالة ومستدامة، من التثبيتات السكنية إلى الأنظمة التجارية. نهدف إلى تسخير قوة الابتكار وتقليل التكاليف مع حماية البيئة. نحن نؤمن بأن التحول إلى الطاقة الشمسية لا يجب أن يكون معقداً. لهذا السبب نقدم حلولاً شاملة من الاستشارة والتمويل إلى التثبيت والدعم المستمر. مع التركيز على الجودة والشفافية ورضا العملاء، نحن أكثر من مجرد مزود للطاقة الشمسية، نحن شريكك طويل الأمد في الطاقة، ملتزمون بتشغيل عالمك بثقة ورعاية.'
                : 'We are committed to delivering reliable, efficient, and sustainable solutions, from residential installations to commercial systems. We aim to harness the power of innovation and reduce your costs while protecting the environment. We believe that switching to solar shouldn\'t be complicated. That\'s why we offer end-to-end solutions, from consultation and financing to installation and ongoing support. With a focus on quality, transparency, and customer satisfaction, we are more than just a solar provider, we\'re your long-term energy partner, dedicated to powering your world with confidence and care.';
        }
        $about->save();
        
        // Our Mission Section
        $mission = Section::firstOrCreate(
            ['name' => 'our_mission', 'page' => 'about'],
            [
                'type' => 'content',
                'order' => 1,
                'columns_per_row' => 3,
                'is_visible' => true,
                'page' => 'about',
            ]
        );
        
        foreach ($locales as $locale) {
            $mission->translateOrNew($locale)->title = $locale === 'ar' ? 'مهمتنا' : 'Our Mission';
            $mission->translateOrNew($locale)->subtitle = $locale === 'ar' ? 'ما نؤمن به' : 'What We Believe';
            $mission->translateOrNew($locale)->content = $locale === 'ar' 
                ? 'نحن ملتزمون بتقديم حلول موثوقة وفعالة ومستدامة، من التثبيتات السكنية إلى الأنظمة التجارية. نهدف إلى تسخير قوة الابتكار وتقليل التكاليف مع حماية البيئة.'
                : 'We are committed to delivering reliable, efficient, and sustainable solutions, from residential installations to commercial systems. We aim to harness the power of innovation and reduce your costs while protecting the environment.';
        }
        $mission->save();
        
        // Our Vision Section
        $vision = Section::firstOrCreate(
            ['name' => 'our_vision', 'page' => 'about'],
            [
                'type' => 'content',
                'order' => 2,
                'columns_per_row' => 3,
                'is_visible' => true,
                'page' => 'about',
            ]
        );
        
        foreach ($locales as $locale) {
            $vision->translateOrNew($locale)->title = $locale === 'ar' ? 'رؤيتنا' : 'Our Vision';
            $vision->translateOrNew($locale)->subtitle = $locale === 'ar' ? 'حيث نتجه' : 'Where We\'re Heading';
            $vision->translateOrNew($locale)->content = $locale === 'ar' 
                ? 'أن نكون الرائدين في مجال الحلول المستدامة والموثوقة، وأن نكون الشريك المفضل للعملاء الذين يبحثون عن الجودة والكفاءة والابتكار في كل مشروع.'
                : 'To be the leading provider of sustainable and reliable solutions, and to be the preferred partner for clients seeking quality, efficiency, and innovation in every project.';
        }
        $vision->save();
        
        // Our Message Section
        $message = Section::firstOrCreate(
            ['name' => 'our_message', 'page' => 'about'],
            [
                'type' => 'content',
                'order' => 3,
                'columns_per_row' => 3,
                'is_visible' => true,
                'page' => 'about',
            ]
        );
        
        foreach ($locales as $locale) {
            $message->translateOrNew($locale)->title = $locale === 'ar' ? 'رسالتنا' : 'Our Message';
            $message->translateOrNew($locale)->subtitle = $locale === 'ar' ? 'ما نريد قوله' : 'What We Want to Say';
            $message->translateOrNew($locale)->content = $locale === 'ar' 
                ? 'نؤمن بأن كل عميل يستحق أفضل خدمة ممكنة. نحن هنا لمساعدتك في تحقيق أهدافك من خلال حلول مخصصة تلبي احتياجاتك الفريدة وتتجاوز توقعاتك.'
                : 'We believe that every client deserves the best possible service. We are here to help you achieve your goals through customized solutions that meet your unique needs and exceed your expectations.';
        }
        $message->save();
        
        $this->command->info('About sections (Our Mission, Our Vision, Our Message) have been created successfully!');
    }
}
