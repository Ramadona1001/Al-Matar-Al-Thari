<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} - {{ $transaction->transaction_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
        }
        
        .invoice-header {
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        
        .invoice-info {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .invoice-info-row {
            display: table-row;
        }
        
        .invoice-info-cell {
            display: table-cell;
            padding: 8px 0;
            width: 50%;
        }
        
        .invoice-info-label {
            font-weight: bold;
            color: #666;
        }
        
        .company-info, .customer-info {
            margin-bottom: 30px;
        }
        
        .info-section-title {
            font-size: 16px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .info-row {
            margin: 5px 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th {
            background-color: #3b82f6;
            color: #fff;
            padding: 12px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .text-left {
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .totals-section {
            margin-top: 30px;
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin: 8px 0;
        }
        
        .total-label {
            display: table-cell;
            font-weight: bold;
            color: #666;
            width: 70%;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .total-value {
            display: table-cell;
            font-weight: bold;
            color: #333;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .grand-total {
            font-size: 18px;
            color: #3b82f6;
            border-top: 2px solid #3b82f6;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-refunded {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="invoice-title">{{ __('Invoice') }}</div>
            <div class="invoice-info">
                <div class="invoice-info-row">
                    <div class="invoice-info-cell">
                        <span class="invoice-info-label">{{ __('Invoice Number') }}:</span>
                        <span class="info-value">{{ $transaction->transaction_id }}</span>
                    </div>
                    <div class="invoice-info-cell text-right">
                        <span class="invoice-info-label">{{ __('Date') }}:</span>
                        <span class="info-value">{{ $transaction->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
                <div class="invoice-info-row">
                    <div class="invoice-info-cell">
                        <span class="invoice-info-label">{{ __('Status') }}:</span>
                        <span class="status-badge status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                    </div>
                    <div class="invoice-info-cell text-right">
                        <span class="invoice-info-label">{{ __('Payment Method') }}:</span>
                        <span class="info-value">{{ $transaction->payment_method ?? __('N/A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        @if($transaction->company)
        <div class="company-info">
            <div class="info-section-title">{{ __('Company Information') }}</div>
            <div class="info-row">
                <span class="info-label">{{ __('Company Name') }}:</span>
                <span class="info-value">{{ is_array($transaction->company->name) ? ($transaction->company->name[app()->getLocale()] ?? $transaction->company->name['en'] ?? '') : $transaction->company->name }}</span>
            </div>
            @if($transaction->company->email)
            <div class="info-row">
                <span class="info-label">{{ __('Email') }}:</span>
                <span class="info-value">{{ $transaction->company->email }}</span>
            </div>
            @endif
            @if($transaction->company->phone)
            <div class="info-row">
                <span class="info-label">{{ __('Phone') }}:</span>
                <span class="info-value">{{ $transaction->company->phone }}</span>
            </div>
            @endif
            @if($transaction->branch)
            <div class="info-row">
                <span class="info-label">{{ __('Branch') }}:</span>
                <span class="info-value">{{ $transaction->branch->name }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Customer Information -->
        @if($transaction->user)
        <div class="customer-info">
            <div class="info-section-title">{{ __('Customer Information') }}</div>
            <div class="info-row">
                <span class="info-label">{{ __('Customer Name') }}:</span>
                <span class="info-value">{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">{{ __('Email') }}:</span>
                <span class="info-value">{{ $transaction->user->email }}</span>
            </div>
            @if($transaction->user->phone)
            <div class="info-row">
                <span class="info-label">{{ __('Phone') }}:</span>
                <span class="info-value">{{ $transaction->user->phone }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Transaction Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Description') }}</th>
                    <th class="text-right">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @if($transaction->original_price)
                <tr>
                    <td class="text-left">{{ __('Original Price') }}</td>
                    <td class="text-right">﷼{{ number_format($transaction->original_price, 2) }}</td>
                </tr>
                @endif
                @if($transaction->discount_amount > 0)
                <tr>
                    <td class="text-left">{{ __('Discount') }}</td>
                    <td class="text-right" style="color: #10b981;">- ﷼{{ number_format($transaction->discount_amount, 2) }}</td>
                </tr>
                @endif
                @if($transaction->coupon)
                <tr>
                    <td class="text-left">{{ __('Coupon Used') }}: {{ $transaction->coupon->code ?? __('N/A') }}</td>
                    <td class="text-right">-</td>
                </tr>
                @endif
                @if($transaction->loyalty_points_used > 0)
                <tr>
                    <td class="text-left">{{ __('Loyalty Points Used') }}: {{ $transaction->loyalty_points_used }}</td>
                    <td class="text-right">-</td>
                </tr>
                @endif
                @if($transaction->loyalty_points_earned > 0)
                <tr>
                    <td class="text-left">{{ __('Loyalty Points Earned') }}: {{ $transaction->loyalty_points_earned }}</td>
                    <td class="text-right">-</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <div class="total-label">{{ __('Subtotal') }}:</div>
                <div class="total-value">﷼{{ number_format($transaction->original_price ?? $transaction->amount, 2) }}</div>
            </div>
            @if($transaction->discount_amount > 0)
            <div class="total-row">
                <div class="total-label">{{ __('Discount') }}:</div>
                <div class="total-value" style="color: #10b981;">- ﷼{{ number_format($transaction->discount_amount, 2) }}</div>
            </div>
            @endif
            <div class="total-row grand-total">
                <div class="total-label">{{ __('Total Amount') }}:</div>
                <div class="total-value">﷼{{ number_format($transaction->amount, 2) }}</div>
            </div>
        </div>

        @if($transaction->notes)
        <div style="margin-top: 30px; padding: 15px; background-color: #f9fafb; border-radius: 4px;">
            <div style="font-weight: bold; margin-bottom: 5px;">{{ __('Notes') }}:</div>
            <div>{{ $transaction->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>{{ __('Thank you for your business!') }}</div>
            <div style="margin-top: 5px;">{{ __('This is a computer-generated invoice.') }}</div>
        </div>
    </div>
</body>
</html>

