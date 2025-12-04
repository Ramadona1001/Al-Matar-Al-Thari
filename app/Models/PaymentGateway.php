<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'api_key',
        'api_secret',
        'merchant_id',
        'public_key',
        'private_key',
        'webhook_secret',
        'test_mode',
        'status',
        'settings',
        'supported_currencies',
        'supported_countries',
        'processing_fees',
        'minimum_amount',
        'maximum_amount',
        'processing_time',
        'description'
    ];

    protected $casts = [
        'test_mode' => 'boolean',
        'status' => 'boolean',
        'settings' => 'array',
        'supported_currencies' => 'array',
        'supported_countries' => 'array',
        'processing_fees' => 'array',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
        'private_key',
        'webhook_secret',
    ];

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function isActive()
    {
        return $this->status === true;
    }

    public function isTestMode()
    {
        return $this->test_mode === true;
    }

    public function supportsCurrency($currency)
    {
        return in_array(strtoupper($currency), $this->supported_currencies ?? []);
    }

    public function supportsCountry($country)
    {
        return in_array(strtoupper($country), $this->supported_countries ?? []);
    }

    public function getProcessingFee($amount, $currency = 'USD')
    {
        $fees = $this->processing_fees ?? [];
        
        if (isset($fees['percentage'])) {
            $fee = $amount * ($fees['percentage'] / 100);
        } else {
            $fee = 0;
        }

        if (isset($fees['fixed'][$currency])) {
            $fee += $fees['fixed'][$currency];
        } elseif (isset($fees['fixed']['USD'])) {
            $fee += $fees['fixed']['USD'];
        }

        return $fee;
    }

    public function getConfig()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'test_mode' => $this->test_mode,
            'public_key' => $this->public_key,
            'merchant_id' => $this->merchant_id,
            'supported_currencies' => $this->supported_currencies,
            'processing_fees' => $this->processing_fees,
            'minimum_amount' => $this->minimum_amount,
            'maximum_amount' => $this->maximum_amount,
        ];
    }
}