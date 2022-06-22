<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'shopify_id', 'title', 'body_html', 'vendor', 'product_type', 'shopify_created_at',
        'handle', 'shopify_updated_at', 'published_at', 'template_suffix', 'status', 'published_scope',
        'tags', 'admin_graphql_api_id', 'variants', 'options', 'images', 'image'
    ];
}
