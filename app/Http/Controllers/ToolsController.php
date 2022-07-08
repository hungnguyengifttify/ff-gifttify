<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GoogleDriveFiles;

class ToolsController extends Controller {

    public function get_image_links(Request $request)
    {
        $link = $request->input('link') ?? '';
        $uriArr = parse_url($link);
        $path = $uriArr['path'] ? explode('/', $uriArr['path']) : '';

        $id = '';
        if ($path) {
            $id = isset($path[count($path)-1]) ? $path[count($path)-1] : '';
        }

        $result = array();
        if ($id) {
            $result = GoogleDriveFiles::get_all_images_from_folder_id($id);
            dump($result);
        }
        return view('tools.image_links', compact('link', 'result') );
    }
}
