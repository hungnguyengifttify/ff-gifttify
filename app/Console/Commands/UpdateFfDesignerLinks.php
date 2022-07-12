<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\FfDesignerLinks;

class UpdateFfDesignerLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ff_designer_links:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Designer Links';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());

        $this->updateGtfsDesignSheet();
        $this->updateGtfsDesignFf2Sheet();
        $this->updateGtfsFf141Ff2Sheet();

        $this->info("Cron Job End at ". now());
    }

    protected function updateGtfsDesignSheet() {
        $spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQe6y0QIqiX4lXs-rg3_Hg__Bbv8b8O02ssvNs9HsPNkaZWtEl8oRCIl2DdMtau5RFCDInB8uFnF_sd/pub?gid=0&single=true&output=csv";
        if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

        $i = 0;
        if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
            while (($v = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $i++;
                if ($i <= 1) continue;

                $request_date = '1900-01-01';
                if (trim($v[1]) != '') {
                    $request_date = explode('/', $v[1]);
                    if (count($request_date) == 3) {
                        if ($request_date[1] <= 12) {
                            $request_date = Carbon::createFromFormat('d/m/y', $v[1], 'UTC');
                        } else {
                            $request_date = Carbon::createFromFormat('m/d/y', $v[1], 'UTC');
                        }
                    } elseif (count($request_date) == 2) {
                        if ($request_date[1] <= 12) {
                            $request_date = Carbon::createFromFormat('d/m/y', $v[1].'/21', 'UTC');
                        } else {
                            $request_date = Carbon::createFromFormat('m/d/y', $v[1].'/21', 'UTC');
                        }
                    }
                    $request_date = $request_date->format('Y-m-d 0:0:0');
                }

                FfDesignerLinks::updateOrCreate([
                    'ref' => $v[2] ?? '',
                ], [
                    'request_date' => $request_date,
                    'image_link' => '',
                    'product_type' => $v[3] ?? '',
                    'store' => $v[4] ?? '',
                    'product_note' => '',
                    'link' => $v[5] ?? '',
                    'designer' => $v[6] ?? '',
                    'status' => $v[7] ?? '',
                    'staff_note' => substr($v[9], 0, 500) ?? '',
                    'reason_note' => substr($v[10], 0, 500) ?? '',
                    'sheet' => 'GtfsDesign',
                ]);
            }
            fclose($handle);
        } else {
            dd("Problem reading csv");
        }

    }

    protected function updateGtfsDesignFf2Sheet() {
        $spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQe6y0QIqiX4lXs-rg3_Hg__Bbv8b8O02ssvNs9HsPNkaZWtEl8oRCIl2DdMtau5RFCDInB8uFnF_sd/pub?gid=89695582&single=true&output=csv";
        if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

        $i = 0;
        if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
            while (($v = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $i++;
                if ($i <= 1) continue;
                if ($v[3] == '') continue;

                $request_date = '1900-01-01';
                if (trim($v[1]) != '' && strpos($v[1], '/') !== false) {
                    $request_date = Carbon::createFromFormat('d/m/Y', $v[1], 'UTC');
                    $request_date = $request_date->format('Y-m-d 0:0:0');
                }

                FfDesignerLinks::updateOrCreate([
                    'ref' => $v[3] ?? '',
                ], [
                    'request_date' => $request_date,
                    'image_link' => $v[2] ?? '',
                    'product_type' => $v[4] ?? '',
                    'store' => $v[5] ?? '',
                    'product_note' => substr($v[6], 0, 500) ?? '',
                    'link' => $v[7] ?? '',
                    'designer' => $v[8] ?? '',
                    'status' => $v[9] ?? '',
                    'staff_note' => substr($v[11], 0, 500) ?? '',
                    'reason_note' => substr($v[12], 0, 500) ?? '',
                    'sheet' => 'GtfsDesignFf2',
                ]);
            }
            fclose($handle);
        } else {
            dd("Problem reading csv");
        }
    }

    protected function updateGtfsFf141Ff2Sheet() {
        $spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQe6y0QIqiX4lXs-rg3_Hg__Bbv8b8O02ssvNs9HsPNkaZWtEl8oRCIl2DdMtau5RFCDInB8uFnF_sd/pub?gid=2094546893&single=true&output=csv";
        if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

        $i = 0;
        if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
            while (($v = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $i++;
                if ($i <= 1) continue;
                if ($v[3] == '') continue;

                $request_date = '1900-01-01';
                if (trim($v[1]) != '' && strpos($v[1], '/') !== false) {
                    $request_date = Carbon::createFromFormat('d/m/Y', $v[1], 'UTC');
                    $request_date = $request_date->format('Y-m-d 0:0:0');
                }

                FfDesignerLinks::updateOrCreate([
                    'ref' => $v[3] ?? '',
                ], [
                    'request_date' => $request_date,
                    'image_link' => $v[2] ?? '',
                    'product_type' => $v[4] ?? '',
                    'store' => $v[5] ?? '',
                    'product_note' => substr($v[6], 0, 500) ?? '',
                    'link' => $v[7] ?? '',
                    'designer' => $v[8] ?? '',
                    'status' => $v[9] ?? '',
                    'staff_note' => substr($v[11], 0, 500) ?? '',
                    'reason_note' => substr($v[12], 0, 500) ?? '',
                    'sheet' => 'GtfsFf141Ff2',
                ]);
            }
            fclose($handle);
        } else {
            dd("Problem reading csv");
        }
    }
}
