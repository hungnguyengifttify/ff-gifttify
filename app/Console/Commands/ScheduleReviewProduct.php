<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RemixApi;


class ScheduleReviewProduct extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'remix:reviewproduct {--page=1} {--limit=100} {--isDev=0} {--redisDB=} {--scanRangeDay=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remix Review Product';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Remix Schedule Product running at ". now());

        $page = (int)$this->option('page');
        $limit = (int)$this->option('limit');
        $isDev = (int)$this->option('isDev');
        $redisDB = (int)$this->option('redisDB');
        $scanRangeDay = (int)$this->option('scanRangeDay');

        if ($redisDB > 0) {
            $arrDb = array($redisDB);
        } else {
            $arrDb = array(1,2,4,6);
        }

        foreach ($arrDb as $db) {
            $pageActive = $page;
            do {
                $remixApi = new RemixApi();
                $response = $remixApi->request('PUT', "reviews/scheduleEmail?db=$db&page=$pageActive&limit=$limit&isDev=$isDev&scanRangeDay=$scanRangeDay", null, null);
                if ($response && $response->getStatusCode() == '200') {
                    $res = $response->getBody()->getContents();
                    $res = json_decode($res);
                    $this->info("Page: " . $pageActive . '/' . ceil($res->total / $limit));
                    $this->info($res->message ?? 'Done');
                } else {
                    dump('Err');
                    $this->error('Something wrong');
                }
                $pageActive++;
            } while (isset($res) && $pageActive <= ceil($res->total / $limit) );

        }

        $this->info("Cron Job Remix Schedule Product DONE at ". now());
    }
}
