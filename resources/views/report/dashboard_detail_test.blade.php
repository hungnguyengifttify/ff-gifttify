<style>
    .table {
        text-align: right;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Store Report Detail

            <select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                <option <?php if (str_contains(url()->current(), '/report_detail/us') ) echo "selected"; ?> value="/report_detail/us">US</option>
                <option <?php if (str_contains(url()->current(), '/report_detail/au') ) echo "selected"; ?> value="/report_detail/au">AU</option>
            </select>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="reportrangeform" method="get">
                <div class="bg-white overflow-hidden shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <a id="reportrange" class="btn btn-primary" href="#">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span><?php echo $params['labelDate'];?></span> <i class="fa fa-caret-down"></i>
                        </a>
                        <input type="hidden" name="fromDate" id="fromDate" />
                        <input type="hidden" name="toDate" id="toDate" />
                        <input type="hidden" name="labelDate" id="labelDate" />
                        <span id="reportrangetext"><?php echo $params['fromDate']->format('d-m-Y') . ' => ' . $params['toDate']->format('d-m-Y');?></span>

                        <script type="text/javascript">
                            <?php if ($store == "us"):; ?>
                            moment.tz.setDefault("America/Los_Angeles");
                            <?php else: ?>
                            moment.tz.setDefault("Australia/Sydney");
                            <?php endif; ?>

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
                        <h1>By Account</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                            <th>CPC</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($accountsAds as $acc): ?>
                        <tr>
                            <td><?php echo $acc['account_name'] ?></td>
                            <td><?php echo gifttify_price_format($acc['totalSpend']); ?></td>
                            <td><?php echo number_format($acc['cpc'], 2); ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>By Country</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                            <th>Rev</th>
                            <th>MO</th>
                            <th>TotalOrders</th>
                            <th>CPC</th>
                            <th>AOV</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $sumTotalSpend = $sumTotalOrderAmount = $sumTotalOrders = 0; ?>
                        <?php foreach ($countriesAds as $v): ?>
                        <tr>
                            <?php $sumTotalSpend += $v['totalSpend'];  ?>
                            <?php $sumTotalOrderAmount += $v['total_order_amount'];  ?>
                            <?php $sumTotalOrders += $v['total_order'];  ?>
                            <td><?php echo $v['country_code']; ?></td>
                            <td><?php echo gifttify_price_format($v['totalSpend']); ?></td>
                            <td><?php echo gifttify_price_format($v['total_order_amount']); ?></td>
                            <td><?php echo round($v['mo']) . '%'; ?></td>
                            <td><?php echo round($v['total_order']); ?></td>
                            <td><?php echo number_format($v['cpc'], 2); ?></td>
                            <td><?php echo gifttify_price_format($v['aov']); ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <?php $sumMo = $sumTotalOrderAmount != 0 ? 100*($sumTotalSpend/$sumTotalOrderAmount) : 0;  ?>

                            <td>All-Country</td>
                            <td><?php echo gifttify_price_format($sumTotalSpend); ?></td>
                            <td><?php echo gifttify_price_format($sumTotalOrderAmount); ?></td>
                            <td><?php echo round($sumMo) . '%'; ?></td>
                            <td><?php echo round($sumTotalOrders) ; ?></td>
                            <td colspan="3"></td>
                        </tr>
                        </tfoot>
                    </table>

                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>By Product Type</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                            <th>Rev</th>
                            <th>CPC</th>
                            <th>MO</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($productTypes as $v): ?>
                        <tr>
                            <td><?php echo $v['product_type_name'] . "[{$v['product_type_code']}]"; ?></td>
                            <td><?php echo gifttify_price_format($v['totalSpend']); ?></td>
                            <td><?php echo gifttify_price_format($v['total_order_amount']); ?></td>
                            <td><?php echo number_format($v['cpc'], 2); ?></td>
                            <td><?php echo round($v['mo']) . '%'; ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>By Ads Type</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                            <th>Percent</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($adsTypes as $v): ?>
                        <tr>
                            <td><?php echo $v['ads_type']; ?></td>
                            <td><?php echo gifttify_price_format($v['totalSpend']); ?></td>
                            <td><?php echo round($v['percent'],2) . '%'; ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>By Designer</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                            <th>Rev</th>
                            <th>CPC</th>
                            <th>MO</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($designerAds as $v): ?>
                        <tr>
                            <td><?php echo $v['designer_name'] . "[{$v['designer_code']}]"; ?></td>
                            <td><?php echo gifttify_price_format($v['totalSpend']); ?></td>
                            <td><?php echo gifttify_price_format($v['total_order_amount']); ?></td>
                            <td><?php echo number_format($v['cpc'], 2); ?></td>
                            <td><?php echo round($v['mo']) . '%'; ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>By Idea</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                            <th>Rev</th>
                            <th>CPC</th>
                            <th>MO</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ideaAds as $v): ?>
                        <tr>
                            <td><?php echo $v['idea_name'] . "[{$v['idea_code']}]"; ?></td>
                            <td><?php echo gifttify_price_format($v['totalSpend']); ?></td>
                            <td><?php echo gifttify_price_format($v['total_order_amount']); ?></td>
                            <td><?php echo number_format($v['cpc'], 2); ?></td>
                            <td><?php echo round($v['mo']) . '%'; ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>By Ads Staff</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>AdsCost</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($adsStaffs as $v): ?>
                        <tr>
                            <td><?php echo $v['adsStaff']; ?></td>
                            <td><?php echo gifttify_price_format($v['totalSpend']); ?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
