# تقرير الميزات المفقودة - Loyalty + Affiliate Platform

## 📊 ملخص عام
**نسبة الإكمال:** ~40%  
**الحالة:** ⚠️ **ناقص - يحتاج تطوير كبير**

---

## 🔴 القسم 1: الميزات المفقودة بالكامل

### 1.1 نظام التذاكر (Ticket System) ❌ **مفقود تماماً**

**المطلوب:**
- Customers يمكنهم رفع تذاكر ضد Companies أو Services
- إرفاق ملفات (صور، PDF، فيديو اختياري)
- فئات: Service not delivered, Payment issue, Other
- Admin يمكنه مراجعة التذاكر واتخاذ إجراءات

**المفقود:**
- ❌ Model: `Ticket.php` - غير موجود
- ❌ Migration: `create_tickets_table` - غير موجود
- ❌ Controller: `TicketController.php` - غير موجود
- ❌ Views: صفحات رفع التذاكر - غير موجودة
- ❌ Routes: `/customer/tickets/*` - غير موجودة
- ❌ File Upload System: نظام رفع الملفات - غير موجود
- ❌ Ticket Status Workflow: سير عمل التذاكر - غير موجود

**ما يجب إنشاؤه:**
```
- app/Models/Ticket.php
- database/migrations/xxxx_create_tickets_table.php
- database/migrations/xxxx_create_ticket_attachments_table.php
- app/Http/Controllers/Customer/TicketController.php
- app/Http/Controllers/Admin/TicketController.php
- resources/views/customer/tickets/*.blade.php
- resources/views/admin/tickets/*.blade.php
```

---

### 1.2 نظام المحفظة (Wallet System) ❌ **مفقود**

**المطلوب:**
- Wallet منفصل يحتوي على:
  - Loyalty Points (نقاط الولاء)
  - Affiliate Points (نقاط الشراكة)
- نظام Pending → Approved للنقاط
- Automated calculation عند OrderCompleted

**المفقود:**
- ❌ Model: `Wallet.php` - غير موجود
- ❌ Migration: `create_wallets_table` - غير موجود
- ❌ Migration: `create_wallet_transactions_table` - غير موجود
- ❌ Separation: Loyalty Points و Affiliate Points غير منفصلين
- ❌ Pending/Approved Logic: منطق الموافقة على النقاط - غير موجود
- ❌ Wallet Balance Calculation: حساب رصيد المحفظة - غير موجود

**الوضع الحالي:**
- ✅ `LoyaltyPoint` model موجود لكنه لا يدعم:
  - فصل Loyalty عن Affiliate Points
  - نظام Pending → Approved
  - Wallet unified balance

**ما يجب إنشاؤه:**
```
- app/Models/Wallet.php
- app/Models/WalletTransaction.php
- database/migrations/xxxx_create_wallets_table.php
- database/migrations/xxxx_create_wallet_transactions_table.php
- app/Services/WalletService.php
```

---

### 1.3 نظام تجميد/إلغاء تجميد البطاقات والحسابات ❌ **مفقود**

**المطلوب:**
- Admin يمكنه تجميد/إلغاء تجميد:
  - Digital Cards
  - Customer Accounts
  - Company Accounts
- تسجيل السبب (reason)
- منع جميع المعاملات عند التجميد
- منع استحقاق النقاط عند التجميد

**المفقود:**
- ❌ Migration: `frozen_at`, `frozen_reason`, `frozen_by` في `digital_cards` - غير موجود
- ❌ Migration: `frozen_at`, `frozen_reason`, `frozen_by` في `users` - غير موجود
- ❌ Migration: `frozen_at`, `frozen_reason`, `frozen_by` في `companies` - غير موجود
- ❌ Controller Methods: `freeze()`, `unfreeze()` - غير موجودة
- ❌ Middleware: التحقق من حالة التجميد - غير موجود
- ❌ Logic: منع المعاملات عند التجميد - غير موجود

**الوضع الحالي:**
- ✅ `DigitalCard` model له `status` لكن لا يدعم:
  - `frozen` status
  - `frozen_reason`
  - `frozen_by` (admin_id)
  - `frozen_at` timestamp

**ما يجب إنشاؤه:**
```
- database/migrations/xxxx_add_freeze_fields_to_digital_cards.php
- database/migrations/xxxx_add_freeze_fields_to_users.php
- database/migrations/xxxx_add_freeze_fields_to_companies.php
- app/Http/Controllers/Admin/FreezeController.php
- app/Http/Middleware/CheckFrozenStatus.php
- app/Services/FreezeService.php
```

---

### 1.4 نظام الأتمتة (Automation System) ❌ **مفقود تماماً**

**المطلوب:**
- Events: `OrderCompleted`, `PaymentConfirmed`, `TicketResolved`
- Jobs: `CalculateLoyaltyPointsJob`, `AffiliateRewardJob`, `PointsSettlementJob`, `ReversePointsJob`
- Automated calculation عند:
  - OrderCompleted → حساب Loyalty Points من original price
  - Referral Purchase → حساب Affiliate Points تلقائياً
  - Ticket Resolved → عكس النقاط إذا لزم الأمر

**المفقود:**
- ❌ Events: `OrderCompleted`, `PaymentConfirmed`, `TicketResolved` - غير موجودة
- ❌ Jobs: جميع Jobs المطلوبة - غير موجودة
- ❌ Listeners: Event Listeners - غير موجودة
- ❌ Automation: لا يوجد أتمتة في `Transaction::complete()`

**الوضع الحالي:**
- ⚠️ `Transaction::complete()` يحتوي على كود بسيط لإضافة النقاط لكن:
  - لا يستخدم Events/Jobs
  - لا يحسب من original price (يحسب من final_amount)
  - لا يدعم Affiliate Points
  - لا يدعم Pending → Approved

**ما يجب إنشاؤه:**
```
- app/Events/OrderCompleted.php
- app/Events/PaymentConfirmed.php
- app/Events/TicketResolved.php
- app/Jobs/CalculateLoyaltyPointsJob.php
- app/Jobs/AffiliateRewardJob.php
- app/Jobs/PointsSettlementJob.php
- app/Jobs/ReversePointsJob.php
- app/Listeners/CalculateLoyaltyPointsListener.php
- app/Listeners/AwardAffiliatePointsListener.php
- app/Providers/EventServiceProvider.php (تحديث)
```

---

### 1.5 نظام سجلات التدقيق (Audit Logs) ❌ **مفقود**

**المطلوب:**
- تسجيل جميع الأحداث:
  - Point transactions
  - Tickets
  - Freezes/Unfreezes
  - Reversals
  - Admin actions

**المفقود:**
- ❌ Model: `AuditLog.php` - غير موجود
- ❌ Migration: `create_audit_logs_table` - غير موجود
- ❌ Service: `AuditLogService.php` - غير موجود
- ❌ Trait: `Auditable` - غير موجود

**ما يجب إنشاؤه:**
```
- app/Models/AuditLog.php
- database/migrations/xxxx_create_audit_logs_table.php
- app/Services/AuditLogService.php
- app/Traits/Auditable.php
```

---

### 1.6 تصميم البطاقة الرقمية (Digital Card Layout) ⚠️ **غير مكتمل**

**المطلوب:**
- Horizontal/Landscape layout
- QR code على اليسار
- Customer info (name, card ID, wallet balances) على اليمين
- إمكانية الحفظ في Mobile Wallet (Apple/Google Wallet)

**الوضع الحالي:**
- ✅ `DigitalCard` model موجود
- ✅ QR code generation موجود
- ⚠️ Layout: التصميم موجود لكن غير مكتمل
- ❌ Mobile Wallet Integration: غير موجود

**ما يجب إضافته:**
```
- تحديث resources/views/customer/digital-card/index.blade.php
- إضافة Mobile Wallet pass generation
- app/Services/MobileWalletService.php
```

---

### 1.7 نظام استبدال النقاط (Points Redemption) ⚠️ **غير مكتمل**

**المطلوب:**
- Admin يمكنه تعيين: X points = free service
- Redemption auto-deducts points
- Redemption creates Free Order

**الوضع الحالي:**
- ✅ `PointRedemption` model موجود
- ✅ `PointsSetting` model موجود
- ❌ Redemption Flow: سير العمل غير موجود
- ❌ Free Order Creation: إنشاء طلب مجاني - غير موجود
- ❌ Admin Interface: واجهة الإدارة - غير موجودة

**ما يجب إضافته:**
```
- app/Http/Controllers/Admin/PointsRedemptionController.php
- app/Http/Controllers/Customer/PointsRedemptionController.php
- app/Services/PointsRedemptionService.php
- resources/views/admin/points/redemption/*.blade.php
- resources/views/customer/points/redemption/*.blade.php
```

---

### 1.8 قواعد النقاط والشراكة (Points & Affiliate Rules) ⚠️ **غير مكتمل**

**المطلوب:**
- Configurable by Admin:
  - Conversion rate (e.g., 10 SAR = 1 point)
  - Maximum points
  - Expiry rules
  - Redemption rules
  - Affiliate commission rates

**الوضع الحالي:**
- ✅ `PointsSetting` model موجود
- ✅ `AdminPointsController` موجود
- ⚠️ Interface: واجهة الإدارة موجودة لكن غير مكتملة
- ❌ Validation: التحقق من القواعد - غير موجود
- ❌ Application: تطبيق القواعد في الحسابات - غير موجود

**ما يجب إضافته:**
```
- تحديث app/Http/Controllers/Admin/PointsController.php
- تحديث resources/views/admin/points/edit.blade.php
- app/Services/PointsRulesService.php
- app/Services/AffiliateRulesService.php
```

---

## ⚠️ القسم 2: الميزات الموجودة جزئياً

### 2.1 نظام الكوبونات (Coupons) ✅ **موجود جزئياً**

**الموجود:**
- ✅ `Coupon` model
- ✅ `CouponUsage` model
- ✅ Merchant Coupon Controller
- ✅ QR Code generation

**المفقود:**
- ❌ Coupons لا تؤثر على النقاط (يجب أن تحسب من original price)
- ❌ Validation: التحقق من أن الكوبونات لا تؤثر على النقاط

**ما يجب إصلاحه:**
```
- تحديث CalculateLoyaltyPointsJob لاستخدام original price
- إضافة validation في Transaction::complete()
```

---

### 2.2 نظام الشراكة (Affiliate System) ✅ **موجود جزئياً**

**الموجود:**
- ✅ `Affiliate` model
- ✅ `AffiliateSale` model
- ✅ Referral code generation
- ✅ Commission calculation

**المفقود:**
- ❌ Automated reward: لا يتم منح النقاط تلقائياً عند الشراء
- ❌ Self-referral prevention: منع الإحالة الذاتية - غير موجود
- ❌ Abuse prevention: منع الإساءة - غير موجود

**ما يجب إضافته:**
```
- app/Jobs/AffiliateRewardJob.php
- app/Services/AffiliateValidationService.php
- تحديث OrderCompleted event listener
```

---

### 2.3 حساب النقاط التلقائي (Automatic Points Calculation) ⚠️ **غير مكتمل**

**الوضع الحالي:**
- ⚠️ `Transaction::complete()` يحتوي على كود بسيط
- ❌ لا يستخدم Events/Jobs
- ❌ لا يحسب من original price
- ❌ لا يدعم Pending → Approved

**ما يجب إصلاحه:**
```
- إنشاء OrderCompleted event
- إنشاء CalculateLoyaltyPointsJob
- تحديث Transaction::complete() لاستخدام Events
- إضافة original_price في transactions table
```

---

## 📋 القسم 3: قائمة المهام التفصيلية (Laravel Tasks)

### Task 1: إنشاء نظام التذاكر (Ticket System)

#### 1.1 Migration
```php
// database/migrations/xxxx_create_tickets_table.php
Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->string('ticket_number')->unique();
    $table->enum('category', ['service_not_delivered', 'payment_issue', 'other']);
    $table->text('subject');
    $table->text('description');
    $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
    $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
    $table->text('resolution_notes')->nullable();
    $table->timestamp('resolved_at')->nullable();
    $table->timestamps();
});

// database/migrations/xxxx_create_ticket_attachments_table.php
Schema::create('ticket_attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
    $table->string('file_path');
    $table->string('file_name');
    $table->string('file_type');
    $table->integer('file_size');
    $table->timestamps();
});
```

#### 1.2 Model
```php
// app/Models/Ticket.php
class Ticket extends Model
{
    protected $fillable = [
        'ticket_number', 'category', 'subject', 'description',
        'status', 'priority', 'user_id', 'company_id', 'service_id',
        'resolved_by', 'resolution_notes', 'resolved_at'
    ];
    
    public function user() { return $this->belongsTo(User::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function attachments() { return $this->hasMany(TicketAttachment::class); }
    public function resolvedBy() { return $this->belongsTo(User::class, 'resolved_by'); }
}
```

#### 1.3 Controller
```php
// app/Http/Controllers/Customer/TicketController.php
- index() - قائمة التذاكر
- create() - إنشاء تذكرة جديدة
- store() - حفظ التذكرة مع المرفقات
- show() - عرض التذكرة
- update() - تحديث التذكرة

// app/Http/Controllers/Admin/TicketController.php
- index() - قائمة جميع التذاكر
- show() - عرض التذكرة
- resolve() - حل التذكرة
- freezeAccount() - تجميد الحساب
- reversePoints() - عكس النقاط
```

---

### Task 2: إنشاء نظام المحفظة (Wallet System)

#### 2.1 Migration
```php
// database/migrations/xxxx_create_wallets_table.php
Schema::create('wallets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
    $table->integer('loyalty_points_balance')->default(0);
    $table->integer('affiliate_points_balance')->default(0);
    $table->integer('loyalty_points_pending')->default(0);
    $table->integer('affiliate_points_pending')->default(0);
    $table->timestamps();
});

// database/migrations/xxxx_create_wallet_transactions_table.php
Schema::create('wallet_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
    $table->enum('type', ['loyalty', 'affiliate']);
    $table->enum('transaction_type', ['earned', 'redeemed', 'reversed', 'settled']);
    $table->integer('points');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->string('source_type')->nullable();
    $table->unsignedBigInteger('source_id')->nullable();
    $table->text('description')->nullable();
    $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('approved_at')->nullable();
    $table->timestamps();
});
```

#### 2.2 Model
```php
// app/Models/Wallet.php
class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'loyalty_points_balance', 'affiliate_points_balance',
        'loyalty_points_pending', 'affiliate_points_pending'
    ];
    
    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class); }
}
```

---

### Task 3: إضافة نظام التجميد (Freeze System)

#### 3.1 Migration
```php
// database/migrations/xxxx_add_freeze_fields_to_digital_cards.php
Schema::table('digital_cards', function (Blueprint $table) {
    $table->boolean('is_frozen')->default(false);
    $table->text('frozen_reason')->nullable();
    $table->foreignId('frozen_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('frozen_at')->nullable();
});

// database/migrations/xxxx_add_freeze_fields_to_users.php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_frozen')->default(false);
    $table->text('frozen_reason')->nullable();
    $table->foreignId('frozen_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('frozen_at')->nullable();
});

// database/migrations/xxxx_add_freeze_fields_to_companies.php
Schema::table('companies', function (Blueprint $table) {
    $table->boolean('is_frozen')->default(false);
    $table->text('frozen_reason')->nullable();
    $table->foreignId('frozen_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('frozen_at')->nullable();
});
```

#### 3.2 Middleware
```php
// app/Http/Middleware/CheckFrozenStatus.php
- التحقق من حالة التجميد قبل المعاملات
- منع الوصول إلى Dashboard إذا كان مجمداً
```

---

### Task 4: إنشاء نظام الأتمتة (Automation)

#### 4.1 Events
```php
// app/Events/OrderCompleted.php
class OrderCompleted
{
    public $transaction;
    public function __construct(Transaction $transaction) { ... }
}

// app/Events/PaymentConfirmed.php
// app/Events/TicketResolved.php
```

#### 4.2 Jobs
```php
// app/Jobs/CalculateLoyaltyPointsJob.php
- حساب النقاط من original price
- إضافة إلى wallet كـ pending
- تسجيل في audit log

// app/Jobs/AffiliateRewardJob.php
- حساب Affiliate Points
- التحقق من self-referral
- إضافة إلى wallet

// app/Jobs/PointsSettlementJob.php
- تحويل pending → approved
- تحديث wallet balance

// app/Jobs/ReversePointsJob.php
- عكس النقاط عند حل التذكرة
- تحديث wallet balance
```

#### 4.3 Listeners
```php
// app/Listeners/CalculateLoyaltyPointsListener.php
- استدعاء CalculateLoyaltyPointsJob

// app/Listeners/AwardAffiliatePointsListener.php
- استدعاء AffiliateRewardJob
```

---

### Task 5: إضافة original_price إلى Transactions

#### 5.1 Migration
```php
// database/migrations/xxxx_add_original_price_to_transactions.php
Schema::table('transactions', function (Blueprint $table) {
    $table->decimal('original_price', 10, 2)->after('amount');
});
```

---

### Task 6: إنشاء نظام Audit Logs

#### 6.1 Migration
```php
// database/migrations/xxxx_create_audit_logs_table.php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->string('action'); // 'points_earned', 'points_redeemed', 'card_frozen', etc.
    $table->string('model_type');
    $table->unsignedBigInteger('model_id');
    $table->text('description');
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
});
```

---

## 🔧 القسم 4: التحسينات المقترحة

### 4.1 Security
- إضافة rate limiting للـ API endpoints
- إضافة CSRF protection
- إضافة input validation أقوى
- إضافة SQL injection prevention

### 4.2 Performance
- إضافة caching للنقاط والمحافظ
- إضافة queue للـ jobs الثقيلة
- إضافة database indexing

### 4.3 Code Quality
- إضافة Unit Tests
- إضافة Feature Tests
- إضافة Code Documentation
- إضافة Type Hints

---

## 📊 ملخص الأولويات

### 🔴 أولوية عالية (يجب تنفيذها فوراً)
1. نظام التذاكر (Ticket System)
2. نظام المحفظة (Wallet System)
3. نظام التجميد (Freeze System)
4. نظام الأتمتة (Automation System)
5. إضافة original_price إلى Transactions

### ⚠️ أولوية متوسطة
1. نظام Audit Logs
2. تحسين تصميم البطاقة الرقمية
3. نظام استبدال النقاط الكامل
4. قواعد النقاط والشراكة

### 💡 أولوية منخفضة
1. Mobile Wallet Integration
2. تحسينات الأداء
3. تحسينات الأمان
4. Unit Tests

---

## ✅ الخلاصة

**المشروع يحتاج إلى:**
- 6 Models جديدة
- 15+ Migrations جديدة
- 10+ Controllers جديدة
- 5+ Services جديدة
- 4 Events جديدة
- 4 Jobs جديدة
- 2+ Middleware جديدة
- 20+ Views جديدة

**الوقت المقدر:** 4-6 أسابيع عمل

**الحالة الحالية:** 40% مكتمل  
**الحالة المطلوبة:** 100% مكتمل

