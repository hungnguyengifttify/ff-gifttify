<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use FKRediSearch\Query\Query;
use FKRediSearch\Query\QueryBuilder;
use FKRediSearch\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Dashboard;
use App\Services\GoogleAnalytics;
use App\Models\GaCampaignReports;
use Illuminate\Support\Facades\Redis;


class DashboardTestController extends Controller {
    public function index (Request $request) {
        $redisConfig = Config::get('database.redis.thecreattify');
        $client = Setup::connect( $redisConfig['host'], $redisConfig['port'], $redisConfig['password'], 0 );
        $search = new Query( $client, 'idx:order' );

        $query = new QueryBuilder();
        $query->setTokenize()
            ->setFuzzyMatching()
            ->addCondition('status', ['completed'], 'AND', TRUE);
        $condition = $query->buildRedisearchQuery();
        dump($condition);

        $results = $search
            ->sortBy( 'paidAt', $order = 'DESC' )
            ->numericFilter( 'paidAt', 1666569600000, 1666656000000 )
            ->limit( 0, $pageSize = 100000 ) // If set, we limit the results to the offset and number of results given. The default is 0 10
            ->search( $query, $documentsAsArray = true );

        if ($results->getCount() == 0) {
            dd($results);
        }

        $totalAmount = 0;
        foreach ($results->getDocuments() as $k => $v) {
            $order = json_decode($v['$']);
            dump($order);
            $totalAmount += $order->total;
        }

        dump($results->getCount(), $totalAmount);
        //dump($redis->set('test', 'abc'));
        //dump($redis->hgetall('json'));
        dd('Test');
    }
}
