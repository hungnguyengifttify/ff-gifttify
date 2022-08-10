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
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table table-responsive table-bordered" style="width: auto">
                        <h1>Summary Report</h1>
                        <thead>
                        <tr>
                            <th></th>
                            <th>Orders</th>
                            <th>Rev</th>
                            <th>AdCost</th>
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
                                <th>AdCost</th>
                                <th>OtherCost</th>
                                <th>Profit/Loss</th>
                                <th>MO</th>
                                <th>CPC</th>
                                <th>AOV</th>
                                <th>Budget</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reports[$st] as $v): ?>
                            <tr>
                                <td><?php echo '<b>' . $v['title'] . '</b>' . '<br/>' . "<span class='small'>{$v['dateDisplay']}</span>"; ?></br></td>
                                <td><?php echo $v['orders']['total']; ?></td>
                                <td><?php echo gifttify_price_format($v['orders']['totalAmount']); ?></td>
                                <td><?php echo gifttify_price_format($v['fbAds']['totalSpend']); ?></td>
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
