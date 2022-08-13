<?php
namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Config;
use Google\Service\AnalyticsReporting;

class GoogleAnalytics {

    protected $client;
    public $service;

    public $viewID = '230760666';

    function __construct() {
        $client = new Client();
        $client->setApplicationName('Google Analytic API');

//        $KEY_FILE_LOCATION = json_decode(Config::get('google.drive_api.json_config'), true);
        $KEY_FILE_LOCATION = __DIR__ . '/../../service-account-credentials.json';
        // Create and configure a new client object.
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new AnalyticsReporting($client);

        return $analytics;
    }

    public function crawlCampaigns($fromTime, $toTime){
        $analytics = $this->__construct();

        // Create the DateRange object.
        $dateRange = new \Google\Service\AnalyticsReporting\DateRange();
        $dateRange->setStartDate("7daysAgo");
        $dateRange->setEndDate("today");

        // Create the Metrics object.
        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:users");
    $sessions->setAlias("Users");
        $metrics[] = $sessions;

        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:newUsers");
        $sessions->setAlias("NewUsers");
        $metrics[] = $sessions;

        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:sessions");
        $sessions->setAlias("Session");
        $metrics[] = $sessions;

        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:avgSessionDuration");
        $sessions->setAlias("AvgSessionDuration");
        $metrics[] = $sessions;

        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:bounceRate");
        $sessions->setAlias("BounceRate");
        $metrics[] = $sessions;

        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:goalCompletionsAll");
        $sessions->setAlias("GoalCompletionsAll");
        $metrics[] = $sessions;

        $sessions = new \Google\Service\AnalyticsReporting\Metric();
        $sessions->setExpression("ga:goalValueAll");
        $sessions->setAlias("GoalValueAll");
        $metrics[] = $sessions;

        // Create the ReportRequest object.
        $request = new \Google\Service\AnalyticsReporting\ReportRequest();
        $request->setViewId('230760666');
        $request->setDateRanges($dateRange);
        $request->setMetrics($metrics);

        $body = new \Google\Service\AnalyticsReporting\GetReportsRequest();
        $body->setReportRequests( array( $request) );
        $reports = $analytics->reports->batchGet($body);

        for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
            $report = $reports[$reportIndex];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[$rowIndex];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();
//                for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
//                    $result1[] = $dimensionHeaders[$i] . ": " . $dimensions[$i];
//                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        $result2[$entry->getName()] = $entry->getName() . ": " . $values[$k];
                    }
                }
            }
        }
        //fix
        dd([$result2]);
    }
}
