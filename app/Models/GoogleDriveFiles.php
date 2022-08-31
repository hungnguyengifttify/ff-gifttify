<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoogleDriveFiles extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

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

    public static function flat_image_links_from_folder_id_by_mysql_query ($folderId, $type = 'flat') {
        $deepLevel = 8;
        $data = DB::select("
            WITH RECURSIVE google_drive_files_flat
            AS (
                  SELECT gdf.id, gdf.`name` , gdf.webContentLink, gdf.mimeType, gdf.parentId, 1 `level`
                    , @rownum := @rownum + 1 as stt, CONVERT('1', CHAR(300)) as stt_order
                  from google_drive_files gdf
                  cross join (select @rownum := 0) r
                  where gdf.id = :folderId

                  UNION ALL

                  SELECT gdf.id, gdf.`name` , gdf.webContentLink, gdf.mimeType, gdf.parentId, `level`+1
                    , @rownum := @rownum + 1 as stt
                    , (case gdf.mimeType when 'application/vnd.google-apps.folder' then CONCAT( CONVERT(gdff.stt_order, CHAR) , LEFT(gdf.`name`,10) ) else CONCAT( CONVERT(gdff.stt_order, CHAR) , LEFT(gdf.`name`,10) ) end )  as stt_order
                  FROM google_drive_files gdf
                  INNER JOIN google_drive_files_flat gdff ON gdff.id = gdf.parentId
                  WHERE `level` < $deepLevel
                  and gdff.mimeType = 'application/vnd.google-apps.folder'
                  and gdf.mimeType != 'image/x-photoshop'
                  and (gdf.trashed = 0 or gdf.trashed is null)
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

        if ($type == 'tree') {
            $c = collect($data)->keyBy('id');
            return $c->map(
                function ($item) use ($c) {
                    if (!($parent = $c->get($item->parentId))) {
                        return $item;
                    }
                    $parent->children[] = $item;
                }
            )->filter()->toArray();
        }

        return collect($data)->map(function($x){ return (array) $x; })->toArray();
    }

    public static function getFirstImageFromParentFolder($folderName, $folderLink) {
        if (!$folderName) return '';
        $link = GoogleDriveFiles::select("id")
            ->where("parentId", "=", DB::raw("(select id from google_drive_files p where p.name = '$folderName' and p.id = SUBSTRING_INDEX('$folderLink', '/', -1) order by p.createdTime desc limit 1)") )
            ->orderBy('createdTime', 'DESC')
            ->first();
        return isset($link->id) ? "https://drive.google.com/thumbnail?authuser=0&sz=w100&id=" . $link->id : '';
    }

    public static function getLinkByNewFormat ($sku) {
        $link = GoogleDriveFiles::select("webViewLink")
            ->where("parentId", "=", DB::raw("(
                select id from google_drive_files p2 where p2.name = 'File FF' and p2.parentId In (
                    select id from google_drive_files p1 where p1.name = SUBSTRING_INDEX('$sku', '-', 1)
                ) order by p2.createdTime desc limit 1
            )") )
            ->where("mimeType", "=", "application/vnd.google-apps.folder" )
            ->where("name", "=", DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX('$sku', '-', 2), '-', -1)") )
            ->orderBy('createdTime', 'DESC')
            ->first();
        return $link->webViewLink ?? '';
    }

    public static function getGoogleDriveCsvFile ($spreadsheet_url, $autoFillEmptyCell = true, $resultWithKey = true) {
        if (!$spreadsheet_url) return false;

        $result = array();
        $prev = array();
        $header = array();
        $i = 0;
        if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $i++;
                if ($i <= 1) {
                    $header = $row;
                    continue;
                }
                if ($row[0] != '') {
                    $prev = $row;
                }

                $rowResult = array();
                if ($autoFillEmptyCell) {
                    foreach ($row as $k => $v) {
                        if ($v == '') {
                            $row[$k] = $prev[$k] ?? '';
                        }
                        $rowResult[$header[$k]] = $row[$k];
                    }
                }

                if ($resultWithKey) {
                    $result[$row[0]][] = $rowResult;
                } else {
                    $result[] = $row;
                }

                $prev = $row;
            }
            fclose($handle);
        } else {
            dd("Problem reading csv");
        }
        return $result;
    }

}
