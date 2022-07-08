<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleDriveFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'createdTime', 'description', 'fullFileExtension', 'mimeType', 'modifiedTime', 'name', 'parents', 'parentId', 'permissionIds', 'shared', 'size', 'spaces', 'thumbnailLink', 'viewersCanCopyContent', 'webContentLink', 'webViewLink', 'writersCanShare', 'owners'
    ];
}
