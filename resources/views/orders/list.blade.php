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
                                <th>Store</th>
                                <th>Order</th>
                                <th>Created Time</th>
                                <th>Sku</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Design Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->store }}</td>
                                    <td>{{ $order->name }}</td>
                                    <td>{{ $order->shopify_created_at }}</td>
                                    <td>{{ $order->sku }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{!! gifttify_price_format($order->price) !!}</td>
                                    <td>
                                        @php $link = $order->link1 ?? $order->link2 ?? $order->link3 ?? $order->link4 ?? $order->link5 ?? $order->link6 ?? $order->link7 ?? '';
                                        @endphp
                                        @if ($link)
                                            <a href="{{ $link }}" target="_blank">Link</a>
                                        @endif
                                    </td>
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
