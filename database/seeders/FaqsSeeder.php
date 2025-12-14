<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use Illuminate\Support\Str;

class FaqsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'order' => 1,
                'category' => 'general',
                'en' => [
                    'question' => 'What is Are Cards?',
                    'answer' => 'Are Cards is a platform specializing in loyalty and affiliate programs, helping businesses grow sales and build long-term customer relationships, while allowing individuals to earn rewards and commissions.'
                ],
                'ar' => [
                    'question' => 'ما هي منصة Are Cards؟',
                    'answer' => 'Are Cards هي منصة متخصصة في برامج الولاء والتسويق بالعمولة، تساعد الشركات على زيادة المبيعات وبناء علاقات طويلة الأمد مع العملاء، وتتيح للأفراد فرصة تحقيق دخل إضافي من خلال التسويق بالعمولة.'
                ]
            ],
            [
                'order' => 2,
                'category' => 'general',
                'en' => [
                    'question' => 'How can I benefit as a user?',
                    'answer' => 'You can earn points, rewards, or cashback when interacting with partner brands, as well as generate income through the affiliate program.'
                ],
                'ar' => [
                    'question' => 'كيف أستفيد من Are Cards كمستخدم؟',
                    'answer' => 'يمكنك كسب نقاط، مكافآت، أو كاش باك عند الشراء أو التفاعل مع العلامات التجارية المشاركة، بالإضافة إلى إمكانية تحقيق أرباح من خلال برنامج التسويق بالعمولة.'
                ]
            ],
            [
                'order' => 3,
                'category' => 'loyalty',
                'en' => [
                    'question' => 'How do loyalty programs work?',
                    'answer' => 'When you shop or engage with participating businesses, you earn points or rewards based on each program\'s rules, which can be redeemed for offers or benefits.'
                ],
                'ar' => [
                    'question' => 'كيف تعمل برامج الولاء؟',
                    'answer' => 'عند التعامل مع أي شركة مشاركة، تحصل على نقاط أو مكافآت حسب شروط البرنامج، ويمكنك استبدالها بعروض أو خصومات أو مزايا أخرى داخل المنصة.'
                ]
            ],
            [
                'order' => 4,
                'category' => 'affiliate',
                'en' => [
                    'question' => 'What is the affiliate program?',
                    'answer' => 'It allows users to promote partner products or services and earn commissions for every successful sale or action completed through their referral.'
                ],
                'ar' => [
                    'question' => 'ما هو برنامج التسويق بالعمولة؟',
                    'answer' => 'هو نظام يتيح لك الترويج لمنتجات أو خدمات الشركات المشاركة، وتحصل مقابل ذلك على عمولة عن كل عملية بيع أو إجراء يتم من خلالك.'
                ]
            ],
            [
                'order' => 5,
                'category' => 'affiliate',
                'en' => [
                    'question' => 'How are commissions calculated?',
                    'answer' => 'Commissions are calculated automatically based on the rates and terms defined by each business on the platform.'
                ],
                'ar' => [
                    'question' => 'كيف يتم احتساب العمولات؟',
                    'answer' => 'يتم احتساب العمولات تلقائيًا وفقًا للنسب والشروط المحددة لكل شركة داخل منصة Are Cards.'
                ]
            ],
            [
                'order' => 6,
                'category' => 'affiliate',
                'en' => [
                    'question' => 'When can I withdraw my earnings?',
                    'answer' => 'Earnings can be withdrawn once the minimum payout threshold is reached, according to the platform\'s payment policies.'
                ],
                'ar' => [
                    'question' => 'متى يمكنني سحب الأرباح؟',
                    'answer' => 'يمكن سحب الأرباح بعد الوصول إلى الحد الأدنى للسحب، ووفقًا لسياسات الدفع المعتمدة داخل المنصة.'
                ]
            ],
            [
                'order' => 7,
                'category' => 'general',
                'en' => [
                    'question' => 'Is registration free?',
                    'answer' => 'Yes, registration is free for users. Pricing plans may apply for businesses.'
                ],
                'ar' => [
                    'question' => 'هل التسجيل في Are Cards مجاني؟',
                    'answer' => 'نعم، التسجيل في المنصة مجاني للمستخدمين، وقد تختلف الخطط والخدمات المقدمة للشركات.'
                ]
            ],
            [
                'order' => 8,
                'category' => 'loyalty',
                'en' => [
                    'question' => 'Can loyalty points be converted into cash?',
                    'answer' => 'This depends on each loyalty program\'s terms. Points cannot be converted into cash unless explicitly stated.'
                ],
                'ar' => [
                    'question' => 'هل يمكن تحويل النقاط إلى أموال نقدية؟',
                    'answer' => 'ذلك يعتمد على شروط برنامج الولاء الخاص بكل شركة، حيث لا يمكن تحويل النقاط إلى نقد إلا إذا كان مذكورًا صراحة.'
                ]
            ],
            [
                'order' => 9,
                'category' => 'business',
                'en' => [
                    'question' => 'How can businesses join Are Cards?',
                    'answer' => 'Businesses can sign up through the website or contact us directly to create a customized loyalty or affiliate program.'
                ],
                'ar' => [
                    'question' => 'كيف يمكن للشركات الانضمام إلى Are Cards؟',
                    'answer' => 'يمكن للشركات التسجيل عبر الموقع أو التواصل معنا مباشرة لبدء إنشاء برنامج ولاء أو تسويق بالعمولة مخصص.'
                ]
            ],
            [
                'order' => 10,
                'category' => 'general',
                'en' => [
                    'question' => 'Is my personal data secure?',
                    'answer' => 'Yes, we take data protection seriously and handle information in accordance with our Privacy Policy.'
                ],
                'ar' => [
                    'question' => 'هل بياناتي الشخصية آمنة؟',
                    'answer' => 'نعم، نلتزم بحماية بيانات المستخدمين وفقًا لسياسة الخصوصية المعتمدة لدينا.'
                ]
            ],
            [
                'order' => 11,
                'category' => 'support',
                'en' => [
                    'question' => 'How can I contact Are Cards support?',
                    'answer' => 'You can reach us via:<br>📧 Email: [Email Address]<br>📞 Phone: [Phone number, if available]'
                ],
                'ar' => [
                    'question' => 'كيف أتواصل مع دعم Are Cards؟',
                    'answer' => 'يمكنك التواصل معنا عبر:<br>📧 [البريد الإلكتروني]<br>📞 [رقم الهاتف – إن وجد]'
                ]
            ],
        ];

        foreach ($faqs as $faqData) {
            // Check if FAQ already exists by question in English
            $existingFaq = Faq::whereHas('translations', function($query) use ($faqData) {
                $query->where('locale', 'en')
                      ->where('question', $faqData['en']['question']);
            })->first();

            if ($existingFaq) {
                $this->command->info("FAQ '{$faqData['en']['question']}' already exists. Skipping...");
                continue;
            }

            $faq = Faq::create([
                'category' => $faqData['category'] ?? null,
                'order' => $faqData['order'] ?? 0,
                'is_active' => true,
            ]);

            // Save translations
            foreach (['en', 'ar'] as $locale) {
                if (isset($faqData[$locale])) {
                    $faq->translateOrNew($locale)->question = $faqData[$locale]['question'];
                    $faq->translateOrNew($locale)->answer = $faqData[$locale]['answer'];
                }
            }
            $faq->save();

            $this->command->info("Created FAQ: {$faqData['en']['question']}");
        }
    }
}

