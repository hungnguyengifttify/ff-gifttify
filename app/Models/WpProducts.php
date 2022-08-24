<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'wp_id', 'name', 'slug', 'permalink', 'date_created', 'date_created_gmt', 'date_modified', 'date_modified_gmt', 'type', 'status', 'featured', 'catalog_visibility', 'description', 'short_description', 'sku', 'price', 'regular_price', 'sale_price', 'date_on_sale_from', 'date_on_sale_from_gmt', 'date_on_sale_to', 'date_on_sale_to_gmt', 'price_html', 'on_sale', 'purchasable', 'total_sales', 'virtual', 'downloadable', 'downloads', 'download_limit', 'download_expiry', 'external_url', 'button_text', 'tax_status', 'tax_class', 'manage_stock', 'stock_quantity', 'stock_status', 'backorders', 'backorders_allowed', 'backordered', 'sold_individually', 'weight', 'dimensions', 'shipping_required', 'shipping_taxable', 'shipping_class', 'shipping_class_id', 'reviews_allowed', 'average_rating', 'rating_count', 'related_ids', 'upsell_ids', 'cross_sell_ids', 'parent_id', 'purchase_note', 'categories', 'tags', 'images', 'attributes', 'default_attributes', 'variations', 'grouped_products', 'menu_order', 'meta_data', 'collection'
    ];
}
