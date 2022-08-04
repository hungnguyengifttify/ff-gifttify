<style>
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
                            <th>AdsCost</th>
                            <th>Rev</th>
                            <th>CPC</th>
                            <th>CPM</th>
                            <th>MO</th>
                            <th>OrdersQty</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($campaigns as $v): ?>
                        <tr {!! display_row_bg_dashboard($v['mo']) !!} >
                            <td>{!! $v['campaign_name'] !!} </td>
                            <td {!! display_zero_cell_dashboard($v['totalSpend'])!!} >{!! gifttify_price_format($v['totalSpend']);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['total_order_amount'])!!} >{!! gifttify_price_format($v['total_order_amount']);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['cpc'])!!} >{!! number_format($v['cpc'], 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['cpm'])!!} >{!! number_format($v['cpm'], 2);  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['mo'])!!} >{!! round($v['mo']) . '%';  !!} </td>
                            <td {!! display_zero_cell_dashboard($v['total_order'])!!} >{!! round($v['total_order']) !!} </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
