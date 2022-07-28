<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\GoogleDrive;
use App\Models\GoogleDriveFiles;
use Illuminate\Support\Facades\DB;

class TrashedGoogleDriveFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googledrive:trashed_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trashed Google Drive files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());

        DB::update("
            update google_drive_files g1
            inner join
            (
                select name, parentId, mimeType, count(*) as total, max(createdTime) as maxCreatedTime from google_drive_files
                WHERE trashed is null or trashed = 0
                group by name, parentId, mimeType HAVING count(*)>1
            ) g2 on g1.name = g2.name and g1.parentId = g2.parentId and g1.mimeType = g2.mimeType
            set g1.trashed = -1;
        ");

        $gDrive = new GoogleDrive();
        $files = GoogleDriveFiles::select("id", "name", "webViewLink")
            ->where('trashed', -1)
            ->get();
        foreach ($files as $f) {
            try {
                $checkedFile = $gDrive->service->files->get($f->id, array("fields" => "id, name, trashed"));
                GoogleDriveFiles::where('id', $f->id)->update(['trashed' => $checkedFile->trashed ? 1 : 0]);
            } catch (\Exception $e) {
                GoogleDriveFiles::where('id', $f->id)->update(['trashed' => 0]);
            }
        }

        $this->info("Cron Job End at ". now());
        $this->info('Success!');
    }
}
