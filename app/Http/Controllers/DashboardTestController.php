<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FbAds;
use App\Models\Dashboard;

use App\Services\GoogleDrive;

class DashboardTestController extends Controller {

    public function index(Request $request)
    {
        $gDrive = new GoogleDrive();

        $optParams = array(
            //'corpora' => 'user',
            //'driveId' => '1gsXWxbdw18LNz8G1_SrwxoKIpGpspCKc',
            //'includeItemsFromAllDrives' => true,
            //'supportsAllDrives' => true,
            'pageSize' => 5,
            'fields' => 'nextPageToken, files(id, createdTime, description, fileExtension, fullFileExtension, mimeType, modifiedTime, name, originalFilename, parents, permissionIds, quotaBytesUsed, shared, size, spaces, thumbnailLink, viewersCanCopyContent, webContentLink, webViewLink, writersCanShare, owners, permissions)',
            'q' => "mimeType = 'application/vnd.google-apps.folder' or mimeType contains 'image/'"
        );
        $results = $gDrive->service->files->listFiles($optParams);
        //$results = $service->drives->listDrives();
        //dd($results);

        if (count($results->getFiles()) == 0) {
            print "No files found.\n";
        } else {
            print "Files:\n";
            dd($results->getFiles());

            foreach ($results->getFiles() as $file) {
                dd($file);
                echo "<br/>";
            }
        }

    }
}
