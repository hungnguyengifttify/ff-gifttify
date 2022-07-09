<?php

namespace App\Services;

class Spreadsheet
{
    public static function exportFromArray ($data, $fileName) {
        try {
            $spreadSheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadSheet->getActiveSheet()->fromArray($data);

            if ( str_contains($fileName, '.csv') ) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheet);
                header("Content-Type: text/csv");
                header("Content-Disposition: attachment; filename=$fileName");
                header("Cache-Control: max-age=0");
            } else {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadSheet);
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment;filename=$fileName");
                header("Cache-Control: max-age=0");
            }

            ob_end_clean();
            $writer->save('php://output');
            return;
        } catch (Exception $e) {
            echo $e->getMessage();
            return;
        }
    }
}
