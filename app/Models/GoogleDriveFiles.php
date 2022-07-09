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

    public static function get_children_from_folder_id($folderId, &$result, $level)
    {
        $level++;
        if ($level > 5) {
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
                $result['folders'][$v->name]['id'] = $v->id;
                $result['folders'][$v->name]['name'] = $v->name;
                self::get_children_from_folder_id($v->id, $result['folders'][$v->name], $level);
            } elseif ($v->mimeType !== 'image/x-photoshop' && strpos(strtoupper($v->name), 'MK') === 0 ) {
                $result['images'][$v->name]['id'] = $v->id;
                $result['images'][$v->name]['name'] = $v->name;
                $result['images'][$v->name]['link'] = str_replace('&export=download', '', $v->webContentLink);
            }
        }
        return $result;
    }

    public static function get_all_images_from_folder_id ($folderId) {
        $result = array();
        $level = 0;
        self::get_children_from_folder_id($folderId, $result, $level);
        return $result;
    }

    public static function get_images_each_level ($data, &$result, $level) {
        $level++;
        $images = $data['images'] ?? array();
        if ($images) {
            foreach ($images as $img) {
                $result[] = array (
                    'level' => $level,
                    'type' => 'image',
                    'name' => $img['name'],
                    'link' => $img['link'],
                );
            }
        }

        $folders = $data['folders'] ?? array();
        if ($folders) {
            foreach ($folders as $folder) {
                $result[] = array (
                    'level' => $level,
                    'type' => 'folder',
                    'name' => $folder['name'],
                );

                self::get_images_each_level($folder, $result, $level);
            }
        }

        return $result;
    }

    public static function flat_image_links_from_folder_id ($folderId) {
        $data = self::get_all_images_from_folder_id ($folderId);
        $result = array();
        $level = 0;
        self::get_images_each_level ($data, $result, $level);
        return $result;
    }

}
