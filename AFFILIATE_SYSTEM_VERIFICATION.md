# Affiliate System Verification

## ✅ Implemented Features

### 1. Automatic Affiliate Account
- ✅ Every Customer is automatically an Affiliate (no separate registration)
- ✅ `referral_code` generated automatically when customer account is created
- ✅ Referral link format: `https://app-domain/register?ref={referral_code}`

### 2. Referral Registration Flow
- ✅ On registration with `ref` parameter, `referred_by_user_id` is saved
- ✅ Referral attribution is locked (cannot be changed after registration)
- ✅ Self-referral prevention in multiple places
- ✅ Duplicate referral prevention (locked on registration)

### 3. Affiliate Reward Trigger
- ✅ Affiliate points are calculated ONLY on `OrderCompleted` event
- ✅ Points are NOT awarded on registration or login
- ✅ Checks transaction status (must be completed, not refunded)

### 4. Conditions
- ✅ Order must be completed
- ✅ Order must not be refunded
- ✅ Self-referral detection (multiple checks)
- ✅ Affiliate must be active
- ✅ Referrer must not be frozen

### 5. Affiliate Points Calculation
- ✅ Admin-configurable rules via `PointsSetting`:
  - Fixed points per order (`referral_bonus_points`)
  - Points per order amount (`earn_rate`)
- ✅ Points added as `pending` in wallet
- ⚠️ **Note**: Settlement period auto-approval needs to be implemented via scheduled job

### 6. Coupon Interaction
- ✅ Coupon usage does NOT affect affiliate points
- ✅ Points calculated based on `original_price` (original service price)

### 7. Ticket Interaction
- ⚠️ **Missing**: Ticket handling for affiliate points:
  - Lock affiliate points when ticket is opened
  - Reverse affiliate points when ticket resolved (company at fault)
  - Approve affiliate points when ticket resolved (customer at fault)

### 8. Database Tables
- ✅ `affiliates` table (with referral_code, referral_link)
- ✅ `affiliate_sales` table (with transaction_id)
- ✅ `users` table (with referred_by_user_id) - **NEW**
- ✅ `referrals` table (legacy, optional)
- ✅ `wallets` table (for affiliate_points_balance, affiliate_points_pending)
- ✅ `wallet_transactions` table (linked to wallet)
- ✅ `points_settings` table (admin configuration)

### 9. Automation
- ✅ Event: `OrderCompleted`
- ✅ Job: `AffiliateRewardJob` (calculates and awards points)
- ⚠️ **Missing**: Job: `ReverseAffiliatePointsJob` (for ticket resolution)

## 📋 Implementation Details

### Registration Flow
```php
// RegisteredUserController.php
- Checks for `ref` parameter in request
- Finds affiliate by referral_code
- Saves referred_by_user_id (locked)
- Creates affiliate account for new customer automatically
- Stores referral_code in session/cookie for fallback
```

### Reward Calculation
```php
// AffiliateRewardJob.php
- Triggers on OrderCompleted event
- Checks transaction status (completed, not refunded)
- Gets referrer from referred_by_user_id (priority) or session/cookie
- Prevents self-referral
- Calculates points from PointsSetting
- Adds points as pending
- Creates wallet transaction
- Records affiliate sale
```

## ⚠️ Missing/Pending Features

1. **Settlement Period Auto-Approval**
   - Need scheduled job to convert pending → approved after X days
   - Or manual approval process

2. **Ticket Handling**
   - Need listener for TicketResolved event
   - Need to lock affiliate points when ticket opened
   - Need to reverse/approve based on fault

3. **Attribution Window**
   - Admin-configurable window (e.g., 30 days) not yet implemented

## 🎯 Next Steps

1. Create `ReverseAffiliatePointsJob` for ticket resolution
2. Add ticket event listeners
3. Create scheduled job for pending → approved conversion
4. Add admin settings for attribution window

