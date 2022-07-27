<style>
    .table {
        text-align: right;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Order Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form class="row g-3">
                        <div class="col-sm-8">
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Date filter:</label>
                                <div class="col-sm-6">
                                    <input type="date" name="fromDate" value="{{ $params['fromDate'] }}" placeholder="From Date" />
                                    <input type="date" name="toDate" value="{{ $params['toDate'] }}" placeholder="To Date" />
                                </div>
                                <label class="col-sm-2 col-form-label">Items Display:</label>
                                <div class="col-sm-2">
                                    <select name="displayItemQty">
                                        <option value="10" {{ $params['displayItemQty'] == 10 ? "selected" : "" }}>10</option>
                                        <option value="50" {{ $params['displayItemQty'] == 50 ? "selected" : "" }}>50</option>
                                        <option value="100" {{ $params['displayItemQty'] == 100 ? "selected" : "" }}>100</option>
                                        <option value="500" {{ $params['displayItemQty'] == 500 ? "selected" : "" }}>500</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary mb-3" name="action" value="filter">Submit</button>
                            <button type="submit" class="btn btn-warning mb-3" name="action" value="export">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Sku</th>
                                <th>Order Image</th>
                                <th>Link Image</th>
                                <th>Design Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td style="max-width: 350px;">
                                        {{ $order->store }} - {{ $order->name }}<br/>
                                        {{ $order->shopify_created_at }}<br/>
                                        {{ $order->type }}<br/>
                                        {{ $order->size }}
                                    </td>
                                    <td>{{ $order->sku }}{!! $order->note ? '<br/>' . $order->note : '' !!}</td>
                                    <td>@if ($order->link)<img width="100px" src="{{ $order->order_image }}" />@endif</td>
                                    <td>@if ($order->link)<img width="100px" src="{{ $order->link_image }}" />@endif</td>
                                    <td>@if ($order->link)<a href="{{ $order->link }}" target="_blank">Link</a>@endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
