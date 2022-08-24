<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpOrderLineItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'wp_id', 'order_id', 'name', 'product_id', 'variation_id', 'quantity', 'tax_class', 'subtotal', 'subtotal_tax', 'total', 'total_tax', 'taxes', 'meta_data', 'sku', 'price'
    ];
}
