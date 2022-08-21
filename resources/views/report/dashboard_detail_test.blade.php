<style>
    .table {
        text-align: right;
    }
    table, th, td {
        border: 1px solid black;
        padding: 5px;
    }
    th {
        cursor: pointer;
        white-space: nowrap;
    }
    th svg {
        display: none;
    }
    th.sort_asc svg.svg_down {
        display: inline;
    }
    th.sort_desc svg.svg_up {
        display: inline;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Campaign Info - Store {{strtoupper($params['store'])}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="reportrangeform" method="get">
                <div class="bg-white overflow-hidden shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <a id="reportrange" class="btn btn-primary" href="#">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span>{!! $params['labelDate']; !!} </span> <i class="fa fa-caret-down"></i>
                        </a>
                        <input type="hidden" name="store" id="store" value="{{$store}}" />
                        <input type="hidden" name="fromDate" id="fromDate" />
                        <input type="hidden" name="toDate" id="toDate" />
                        <input type="hidden" name="labelDate" id="labelDate" />
                        <input type="hidden" name="debug" id="debug" value="{!! $_REQUEST['debug'] ?? '0';  !!} " />
                        <span id="reportrangetext">{!! $params['fromDate']->format('d-m-Y') . ' => ' . $params['toDate']->format('d-m-Y'); !!} </span>

                        <script type="text/javascript">
                            <?php if ($store == 'au-thecreattify'):;  ?>
                            moment.tz.setDefault("Australia/Sydney");
                            <?php else:  ?>
                            moment.tz.setDefault("America/Los_Angeles");
                            <?php endif;  ?>

                            moment().startOf('isoWeek');

                            $(function() {

                                var url_string = window.location.href;
                                var url = new URL(url_string);
                                var fromDate = url.searchParams.get("fromDate");
                                var toDate = url.searchParams.get("toDate");

                                if (fromDate) {
                                    var start = moment(fromDate);
                                    var end = moment(toDate);
                                    var label = url.searchParams.get("labelDate");
                                } else {
                                    var start = moment();
                                    var end = moment();
                                    var label = 'Today';
                                }

                                $('#reportrange').click(function () {
                                    return false;
                                });

                                $('#reportrange').daterangepicker({
                                    "autoApply": true,
                                    ranges: {
                                        'Today': [moment(), moment()],
                                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                        'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                                        'This Week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
                                        'Last Week': [moment().subtract(7, 'days').startOf('isoWeek'), moment().subtract(7, 'days').endOf('isoWeek')],
                                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                                    },
                                    "alwaysShowCalendars": true,
                                    startDate: start,
                                    endDate: end
                                }, displayDate);

                                function displayDate(start, end, label, isFirstTime) {
                                    $('#reportrange span').html(label);
                                    $('#reportrangetext').html(start.format('DD-MM-YYYY') + ' => ' + end.format('DD-MM-YYYY'));

                                    $('#fromDate').val(start.format('YYYY-MM-DD'));
                                    $('#toDate').val(end.format('YYYY-MM-DD'));
                                    $('#labelDate').val(label);

                                    console.log($('#fromDate').val());
                                    console.log($('#toDate').val());
                                    console.log(isFirstTime);

                                    if (!isFirstTime) {
                                        $('#reportrangeform').submit();
                                    }
                                };

                                displayDate(start, end, label, 1);

                            });
                        </script>
                    </div>
                </div>

            </form>

        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table id="campaign_info" class="table table-responsive table-bordered" style="width: auto">
                        <thead>
                        <tr>
                            <th>
                                CampaignName
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                AccountName
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                AdsCost
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                Adcost<br/>(Adwords)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                Rev
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                Rev<br/>(GA)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                CPC
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                CPM
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                MO
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                OrdersQty
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                OrdersQty<br/>(GA)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                Budget
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                            <th>
                                Status
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_down" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 svg_up" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </th>
                        </tr>
                        </thead>

                        <tbody>

                        @php
                            $sum_totalSpend = array_sum(array_column($campaigns,'totalSpend'));
                            $sum_ga_ad_cost = array_sum(array_column($campaigns,'ga_ad_cost'));
                            $sum_total_order_amount = array_sum(array_column($campaigns,'total_order_amount'));
                            $sum_ga_total_order_amount = array_sum(array_column($campaigns,'ga_total_order_amount'));

                            $sum_cpc = array_sum(array_column($campaigns,'totalUniqueClicks')) != 0 ? array_sum(array_column($campaigns,'totalSpend')) / array_sum(array_column($campaigns,'totalUniqueClicks')) : 0;
                            $sum_cpm = array_sum(array_column($campaigns,'impressions')) != 0 ? 1000 * array_sum(array_column($campaigns,'totalSpend')) / array_sum(array_column($campaigns,'impressions')) : 0;
                            $sum_mo = $sum_total_order_amount > 0 ? 100 * ($sum_totalSpend / $sum_total_order_amount) : 0;

                            $sum_total_order = array_sum(array_column($campaigns,'total_order'));
                            $sum_ga_total_order = array_sum(array_column($campaigns,'ga_total_order'));
                            $sum_budget = array_sum(array_column($campaigns,'budget'));
                        @endphp
                        <tr {!! display_row_bg_dashboard($sum_mo) !!} class="fw-bold" >
                            <td>
                                <select id="filter_campaign_name">
                                    <option value="ALL">-- All --</option>
                                    <option value="test">Test</option>
                                    <option value="maintain">Maintain</option>
                                    <option value="scale">Scale</option>
                                </select>
                            </td>
                            <td>
                                <input id="filter_account_name" class="form-control" />
                            </td>
                            <td {!! display_zero_cell_dashboard( $sum_totalSpend )!!} >{!! gifttify_price_format( $sum_totalSpend );  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_ga_ad_cost )!!} >{!! gifttify_price_format( $sum_ga_ad_cost, 0 ) !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_total_order_amount )!!} >{!! gifttify_price_format( $sum_total_order_amount );  !!} </td>
                            <td {!! display_ga_cell_dashboard( $sum_ga_total_order_amount, $sum_total_order_amount )!!} >{!! gifttify_price_format( $sum_ga_total_order_amount );  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_cpc )!!} >{!! number_format($sum_cpc, 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_cpm )!!} >{!! number_format($sum_cpm, 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_mo )!!} >{!! round( $sum_mo ) . '%';  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_total_order )!!} >{!! round( $sum_total_order ) !!} </td>
                            <td {!! display_ga_cell_dashboard( $sum_ga_total_order, $sum_total_order )!!} >{!! round( $sum_ga_total_order ) !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_budget )!!} >{!! gifttify_price_format( $sum_budget, 0 ) !!} </td>
                            <td>
                                <select id="filter_status">
                                    <option value="ALL">-- All --</option>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="PAUSED">PAUSED</option>
                                    <option value="DISABLED">DISABLED</option>
                                    <option value=""></option>
                                </select>
                            </td>
                        </tr>

                        <?php foreach ($campaigns as $v): ?>
                        <tr {!! display_row_bg_dashboard($v['mo']) !!} class="tr_sortable" >
                            @php
                                $account_status = \App\Models\FbAccount::$status[$v['account_status']] ?? '';
                                $creativeLink = route('ads_creative') . "?store=" . $store . "&" . app('request')->input('store') . "&fromDate=" . app('request')->input('fromDate') . "&toDate=" . app('request')->input('toDate') . "&labelDate=" . app('request')->input('labelDate') . "&code=" . $v['campaign_name'] . "&type=campaign_name";
                            @endphp
                            <td><a href="{{$creativeLink}}" target="_blank">{!! $v['campaign_name'] !!}</a> </td>
                            <td>{!! $v['account_name'] !!} </td>
                            <td {!! display_zero_cell_dashboard($v['totalSpend'])!!} >{!! gifttify_price_format($v['totalSpend']);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['ga_ad_cost'])!!} >{!! gifttify_price_format( $v['ga_ad_cost'], 0 ) !!} </td>
                            <td {!! display_zero_cell_dashboard($v['total_order_amount'])!!} >{!! gifttify_price_format($v['total_order_amount']);  !!} </td>
                            <td {!! display_ga_cell_dashboard($v['ga_total_order_amount'], $v['total_order_amount'])!!} >{!! gifttify_price_format($v['ga_total_order_amount']);  !!} </td>
                            <td {!! display_row_bg_campaign_cpc($v['cpc'])!!} >{!! number_format($v['cpc'], 2);  !!} </td>
                            <td {!! display_row_bg_campaign_cpm($v['cpm'])!!} >{!! number_format($v['cpm'], 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['mo'])!!} >{!! round($v['mo']) . '%';  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['total_order'])!!} >{!! round($v['total_order']) !!} </td>
                            <td {!! display_ga_cell_dashboard($v['ga_total_order'], $v['total_order'])!!} >{!! round($v['ga_total_order']) !!} </td>
                            <td {!! display_zero_cell_dashboard($v['budget'])!!} >{!! gifttify_price_format( $v['budget'], 0 ) !!} </td>
                            <td {!! display_row_bg_campaign_status($v['mo'], $v['totalSpend'], $v['status'], $account_status) !!}>{!! $account_status == 'DISABLED' ? 'DISABLED' : $v['status'] !!} </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const getCellValue = (tr, idx) => tr.children[idx].innerText.replace('$','').replace(',','').replace('%','') || tr.children[idx].textContent.replace('$','').replace(',','').replace('%','');

        const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
        )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

        // do the work...
        document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
            const table = th.closest('table');

            if ( th.classList.contains("sort_asc") ) {
                document.querySelectorAll('th').forEach( th => {
                    th.classList.remove("sort_asc")
                    th.classList.remove("sort_desc")}
                );
                th.classList.add("sort_desc");
                this.asc = false;
            } else if ( th.classList.contains("sort_desc") ) {
                document.querySelectorAll('th').forEach( th => {
                    th.classList.remove("sort_asc")
                    th.classList.remove("sort_desc")}
                );
                th.classList.add("sort_asc");
                this.asc = true;
            } else {
                document.querySelectorAll('th').forEach( th => {
                    th.classList.remove("sort_asc")
                    th.classList.remove("sort_desc")}
                );
                th.classList.add("sort_asc");
                this.asc = true;
            }

            Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc))
                .forEach( tr => table.appendChild(tr) );
        })));

        var filter_status = document.querySelector('#filter_status');
        filter_status.addEventListener('change',function() {
            const table = document.getElementById('campaign_info');
            const status = this.value;

            document.querySelectorAll('tr.tr_sortable').forEach( tr => tr.classList.remove("hidden"));
            if (status == 'ALL') return false;

            Array.from(table.querySelectorAll('tr.tr_sortable'))
                .forEach( tr => {
                    if (tr.children.item(12).innerText != status) {
                        tr.classList.add("hidden");
                    }
                } );
        });

        var filter_account_name = document.querySelector('#filter_account_name');
        filter_account_name.addEventListener('keyup',function() {
            const table = document.getElementById('campaign_info');
            const account_name = this.value;

            document.querySelectorAll('tr.tr_sortable').forEach( tr => tr.classList.remove("hidden"));
            if (account_name == '') return false;

            Array.from(table.querySelectorAll('tr.tr_sortable'))
                .forEach( tr => {
                    if ( !tr.children.item(1).innerText.includes(account_name) ) {
                        tr.classList.add("hidden");
                    }
                } );
        });

        var filter_campaign_name = document.querySelector('#filter_campaign_name');
        filter_campaign_name.addEventListener('change',function() {
            const table = document.getElementById('campaign_info');
            const campaign_name = this.value;

            document.querySelectorAll('tr.tr_sortable').forEach( tr => tr.classList.remove("hidden"));
            if (campaign_name == 'ALL') return false;

            Array.from(table.querySelectorAll('tr.tr_sortable'))
                .forEach( tr => {
                    if ( !tr.children.item(0).innerText.toLowerCase().includes(campaign_name) ) {
                        tr.classList.add("hidden");
                    }
                } );
        });

        var triggerClickElement = document.querySelector('#campaign_info thead th:nth-child(5)');
        triggerClickElement.click();
        triggerClickElement.click();

    </script>
</x-app-layout>
