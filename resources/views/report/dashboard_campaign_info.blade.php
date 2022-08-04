<style>
    .table {
        text-align: right;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Campaign Info - Store {{strtoupper($params['store'])}} - From {{$params['fromDate']}} to {{$params['toDate']}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table table-responsive table-bordered" style="width: auto">
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
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
