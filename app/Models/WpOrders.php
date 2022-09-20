<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WpOrders extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'wp_id', 'parent_id', 'number', 'order_key', 'created_via', 'version', 'status', 'currency', 'date_created', 'date_created_gmt', 'date_modified', 'date_modified_gmt', 'discount_total', 'discount_tax', 'shipping_total', 'shipping_tax', 'cart_tax', 'total', 'total_tax', 'prices_include_tax', 'customer_id', 'customer_ip_address', 'customer_user_agent', 'customer_note', 'billing', 'shipping', 'payment_method', 'payment_method_title', 'transaction_id', 'date_paid', 'date_paid_gmt', 'date_completed', 'date_completed_gmt', 'cart_hash', 'meta_data', 'line_items', 'shipping_lines', 'fee_lines', 'coupon_lines', 'refunds', '_links'
    ];
}
