# Affiliate Ticket Handling & Settlement Implementation

## ✅ Implemented Features

### 1. Ticket Handling for Affiliate Points

#### TicketOpened Event
- ✅ Event: `App\Events\TicketOpened`
- ✅ Listener: `LockAffiliatePointsOnTicketOpened`
- ✅ Action: Locks affiliate points when ticket is opened with linked transaction
- ✅ Logs: All actions audit-logged with admin_id, reason, timestamp

#### TicketResolved Event
- ✅ Event: `App\Events\TicketResolved` (existing, enhanced)
- ✅ Listener: `HandleAffiliatePointsOnTicketResolved`
- ✅ Actions:
  - If company at fault: Reverses affiliate points via `ReverseAffiliatePointsJob`
  - If customer at fault: Unlocks points to allow settlement
- ✅ Logs: All actions audit-logged

#### ReverseAffiliatePointsJob
- ✅ Reverses affiliate points when company is at fault
- ✅ Updates wallet transaction status to `reversed`
- ✅ Deducts points from wallet (approved or pending based on original status)
- ✅ Creates reversal transaction record
- ✅ Updates affiliate sale status to `rejected`
- ✅ Audit logging with admin_id, reason, timestamp

### 2. Settlement Period Automation

#### Admin Setting
- ✅ Added `affiliate_settlement_days` to `points_settings` table
- ✅ Default: 30 days
- ✅ Updated `PointsSetting` model to include field

#### SettleAffiliatePointsJob
- ✅ Scheduled job runs daily
- ✅ Converts affiliate points from `pending` → `approved`
- ✅ Conditions checked:
  - Older than `settlement_days`
  - Not locked
  - No open ticket for related transaction
- ✅ Updates wallet balances accordingly
- ✅ Creates settled transaction record

#### Scheduling
- ✅ Added to `app/Console/Kernel.php`
- ✅ Runs daily: `$schedule->job(new SettleAffiliatePointsJob)->daily();`

### 3. Points Redemption Protection

#### Wallet Model
- ✅ Updated `redeemAffiliatePoints()` method
- ✅ Checks for pending or locked transactions before allowing redemption
- ✅ Points cannot be redeemed while pending or locked
- ✅ Only approved points can be redeemed

### 4. Database Changes

#### Migrations Created
1. ✅ `add_transaction_id_to_tickets_table` - Links tickets to transactions
2. ✅ `add_locked_status_to_wallet_transactions_table` - Adds `locked` and `reversed` to status enum
3. ✅ `add_affiliate_settlement_days_to_points_settings_table` - Admin configuration

#### Models Updated
- ✅ `Ticket`: Added `transaction_id` to fillable, added `transaction()` relationship
- ✅ `WalletTransaction`: Added `isLocked()`, `isReversed()` methods
- ✅ `PointsSetting`: Added `affiliate_settlement_days` field
- ✅ `Wallet`: Updated redemption validation

### 5. Controllers Updated

#### Customer TicketController
- ✅ Added `transaction_id` to validation
- ✅ Fires `TicketOpened` event when ticket is created

#### Admin TicketController
- ✅ Fires `TicketResolved` event with `shouldReversePoints` flag
- ✅ Removed old manual point reversal logic (now handled by event/job)

### 6. Event Service Provider
- ✅ Registered `TicketOpened` → `LockAffiliatePointsOnTicketOpened`
- ✅ Registered `TicketResolved` → `HandleAffiliatePointsOnTicketResolved`

## 📋 Workflow

### Ticket Opens
1. Customer creates ticket with transaction_id
2. `TicketOpened` event fires
3. `LockAffiliatePointsOnTicketOpened` listener runs
4. Finds affiliate wallet transactions for that transaction
5. Updates status to `locked`
6. Logs action to audit log

### Ticket Resolved - Company at Fault
1. Admin resolves ticket with `should_reverse_points = true`
2. `TicketResolved` event fires
3. `HandleAffiliatePointsOnTicketResolved` listener runs
4. Dispatches `ReverseAffiliatePointsJob`
5. Job reverses points:
   - Updates transaction status to `reversed`
   - Deducts points from wallet
   - Creates reversal transaction
   - Updates affiliate sale status
   - Logs to audit log

### Ticket Resolved - Customer at Fault
1. Admin resolves ticket with `should_reverse_points = false`
2. `TicketResolved` event fires
3. `HandleAffiliatePointsOnTicketResolved` listener runs
4. Unlocks points (status `locked` → `pending`)
5. Points become eligible for settlement

### Settlement Process (Daily)
1. `SettleAffiliatePointsJob` runs daily
2. Finds pending transactions older than `affiliate_settlement_days`
3. Checks conditions:
   - Not locked
   - No open tickets
4. Updates status to `approved`
5. Moves points from pending to approved balance
6. Creates settled transaction record

## 🔒 Security & Validation

- ✅ Points cannot be redeemed while pending or locked
- ✅ All actions are audit-logged
- ✅ Transactions checked for open tickets before settlement
- ✅ Self-referral prevention
- ✅ Multiple validation checks

## 🚀 Running Migrations

Run the following command to apply all database changes:

```bash
php artisan migrate
```

## 📝 Notes

- Settlement job runs daily via Laravel scheduler
- Ensure cron is set up: `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`
- Admin can configure `affiliate_settlement_days` in Points Settings
- All affiliate point actions are logged to audit log for traceability

