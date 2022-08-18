<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Config;
use Google\Service\AnalyticsReporting;

class GoogleAnalytics
{

    protected $analytics;
    public $service;

    public $dateRange;
    public $metrics = [];


    public $viewID = '230760666';

    function __construct()
    {
        $client = new Client();
        $client->setApplicationName('Google Analytic API');
        $config = json_decode(Config::get('google.analytic_report_api.json_config'), true);
        $client->setAuthConfig($config);

        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $this->analytics = new AnalyticsReporting($client);
        $this->setMetrics();
    }

    public function setMetrics()
    {
        // Create the Metrics object. max 10 metric in one Api
        $listMetric = [
            "ga:users" => "users",
            "ga:newUsers" => "new_users",
            "ga:sessions" => "session",
            "ga:bounceRate" => "bounce_rate",
            "ga:pageviewsPerSession" => "pageviews_per_session",
            "ga:avgSessionDuration" => "avg_session_duration",
            "ga:transactions" => "transactions",
            "ga:transactionsPerSession" => "transactions_per_session",
            "ga:transactionRevenue" => "transaction_revenue",
            "ga:adCost" => "ad_cost",
        ];

        foreach ($listMetric as $gaKey => $gaAlias) {
            $sessions = new \Google\Service\AnalyticsReporting\Metric();
            $sessions->setExpression($gaKey);
            $sessions->setAlias($gaAlias);
            $this->metrics[] = $sessions;
        }
    }

    public function crawlCampaigns($viewId, $fromTime, $toTime)
    {
        // Create the DateRange object.
        $dateRange = new \Google\Service\AnalyticsReporting\DateRange();
        $dateRange->setStartDate($fromTime);
        $dateRange->setEndDate($toTime);

        // Create the Dimension object.
        $dimension = new \Google\Service\AnalyticsReporting\Dimension();
        $dimension->setName("ga:campaign");

        // Create the ReportRequest object.
        $request = new \Google\Service\AnalyticsReporting\ReportRequest();
        $request->setViewId($viewId);  // View ID
        $request->setDateRanges($dateRange);  // Set Date
        $request->setDimensions([$dimension]); // Set Dimension
        $request->setMetrics($this->metrics);  // set Metrics

        $body = new \Google\Service\AnalyticsReporting\GetReportsRequest();
        $body->setReportRequests(array($request));
        $reports = $this->analytics->reports->batchGet($body);

        return $this->handleData($reports);
    }

    public function handleData($reports)
    {
        $returnValue = [];
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

                $campainName = $dimensions[0];
                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        $result[$entry->getName()] = $values[$k];
                    }
                }
                $returnValue[$campainName] = $result;
            }
        }
        return $returnValue;
    }

    public function crawlCampaignsTest($viewId, $fromTime, $toTime)
    {
        // Create the DateRange object.
        $dateRange = new \Google\Service\AnalyticsReporting\DateRange();
        $dateRange->setStartDate($fromTime);
        $dateRange->setEndDate($toTime);

        // Create the Dimension object.
        $dimension = new \Google\Service\AnalyticsReporting\Dimension();
        $dimension->setName("ga:campaign");

        // Create the ReportRequest object.
        $request = new \Google\Service\AnalyticsReporting\ReportRequest();
        $request->setViewId($viewId);  // View ID
        $request->setDateRanges($dateRange);  // Set Date
        $request->setDimensions([$dimension]); // Set Dimension
        $request->setMetrics($this->metrics);  // set Metrics

        $body = new \Google\Service\AnalyticsReporting\GetReportsRequest();
        $body->setReportRequests(array($request));
        $reports = $this->analytics->reports->batchGet($body);

        return $this->printResults($reports);
    }

    public function printResults($reports)
    {
        $returnValue = [];
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

                $campainName = $dimensions[0];
                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        $result[$entry->getName()] = $values[$k];
                    }
                }
                $returnValue[$campainName] = $result;
            }
        }
        return $returnValue;
    }
}
