<style>
    .table {
        text-align: right;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
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
                        <input type="hidden" name="fromDate" id="fromDate" />
                        <input type="hidden" name="toDate" id="toDate" />
                        <input type="hidden" name="labelDate" id="labelDate" />
                        <input type="hidden" name="debug" id="debug" value="{!! $_REQUEST['debug'] ?? '0';  !!} " />
                        <span id="reportrangetext">{!! $params['fromDate']->format('d-m-Y') . ' => ' . $params['toDate']->format('d-m-Y'); !!} </span>

                        <script type="text/javascript">
                            moment.tz.setDefault("America/Los_Angeles");
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
                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>Summary Report</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>Orders</th>
                            <th>Rev</th>
                            <th>FbAdCost</th>
                            <th>GoogleAdCost</th>
                            <th>SumAdCost</th>
                            <th>OtherCost</th>
                            <th>Profit/Loss</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reports['all'] as $v): ?>
                        <tr>
                            <td><?php echo '<b>' . $v['title'] . '</b>' . '<br/>' . "<span class='small'>{$v['dateDisplay']}</span>"; ?></br></td>
                            <td><?php echo $v['orders']['total']; ?></td>
                            <td><?php echo gifttify_price_format($v['orders']['totalAmount']); ?></td>
                            <td><?php echo gifttify_price_format($v['fbAds']['totalSpend']); ?></td>
                            <td><?php echo gifttify_price_format($v['ggAds']['ga_ad_cost']); ?></td>
                            <td><?php echo gifttify_price_format($v['fbAds']['totalSpend'] + $v['ggAds']['ga_ad_cost']); ?></td>
                            <td><?php echo gifttify_price_format($v['productCost']); ?></td>
                            <td><?php echo gifttify_price_format($v['profitLoss']); ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    @foreach($storesConfig as $st => $stConfig)
                        <table class="table table-responsive table-bordered" style="width: auto">
                            <h1>{{$stConfig['domain']}} <a href="/report_detail/{{$st}}" style="font-size: x-large;">View Detail</a></h1>
                            <thead>
                            <tr>
                                <th></th>
                                <th>Orders</th>
                                <th>Rev</th>
                                <th>FbAdCost</th>
                                <th>GoogleAdCost</th>
                                <th>SumAdCost</th>
                                <th>OtherCost</th>
                                <th>Profit/Loss</th>
                                <th>MO</th>
                                <th>CPC</th>
                                <th>AOV</th>
                                <th>FbBudget</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports[$st] as $v): ?>
                            <tr>
                                <td><?php echo '<b>' . $v['title'] . '</b>' . '<br/>' . "<span class='small'>{$v['dateDisplay']}</span>"; ?></br></td>
                                <td><?php echo $v['orders']['total']; ?></td>
                                <td><?php echo gifttify_price_format($v['orders']['totalAmount']); ?></td>
                                <td><?php echo gifttify_price_format($v['fbAds']['totalSpend']); ?></td>
                                <td><?php echo gifttify_price_format($v['ggAds']['ga_ad_cost']); ?></td>
                                <td><?php echo gifttify_price_format($v['fbAds']['totalSpend'] + $v['ggAds']['ga_ad_cost']); ?></td>
                                <td><?php echo gifttify_price_format($v['productCost']); ?></td>
                                <td><?php echo gifttify_price_format($v['profitLoss']); ?></td>
                                <td><?php echo round($v['mo']) . '%'; ?></td>
                                <td><?php echo in_array($v['title'], array('Today', 'Yesterday')) ? number_format($v['cpc'], 2) : ''; ?></td>
                                <td><?php echo gifttify_price_format($v['aov']); ?></td>
                                <td><?php echo gifttify_price_format($v['fbAds']['dailyBudget'], 0); ?></td>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

</x-app-layout>


