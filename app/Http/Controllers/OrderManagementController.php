<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Spreadsheet;
use App\Models\Orders;

class OrderManagementController extends Controller {

    public function list(Request $request)
    {
        $action = $request->input('action') ?? '';

        $displayItemQty = $request->input('displayItemQty') ?? 50;
        $fromDate = $request->input('fromDate') ?? Carbon::yesterday()->format('Y-m-d');
        $toDate = $request->input('toDate') ?? Carbon::now()->format('Y-m-d');

        $params = array(
            'displayItemQty' => $displayItemQty,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        );

        $orders = Orders::getList($fromDate, $toDate, $displayItemQty);

        if ($action == 'export') {
            return $this->exportOrdersToSupplier($orders);
        }
        return view('orders.list', compact('orders', 'params'));
    }

    protected function exportOrdersToSupplier ($data) {
        $excelData = array(
            array('Name', 'Lineitem quantity', 'Type', 'Size', 'Lineitem name', 'Lineitem sku',
                'Shipping Name', 'Shipping Street', 'Shipping Address1', 'Shipping Address2',
                'Shipping Company', 'Shipping City', 'Shipping Zip', 'Shipping Province',
                'Shipping Country', 'Shipping Phone', 'Notes', 'Design', 'Price'
            )
        );
        foreach ($data as $v) {
            $address = json_decode($v->shipping_address);
            $link = $v->link1 ?? $v->link2 ?? $v->link3 ?? $v->link4 ?? $v->link5 ?? $v->link6 ?? $v->link7 ?? '';

            $line = array(
                $v->name, $v->quantity, $v->product_type, $v->variant_title, $v->item_name, $v->sku,
                $address->name ?? '', '', $address->address1 ?? '', $address->address2 ?? '',
                $address->company ?? '', $address->city ?? '', $address->zip ?? '', $address->province ?? '',
                $address->country ?? '', $address->phone ?? '', '', $link, ''
            );
            $excelData[] = $line;
        }
        $fileName = "orders_to_supplier_" . time() . '.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }
}
