<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'shopify_id', 'admin_graphql_api_id', 'app_id', 'browser_ip', 'buyer_accepts_marketing', 'cancel_reason', 'cancelled_at', 'cart_token', 'checkout_id', 'checkout_token', 'client_details', 'closed_at', 'confirmed', 'contact_email', 'shopify_created_at', 'currency', 'current_subtotal_price', 'current_subtotal_price_set', 'current_total_discounts', 'current_total_discounts_set', 'current_total_duties_set', 'current_total_price', 'current_total_price_set', 'current_total_tax', 'current_total_tax_set', 'customer_locale', 'device_id', 'discount_codes', 'email', 'estimated_taxes', 'financial_status', 'fulfillment_status', 'gateway', 'landing_site', 'landing_site_ref', 'location_id', 'name', 'note', 'note_attributes', 'number', 'order_number', 'order_status_url', 'original_total_duties_set', 'payment_gateway_names', 'phone', 'presentment_currency', 'processed_at', 'processing_method', 'reference', 'source_identifier', 'source_name', 'source_url', 'subtotal_price', 'subtotal_price_set', 'tags', 'tax_lines', 'taxes_included', 'test', 'token', 'total_discounts', 'total_discounts_set', 'total_line_items_price', 'total_line_items_price_set', 'total_outstanding', 'total_price', 'total_price_set', 'total_price_usd', 'total_shipping_price_set', 'total_tax', 'total_tax_set', 'total_tip_received', 'total_weight', 'shopify_updated_at', 'user_id', 'customer', 'discount_applications', 'fulfillments', 'line_items', 'payment_terms', 'refunds', 'shipping_address', 'shipping_lines'
    ];

    public static function getList ($fromDate, $toDate, $displayItemQty) {
        $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->format('Y-m-d');
        $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->addDays(1)->format('Y-m-d');

        $orders = Orders::select(DB::raw("
            order_line_items.properties, orders.shipping_address, products.product_type , order_line_items.variant_title, order_line_items.name as item_name,
            CONVERT_TZ(orders.shopify_created_at,'UTC','Asia/Ho_Chi_Minh') as shopify_created_at,
            order_line_items.sku, order_line_items.quantity, order_line_items.price,
            orders.store, orders.name,
            (select link from ff_designer_links where ref=order_line_items.sku and ref != '' limit 1) as link1,
            (select link from ff_designer_links where ref=order_line_items.name and ref != '' limit 1) as link2,
            (select webViewLink from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 6) and mimeType = 'application/vnd.google-apps.folder' limit 1) as link3,
            (select webViewLink from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 5) and mimeType = 'application/vnd.google-apps.folder' limit 1) as link4,
            (select webViewLink from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 4) and mimeType = 'application/vnd.google-apps.folder' limit 1) as link5,
            (select webViewLink from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 3) and mimeType = 'application/vnd.google-apps.folder' limit 1) as link6,
            (select webViewLink from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 2) and mimeType = 'application/vnd.google-apps.folder' limit 1) as link7,

            (select ref from ff_designer_links where ref=order_line_items.sku and ref != '' limit 1) as note1,
            (select ref from ff_designer_links where ref=order_line_items.name and ref != '' limit 1) as note2,
            (select name from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 6) and mimeType = 'application/vnd.google-apps.folder' limit 1) as note3,
            (select name from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 5) and mimeType = 'application/vnd.google-apps.folder' limit 1) as note4,
            (select name from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 4) and mimeType = 'application/vnd.google-apps.folder' limit 1) as note5,
            (select name from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 3) and mimeType = 'application/vnd.google-apps.folder' limit 1) as note6,
            (select name from google_drive_files where name=SUBSTRING_INDEX(order_line_items.sku, '-', 2) and mimeType = 'application/vnd.google-apps.folder' limit 1) as note7

        "))
            ->leftJoin('order_line_items','orders.shopify_id', '=', 'order_line_items.order_id')
            ->leftJoin('products','order_line_items.product_id', '=', 'products.shopify_id')
            ->where('product_id', '>', '0')
            ->where(DB::raw("CONVERT_TZ(orders.shopify_created_at,'UTC','Asia/Ho_Chi_Minh')"), '>=', "$fromDate")
            ->where(DB::raw("CONVERT_TZ(orders.shopify_created_at,'UTC','Asia/Ho_Chi_Minh')"), '<', "$toDate")
            ->orderBy('orders.shopify_created_at', 'DESC')
            ->paginate($displayItemQty)->withQueryString();

        return $orders;
    }
}
