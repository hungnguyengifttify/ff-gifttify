<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAdsCreatives extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'account_id', 'actor_id', 'body', 'call_to_action_type', 'effective_object_story_id',
        'image_crops', 'image_hash', 'image_url', 'name', 'object_story_spec', 'object_type',
        'status', 'thumbnail_url', 'title', 'url_tags'
    ];
}
