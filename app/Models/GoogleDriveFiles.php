<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoogleDriveFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'createdTime', 'fullFileExtension', 'mimeType', 'modifiedTime', 'name', 'parents', 'parentId', 'permissionIds', 'shared', 'size', 'spaces', 'thumbnailLink', 'viewersCanCopyContent', 'webContentLink', 'webViewLink', 'writersCanShare', 'owners', 'trashed', 'trashedTime'
    ];

    public static function get_children_from_folder_id($folderId, &$result, $level)
    {
        $level++;
        if ($level > 5) {
            return $result;
        }
        if ($level == 1) {
            $dataRoot = DB::selectOne("
                select id, name, mimeType, webContentLink
                from google_drive_files
                where id = :id
                ;", ['id' => $folderId]
            );
            $result['folders'][$dataRoot->name]['id'] = $dataRoot->id;
            $result['folders'][$dataRoot->name]['name'] = $dataRoot->name;
            self::get_children_from_folder_id($dataRoot->id, $result['folders'][$dataRoot->name], $level);
        } else {
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

    public static function flat_image_links_from_folder_id_by_mysql_query ($folderId) {
        $deepLevel = 8;
        $data = DB::select("
            WITH RECURSIVE google_drive_files_flat
            AS (
                  SELECT gdf.id, gdf.`name` , gdf.webContentLink, gdf.mimeType, gdf.parentId, 1 `level`
                    , @rownum := @rownum + 1 as stt, CONVERT('1', CHAR(100)) as stt_order
                  from google_drive_files gdf
                  cross join (select @rownum := 0) r
                  where gdf.id = :folderId

                  UNION ALL

                  SELECT gdf.id, gdf.`name` , gdf.webContentLink, gdf.mimeType, gdf.parentId, `level`+1
                    , @rownum := @rownum + 1 as stt
                    , (case gdf.mimeType when 'application/vnd.google-apps.folder' then CONCAT( CONVERT(gdff.stt_order, CHAR) , LEFT(gdf.`name`,3) ) else CONCAT( CONVERT(gdff.stt_order, CHAR) , LEFT(gdf.`name`,6) ) end )  as stt_order
                  FROM google_drive_files gdf
                  INNER JOIN google_drive_files_flat gdff ON gdff.id = gdf.parentId
                  WHERE `level` < $deepLevel
                  and gdff.mimeType = 'application/vnd.google-apps.folder'
                  and gdf.mimeType != 'image/x-photoshop'
                )
            select * FROM
            (
                SELECT `level`, `name`, id, '' as link, 'folder' as type, stt_order, stt, parentId
                FROM google_drive_files_flat
                where mimeType = 'application/vnd.google-apps.folder'

                UNION ALL

                SELECT `level`, `name`, id, REPLACE(webContentLink, '&export=download', '') as link, 'image' as type, stt_order, stt, parentId
                FROM google_drive_files_flat
                where mimeType != 'application/vnd.google-apps.folder' and LEFT(UPPER(name), 2) = 'MK'
            ) a
            order by stt_order asc, `name` asc;
            ",
            ['folderId' => $folderId]
        );
        return collect($data)->map(function($x){ return (array) $x; })->toArray();
    }

}
