<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo $title; ?>
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
                                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
            <div class="bg-white overflow-hidden shadow-sm">
                <div class="p-6 bg-white border-b border-gray-200">
                    <?php $totalSpend = $fbAds ? $fbAds->totalSpend : 0; ?>
                    <?php $totalOrders = $orders ? count($orders) : 0; ?>
                    <table class="table" style="width: auto">
                        <tbody>
                            <tr>
                                <td>Số lượng đơn hàng</td>
                                <td><?php echo $totalOrders; ?></td>
                            </tr>
                            <tr>
                                <td>Số tiền đã chi tiêu</td>
                                <td><?php echo '$' . number_format($totalSpend, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
