<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'cod',
        'stripe',
        'paypal',
        'razor',
        'flutterwave',
        'stripeSecretKey',
        'stripePublicKey',
        'paystackSecretKey',
        'paystackPublicKey',
        'paypalClientId',
        'paypalSecret',
        'razorPublishKey',
        'razorSecretKey',
        'ravePublicKey',
        'raveSecretKey',
        'raveWebhookSecretHash',
        'flutterDebugMode',
        'wallet'
    ];

    protected $table = 'payment_setting';

}
