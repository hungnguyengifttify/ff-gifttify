<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemixProductType extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'product_type', 'title', 'description', 'base_price', 'size_chart',
        'category', 'gender', 'status', 'images', 'options', 'variants'
    ];
}
