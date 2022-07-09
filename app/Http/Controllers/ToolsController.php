<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GoogleDriveFiles;

use App\Services\Spreadsheet;

class ToolsController extends Controller {

    public function get_image_links(Request $request)
    {
        $link = $request->input('link') ?? '';
        $action = $request->input('action') ?? '';
        $uriArr = parse_url($link);
        $path = $uriArr['path'] ? explode('/', $uriArr['path']) : '';

        $id = '';
        if ($path) {
            $id = isset($path[count($path)-1]) ? $path[count($path)-1] : '';
        }

        $result = array();
        if ($id) {
            $result = GoogleDriveFiles::flat_image_links_from_folder_id($id);
        }
        if ($action == 'download_csv') {
            $this->export_image_links($result);
            return true;
        }
        return view('tools.image_links', compact('link', 'result') );
    }

    protected function export_image_links ($data) {
        $excelData = array();
        foreach ($data as $v) {
            $line = array();
            for ($i = 0; $i < $v['level'] - 1; $i++) {
                $line[] = '';
            }
            $line[] = $v['name'];
            if ($v['type'] == 'image') {
                $line[] = $v['link'];
            }

            $excelData[] = $line;
        }
        $fileName = "image_links_" . time() . '.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }
}
