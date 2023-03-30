<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportProductsCsv extends Model
{
    use HasFactory;

    public static $storeDb = array(
        'thecreattify.co' => 1,
        'store.gifttify.com' => 2,
        '66circle.com' => 3,
        'owllify.com' => 4,
        'vanoba.com' => 5,
        'whelands.com' => 6,
        'gifttifyagency.com' => 1,
    );

    protected $table = 'import_products_csv';

    protected $fillable = [
        'shopifyId', 'slug', 'title', 'productType', 'status', 'tags', 'tagsArr', 'images',
        'options', 'variants', 'seo', 'syncedStatus', 'syncedStatusTime', 'syncedImage', 'syncedImageTime',
        'linkMockupTemplate'
    ];
}
