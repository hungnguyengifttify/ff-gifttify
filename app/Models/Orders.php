<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'shopify_id', 'admin_graphql_api_id', 'app_id', 'browser_ip', 'buyer_accepts_marketing', 'cancel_reason', 'cancelled_at', 'cart_token', 'checkout_id', 'checkout_token', 'client_details', 'closed_at', 'confirmed', 'contact_email', 'shopify_created_at', 'currency', 'current_subtotal_price', 'current_subtotal_price_set', 'current_total_discounts', 'current_total_discounts_set', 'current_total_duties_set', 'current_total_price', 'current_total_price_set', 'current_total_tax', 'current_total_tax_set', 'customer_locale', 'device_id', 'discount_codes', 'email', 'estimated_taxes', 'financial_status', 'fulfillment_status', 'gateway', 'landing_site', 'landing_site_ref', 'location_id', 'name', 'note', 'note_attributes', 'number', 'order_number', 'order_status_url', 'original_total_duties_set', 'payment_gateway_names', 'phone', 'presentment_currency', 'processed_at', 'processing_method', 'reference', 'source_identifier', 'source_name', 'source_url', 'subtotal_price', 'subtotal_price_set', 'tags', 'tax_lines', 'taxes_included', 'test', 'token', 'total_discounts', 'total_discounts_set', 'total_line_items_price', 'total_line_items_price_set', 'total_outstanding', 'total_price', 'total_price_set', 'total_price_usd', 'total_shipping_price_set', 'total_tax', 'total_tax_set', 'total_tip_received', 'total_weight', 'shopify_updated_at', 'user_id', 'customer', 'discount_applications', 'fulfillments', 'line_items', 'payment_terms', 'refunds', 'shipping_address', 'shipping_lines'
    ];
}
