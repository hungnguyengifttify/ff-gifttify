<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dashboard;
use App\Services\GoogleAnalytics;
use App\Models\GaCampaignReports;
use Illuminate\Support\Facades\Redis;


class DashboardTestController extends Controller {
    public function index (Request $request) {
        $redis = Redis::connection('owllify.com');
        //dump($redis->ping());
        //dump($redis->set('test', 'abc'));
        dump($redis->hgetall('json'));
        dd('Test');
    }
}
