<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoogleDriveFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'createdTime', 'fullFileExtension', 'mimeType', 'modifiedTime', 'name', 'parents', 'parentId', 'permissionIds', 'shared', 'size', 'spaces', 'thumbnailLink', 'viewersCanCopyContent', 'webContentLink', 'webViewLink', 'writersCanShare', 'owners'
    ];

    public static function get_children_from_folder_id($folderId, &$result, &$level)
    {
        $level++;
        if ($level > 4) {
            return $result;
        }
        $data = DB::select("
            select id, name, mimeType, webContentLink
            from google_drive_files
            where parentId = :parentId
            ;", ['parentId' => $folderId]
        );
        foreach ($data as $k => $v) {
            if ($v->mimeType == 'application/vnd.google-apps.folder') {
                $result['folders'][$v->id] = (array)$v;
                self::get_children_from_folder_id($v->id, $result['folders'][$v->id], $level);
            } elseif ($v->mimeType !== 'image/x-photoshop' && strpos(strtoupper($v->name), 'MK') === 0 ) {
                $result['images'][$v->id]['id'] = $v->id;
                $result['images'][$v->id]['name'] = $v->name;
                $result['images'][$v->id]['link'] = str_replace('&export=download', '', $v->webContentLink);
            }
        }
        return $result;
    }

    public static function get_all_images_from_folder_id ($folderId) {
        $result = array();
        $level = 0;
        self::get_children_from_folder_id($folderId, $result, $level);
        dd($result);
    }
}
