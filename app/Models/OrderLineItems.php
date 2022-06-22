<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLineItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'shopify_id', 'order_id', 'product_id', 'variant_id', 'admin_graphql_api_id', 'fulfillable_quantity', 'fulfillment_service', 'fulfillment_status', 'gift_card', 'grams', 'name', 'price', 'price_set', 'product_exists', 'properties', 'quantity', 'requires_shipping', 'sku', 'taxable', 'title', 'total_discount', 'total_discount_set', 'variant_inventory_management', 'variant_title', 'vendor', 'tax_lines', 'duties', 'discount_allocations'
    ];
}
