<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariants extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'shopify_id', 'product_id', 'shopify_created_at', 'shopify_updated_at', 'barcode', 'compare_at_price', 'fulfillment_service', 'grams', 'weight', 'weight_unit', 'inventory_item_id', 'inventory_management', 'inventory_policy', 'inventory_quantity', 'option1', 'option2', 'option3', 'position', 'price', 'requires_shipping', 'sku', 'taxable', 'title'
    ];
}
