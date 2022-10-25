<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use FKRediSearch\Query\Query;
use FKRediSearch\Query\QueryBuilder;
use FKRediSearch\Setup;
use Illuminate\Support\Facades\Config;

class RedisGtf extends Model
{
    public static function getTotalOrderByDate($store = 'thecreattify', $fromDateTs = '', $toDateTs = '') {
        $redisConfig = Config::get("database.redis.$store");
        $client = Setup::connect( $redisConfig['host'], $redisConfig['port'], $redisConfig['password'], 0 );
        $search = new Query( $client, 'idx:order' );

        $query = new QueryBuilder();
        $query->setTokenize()
            ->setFuzzyMatching()
            ->addCondition('status', ['completed'], 'AND', TRUE);

        $fromDate = $fromDateTs * 1000;
        $toDate = $toDateTs * 1000;

        $results = $search
            ->sortBy( 'paidAt', $order = 'DESC' )
            ->numericFilter( 'paidAt', $fromDate, $toDate )
            ->limit( 0, $pageSize = 100000 ) // If set, we limit the results to the offset and number of results given. The default is 0 10
            ->search( $query, $documentsAsArray = true );

        $total = $results->getCount();
        if ($results->getCount() == 0) {
            return array('total' => 0, 'totalAmount' => 0);
        }

        $totalAmount = 0;
        foreach ($results->getDocuments() as $k => $v) {
            $order = json_decode($v['$']);
            $totalAmount += $order->total;
        }
        return array('total' => $total, 'totalAmount' => $totalAmount);
    }
}
