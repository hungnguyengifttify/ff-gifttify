<script>
    function selectElementContents(el) {
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
            }
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
        }
        document.execCommand("Copy");
    }
    function copyTableWithoutHeader(tableId){
        document.getElementById('headOfTableId').style.display = 'none';
        selectElementContents( document.getElementById(tableId) );
        document.getElementById('headOfTableId').style.display = '';
    }
</script>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container mt-5 mb-5" style="overflow-x: scroll;">
                <form method="get" id="formFilter">
                    <div class="row justify-content-sm-between">
                        <div class="col-md-6 col-lg-2">
                            Total items: {{$total}}
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <select name="limit" class="form-select" onchange="document.getElementById('formFilter').submit();">
                                <option value="">Items per page</option>
                                <option value="1" @if($limit == 1) selected @endif >1</option>
                                <option value="2" @if($limit == 2) selected @endif >2</option>
                                <option value="3" @if($limit == 3) selected @endif >3</option>
                                <option value="4" @if($limit == 4) selected @endif >4</option>
                                <option value="5" @if($limit == 5) selected @endif >5</option>
                                <option value="6" @if($limit == 6) selected @endif >6</option>
                                <option value="7" @if($limit == 7) selected @endif >7</option>
                                <option value="8" @if($limit == 8) selected @endif >8</option>
                                <option value="9" @if($limit == 9) selected @endif >9</option>
                                <option value="10" @if($limit == 10) selected @endif >10</option>
                                <option value="15" @if($limit == 15) selected @endif >15</option>
                                <option value="20" @if($limit == 20) selected @endif >20</option>
                                <option value="50" @if($limit == 50) selected @endif >50</option>
                            </select>
                        </div>
                    </div>
                </form>
                <table id="tableId" class="table table-bordered table-responsive mb-5 mt-2" style="width: 100%">
                    <thead id="headOfTableId">
                    <tr class="table-success">
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Paid at</th>
                        <th scope="col">Lineitem quantity</th>
                        <th scope="col"><div style="width: 300px;">Lineitem name</div></th>
                        <th scope="col"><div style="width: 120px;">Shipping Name</div></th>
                        <th scope="col"><div style="width: 300px;">Shipping Street</div></th>
                        <th scope="col"><div style="width: 160px;">Shipping Address1</div></th>
                        <th scope="col"><div style="width: 160px;">Shipping Address2</div></th>
                        <th scope="col"><div style="width: 160px;">Shipping City</div></th>
                        <th scope="col">Shipping Zip</th>
                        <th scope="col">Shipping Province</th>
                        <th scope="col">Shipping Country</th>
                        <th scope="col"><div style="width: 100px;">Shipping Phone</div></th>
                        <th scope="col"><div style="width: 160px;">Note</div></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $stt = 1; ?>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $stt++ }}</td>
                            <td>{{ $order['name'] }}</td>
                            <td>{{ $order['email'] }}</td>
                            <td>{{ $order['processed_at'] }}</td>
                            <td>{{ $order['order_item_quantity'] }}</td>
                            <td>{{ $order['order_item_name'] }}</td>
                            <td>{{ $order['shipping_address']['name'] }}</td>
                            <td>{{ $order['shipping_address']['street'] }}</td>
                            <td>{{ $order['shipping_address']['address1'] }}</td>
                            <td>{{ $order['shipping_address']['address2'] }}</td>
                            <td>{{ $order['shipping_address']['city'] }}</td>
                            <td>{{ $order['shipping_address']['zip'] }}</td>
                            <td>{{ $order['shipping_address']['province_code'] }}</td>
                            <td>{{ $order['shipping_address']['country_code'] }}</td>
                            <td>{{ $order['shipping_address']['phone'] }}</td>
                            <td>{{ $order['note'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
