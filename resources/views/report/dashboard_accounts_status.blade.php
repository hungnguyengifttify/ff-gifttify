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
            Account Status
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table id="campaign_info" class="table table-responsive table-bordered" style="width: auto">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Store</th>
                            <th>AmountSpent</th>
                            <th>Balance</th>
                            <th>AccountStatus</th>
                            <th>DisableReason</th>
                            <th>CreatedTime</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($accounts as $v)
                        <tr>
                            <td>{{$v->id}}</td>
                            <td>{{$v->name}}</td>
                            <td style="white-space: nowrap;">{!! \App\Models\Dashboard::getStoreFromAccountId($v->id) !!}</td>
                            <td>{!! gifttify_price_format($v->amount_spent, 0) !!}</td>
                            <td>{{ gifttify_price_format($v->balance, 0) }}</td>
                            <td>{{ \App\Models\FbAccount::$status[$v->account_status] ?? '' }}</td>
                            <td>{{ \App\Models\FbAccount::$disable_reason[$v->disable_reason] ?? '' }}</td>
                            <td>{{$v->created_time}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
