<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use Illuminate\Support\Str;

class PrivacyPolicyPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slug = 'privacy';
        
        // Check if page already exists
        $existingPage = Page::where('slug', $slug)->first();
        if ($existingPage) {
            $this->command->info("Page with slug '{$slug}' already exists. Updating...");
            $page = $existingPage;
        } else {
            // Create new page
            $page = new Page([
                'slug' => $slug,
                'is_published' => true,
                'show_in_menu' => true,
                'order' => 0,
                'template' => 'default',
            ]);
            $page->save();
        }

        // Update page basic info
        $page->is_published = true;
        $page->show_in_menu = true;
        $page->order = 0;
        $page->save();

        // English Translation
        $page->translateOrNew('en')->title = 'Privacy Policy';
        $page->translateOrNew('en')->menu_label = 'Privacy Policy';
        $page->translateOrNew('en')->excerpt = 'Are Cards respects your privacy and is committed to protecting the personal data collected through our platform and services.';
        $page->translateOrNew('en')->content = '<h2>Privacy Policy – Are Cards</h2>
<p><strong>Last Updated: ' . date('Y-m-d') . '</strong></p>

<p>Are Cards respects your privacy and is committed to protecting the personal data collected through our platform and services.</p>

<h3>1. Information We Collect</h3>
<p>We may collect the following types of information:</p>
<ul>
    <li>Personal information (such as name, email address, phone number)</li>
    <li>Account and login details</li>
    <li>Transaction and activity data within the platform</li>
    <li>Technical data (IP address, device type, browser)</li>
    <li>Loyalty and affiliate program data</li>
</ul>

<h3>2. How We Use Information</h3>
<p>We use collected information to:</p>
<ul>
    <li>Operate and manage the platform and services</li>
    <li>Improve user experience</li>
    <li>Manage loyalty and affiliate programs</li>
    <li>Process payments and commissions</li>
    <li>Communicate with users</li>
    <li>Comply with legal and regulatory requirements</li>
</ul>

<h3>3. Data Sharing</h3>
<p>We do not sell or rent user data.</p>
<p>Information may be shared only with:</p>
<ul>
    <li>Partner businesses providing services</li>
    <li>Payment and technology service providers</li>
    <li>Legal authorities when required by law</li>
</ul>

<h3>4. Data Security</h3>
<p>We implement appropriate technical and organizational measures to protect data from unauthorized access, loss, or misuse.</p>

<h3>5. Cookies</h3>
<p>Are Cards uses cookies to enhance performance and user experience. Users can manage cookie settings through their browser.</p>

<h3>6. User Rights</h3>
<p>Users have the right to:</p>
<ul>
    <li>Access their personal data</li>
    <li>Update or correct their information</li>
    <li>Request account or data deletion (subject to legal requirements)</li>
    <li>Withdraw consent for data processing</li>
</ul>

<h3>7. Data Retention</h3>
<p>We retain personal data only as long as necessary to provide services or meet legal obligations.</p>

<h3>8. External Links</h3>
<p>Our platform may contain links to third-party websites. We are not responsible for their privacy practices.</p>

<h3>9. Changes to This Policy</h3>
<p>Are Cards may update this Privacy Policy at any time. Users will be notified of significant changes.</p>

<h3>10. Contact Us</h3>
<p>For any questions regarding this Privacy Policy, please contact us at:</p>
<p>📧 [Email Address]</p>';

        $page->translateOrNew('en')->meta_title = 'Privacy Policy - Are Cards';
        $page->translateOrNew('en')->meta_description = 'Are Cards respects your privacy and is committed to protecting the personal data collected through our platform and services.';
        $page->translateOrNew('en')->meta_keywords = 'privacy policy, data protection, user rights, cookies, data security';
        $page->translateOrNew('en')->save();

        // Arabic Translation
        $page->translateOrNew('ar')->title = 'سياسة الخصوصية';
        $page->translateOrNew('ar')->menu_label = 'سياسة الخصوصية';
        $page->translateOrNew('ar')->excerpt = 'تحترم Are Cards خصوصية مستخدميها، وتلتزم بحماية البيانات الشخصية التي يتم جمعها واستخدامها عند استخدام منصتنا وخدماتنا.';
        $page->translateOrNew('ar')->content = '<h2>سياسة الخصوصية – Are Cards</h2>
<p><strong>آخر تحديث: ' . date('Y-m-d') . '</strong></p>

<p>تحترم Are Cards خصوصية مستخدميها، وتلتزم بحماية البيانات الشخصية التي يتم جمعها واستخدامها عند استخدام منصتنا وخدماتنا.</p>

<h3>1. المعلومات التي نقوم بجمعها</h3>
<p>قد نقوم بجمع الأنواع التالية من المعلومات:</p>
<ul>
    <li>المعلومات الشخصية (مثل الاسم، البريد الإلكتروني، رقم الهاتف)</li>
    <li>معلومات الحساب وتسجيل الدخول</li>
    <li>معلومات المعاملات والأنشطة داخل المنصة</li>
    <li>بيانات تقنية مثل عنوان IP ونوع الجهاز والمتصفح</li>
    <li>بيانات خاصة ببرامج الولاء والتسويق بالعمولة</li>
</ul>

<h3>2. كيفية استخدام المعلومات</h3>
<p>نستخدم المعلومات التي نجمعها من أجل:</p>
<ul>
    <li>تشغيل وإدارة المنصة والخدمات</li>
    <li>تحسين تجربة المستخدم</li>
    <li>إدارة برامج الولاء والتسويق بالعمولة</li>
    <li>معالجة المدفوعات والعمولات</li>
    <li>التواصل مع المستخدمين</li>
    <li>الالتزام بالمتطلبات القانونية والتنظيمية</li>
</ul>

<h3>3. مشاركة المعلومات</h3>
<p>لا نقوم ببيع أو تأجير بيانات المستخدمين.</p>
<p>قد نقوم بمشاركة المعلومات فقط مع:</p>
<ul>
    <li>الشركات الشريكة لتقديم الخدمات</li>
    <li>مزودي خدمات الدفع والتقنيات</li>
    <li>الجهات القانونية عند الطلب الرسمي</li>
</ul>

<h3>4. حماية البيانات</h3>
<p>نلتزم باتخاذ الإجراءات التقنية والتنظيمية المناسبة لحماية البيانات من الوصول غير المصرح به أو الفقدان أو التعديل.</p>

<h3>5. ملفات تعريف الارتباط (Cookies)</h3>
<p>تستخدم Are Cards ملفات تعريف الارتباط لتحسين الأداء وتجربة الاستخدام، ويمكن للمستخدم التحكم في إعدادات الكوكيز من خلال المتصفح.</p>

<h3>6. حقوق المستخدم</h3>
<p>يحق للمستخدم:</p>
<ul>
    <li>الوصول إلى بياناته الشخصية</li>
    <li>تعديل أو تحديث بياناته</li>
    <li>طلب حذف الحساب أو البيانات (وفقًا للمتطلبات القانونية)</li>
    <li>سحب الموافقة على استخدام البيانات</li>
</ul>

<h3>7. الاحتفاظ بالبيانات</h3>
<p>نحتفظ بالبيانات طالما كانت ضرورية لتقديم الخدمات أو للالتزام بالمتطلبات القانونية.</p>

<h3>8. روابط خارجية</h3>
<p>قد تحتوي المنصة على روابط لمواقع خارجية، ولسنا مسؤولين عن سياسات الخصوصية الخاصة بها.</p>

<h3>9. التعديلات على سياسة الخصوصية</h3>
<p>يحق لـ Are Cards تحديث هذه السياسة في أي وقت، وسيتم إخطار المستخدمين في حال وجود تغييرات جوهرية.</p>

<h3>10. التواصل معنا</h3>
<p>لأي استفسارات تتعلق بسياسة الخصوصية، يرجى التواصل معنا عبر:</p>
<p>📧 [البريد الإلكتروني]</p>';

        $page->translateOrNew('ar')->meta_title = 'سياسة الخصوصية - Are Cards';
        $page->translateOrNew('ar')->meta_description = 'تحترم Are Cards خصوصية مستخدميها، وتلتزم بحماية البيانات الشخصية التي يتم جمعها واستخدامها عند استخدام منصتنا وخدماتنا.';
        $page->translateOrNew('ar')->meta_keywords = 'سياسة الخصوصية، حماية البيانات، حقوق المستخدم، ملفات تعريف الارتباط، أمان البيانات';
        $page->translateOrNew('ar')->save();

        $this->command->info('Privacy Policy page created/updated successfully!');
        $this->command->info('Slug: ' . $slug);
    }
}
