<style>
    .table {
        text-align: right;
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
                            <th>CampaignName</th>
                            <th>AccountName</th>
                            <th>AdsCost</th>
                            <th>Rev</th>
                            <th>CPC</th>
                            <th>CPM</th>
                            <th>MO</th>
                            <th>OrdersQty</th>
                            <th>Budget</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sum_totalSpend = array_sum(array_column($campaigns,'totalSpend'));
                            $sum_total_order_amount = array_sum(array_column($campaigns,'total_order_amount'));

                            $sum_cpc = array_sum(array_column($campaigns,'totalUniqueClicks')) != 0 ? array_sum(array_column($campaigns,'totalSpend')) / array_sum(array_column($campaigns,'totalUniqueClicks')) : 0;
                            $sum_cpm = array_sum(array_column($campaigns,'impressions')) != 0 ? 1000 * array_sum(array_column($campaigns,'totalSpend')) / array_sum(array_column($campaigns,'impressions')) : 0;
                            $sum_mo = $sum_total_order_amount > 0 ? 100 * ($sum_totalSpend / $sum_total_order_amount) : 0;

                            $sum_total_order = array_sum(array_column($campaigns,'total_order'));
                            $sum_budget = array_sum(array_column($campaigns,'budget'));
                        @endphp
                        <tr {!! display_row_bg_dashboard($sum_mo) !!} class="fw-bold" >
                            <td>Total</td>
                            <td>All Accounts</td>
                            <td {!! display_zero_cell_dashboard( $sum_totalSpend )!!} >{!! gifttify_price_format( $sum_totalSpend );  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_total_order_amount )!!} >{!! gifttify_price_format( $sum_total_order_amount );  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_cpc )!!} >{!! number_format($sum_cpc, 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_cpm )!!} >{!! number_format($sum_cpm, 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_mo )!!} >{!! round( $sum_mo ) . '%';  !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_total_order )!!} >{!! round( $sum_total_order ) !!} </td>
                            <td {!! display_zero_cell_dashboard( $sum_budget )!!} >{!! gifttify_price_format( $sum_budget, 0 ) !!} </td>
                            <td></td>
                        </tr>

                        <?php foreach ($campaigns as $v): ?>
                        <tr {!! display_row_bg_dashboard($v['mo']) !!} >
                            <td>{!! $v['campaign_name'] !!} </td>
                            <td>{!! $v['account_name'] !!} </td>
                            <td {!! display_zero_cell_dashboard($v['totalSpend'])!!} >{!! gifttify_price_format($v['totalSpend']);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['total_order_amount'])!!} >{!! gifttify_price_format($v['total_order_amount']);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['cpc'])!!} >{!! number_format($v['cpc'], 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['cpm'])!!} >{!! number_format($v['cpm'], 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['mo'])!!} >{!! round($v['mo']) . '%';  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['total_order'])!!} >{!! round($v['total_order']) !!} </td>
                            <td {!! display_zero_cell_dashboard($v['budget'])!!} >{!! gifttify_price_format( $v['budget'], 0 ) !!} </td>
                            <td>{!! $v['status'] !!} </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
