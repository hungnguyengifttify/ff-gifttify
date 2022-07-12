<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FfDesignerLinks extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_date', 'image_link', 'ref', 'product_type', 'store', 'product_note',
        'link', 'designer', 'status', 'staff_note', 'reason_note', 'sheet'
    ];
}
