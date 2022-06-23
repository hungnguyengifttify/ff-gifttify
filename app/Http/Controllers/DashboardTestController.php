<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FbAds;
use App\Models\Dashboard;

class DashboardTestController extends Controller {

    public function index(Request $request)
    {
        $result = Dashboard::getReportByDate('us', 'today');
        $result2 = Dashboard::getReportByDate('us', 'yesterday');
        $result3 = Dashboard::getReportByDate('us', 'this_week');
        $result4 = Dashboard::getReportByDate('us', 'last_week');
        $result5 = Dashboard::getReportByDate('us', 'this_month');

        $result6 = Dashboard::getReportByDate('au', 'today');
        $result7 = Dashboard::getReportByDate('au', 'yesterday');
        $result8 = Dashboard::getReportByDate('au', 'this_week');
        $result9 = Dashboard::getReportByDate('au', 'last_week');
        $result10 = Dashboard::getReportByDate('au', 'this_month');

        dd($result, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10);

    }
}
