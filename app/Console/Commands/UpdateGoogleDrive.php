<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\GoogleDrive;
use App\Models\GoogleDriveFiles;

class UpdateGoogleDrive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googledrive:get_files {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Google Drive files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());

        $orderByArr = array(
            'default', 'modifiedTime desc', 'createdTime desc', 'recency desc'
        );
        foreach ($orderByArr as $orderBy) {
            $gDrive = new GoogleDrive();
            $pageToken = NULL;

            do {
                try {
                    $parameters = array(
                        'pageSize' => 300,
                        'fields' => 'nextPageToken, files(id, createdTime, fileExtension, fullFileExtension, mimeType, modifiedTime, name, originalFilename, parents, permissionIds, shared, size, spaces, thumbnailLink, viewersCanCopyContent, webContentLink, webViewLink, writersCanShare, owners, trashed, trashedTime)',
                        'q' => "mimeType = 'application/vnd.google-apps.folder' or mimeType contains 'image/'"
                    );
                    if ($orderBy != 'default') {
                        $parameters['orderBy'] = $orderBy;
                    }

                    if ($pageToken) {
                        $parameters['pageToken'] = $pageToken;
                    }
                    $files = $gDrive->service->files->listFiles($parameters);

                    $results = $files->getFiles();
                    $pageToken = $files->getNextPageToken();

                    foreach ($results as $v) {
                        GoogleDriveFiles::updateOrCreate([
                            'id' => $v['id'] ?? 0,
                        ], [
                            'createdTime' => isset($v['createdTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $v['createdTime'], 'UTC') : '1900-01-01',
                            'modifiedTime' => isset($v['modifiedTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $v['modifiedTime'], 'UTC') : '1900-01-01',
                            'fullFileExtension' => $v['fullFileExtension'] ?? '',
                            'mimeType' => $v['mimeType'] ?? '',
                            'name' => $v['name'] ?? '',
                            'thumbnailLink' => $v['thumbnailLink'] ?? '',
                            'webContentLink' => $v['webContentLink'] ?? '',
                            'webViewLink' => $v['webViewLink'] ?? '',
                            'viewersCanCopyContent' => $v['viewersCanCopyContent'] ?? 0,
                            'writersCanShare' => $v['writersCanShare'] ?? 0,
                            'size' => $v['size'] ?? 0,
                            //'trashed' => $v['trashed'] ?? 0,
                            //'trashedTime' => isset($v['trashedTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $v['trashedTime'], 'UTC') : '1900-01-01',
                            'parentId' => isset($v['parents'][0]) ? $v['parents'][0] : '',
                            'parents' => isset($v['parents']) ? json_encode($v['parents']) : json_encode(''),
                            'owners' => isset($v['owners']) ? json_encode($v['owners']) : json_encode(''),
                            'spaces' => isset($v['spaces']) ? json_encode($v['spaces']) : json_encode(''),
                            'permissionIds' => isset($v['permissionIds']) ? json_encode($v['permissionIds']) : json_encode(''),
                        ]);
                    }

                    $timeReport = $this->argument('time_report');
                    if ($timeReport != 'all') {
                        break;
                    }
                } catch (Exception $e) {
                    print "An error occurred: " . $e->getMessage();
                    $pageToken = NULL;
                }
            } while ($pageToken);

        }

        $this->info("Cron Job End at ". now());
        $this->info('Success!');
    }
}
