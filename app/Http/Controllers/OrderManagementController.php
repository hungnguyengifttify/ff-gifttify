<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Spreadsheet;
use App\Models\Orders;

class OrderManagementController extends Controller {

    public function list(Request $request, $store = 'us')
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

        $orders = Orders::getList($store, $fromDate, $toDate, $displayItemQty);

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
            $properties = json_decode($v->properties);

            $variant_title = str_replace(
                array('(Best For Single)', '(Best For Couple)', '(Best For Family)', '- Best Seller'),
                array('', '', '', ''),
                $v->variant_title
            );
            $pSize = '';
            foreach ($properties as $p) {
                if ($p->name != '_cl_options') {
                    $pSize .= "{$p->name}:{$p->value}, ";
                }
            }
            $pSize = trim($pSize, ', ');
            $size = $pSize ? $pSize . ', ' . $variant_title : $variant_title;
            $size = str_replace(array('"', "'"), array('', ''), $size);

            $result = array();
            preg_match('/([1-4])\s?pcs/', $size, $result);
            $pc = $result[1] ?? 1;
            $quantity = $v->quantity * $pc;

            $link = $v->link1 ?? $v->link2 ?? $v->link3 ?? $v->link4 ?? $v->link5 ?? $v->link6 ?? $v->link7 ?? $v->link8 ?? $v->link9 ?? $v->link10 ?? $v->link11 ?? $v->link12 ?? $v->link13 ?? $v->link14 ?? $v->link15 ?? $v->link16 ?? $v->link17 ?? $v->link18 ?? $v->link19 ?? $v->link20 ?? $v->link21 ?? $v->link22 ?? $v->link23 ?? $v->link24 ?? $v->link25 ?? $v->link26 ?? $v->link27 ?? $v->link28 ?? '';
            $note = $v->note1 ?? $v->note2 ?? $v->note3 ?? $v->note4 ?? $v->note5 ?? $v->note6 ?? $v->note7 ?? $v->note8 ?? $v->note9 ?? $v->note10 ?? $v->note11 ?? $v->note12 ?? $v->note13 ?? $v->note14 ?? $v->note15 ?? $v->note16 ?? $v->note17 ?? $v->note18 ?? $v->note19 ?? $v->note20 ?? $v->note21 ?? $v->note22 ?? $v->note23 ?? $v->note24 ?? $v->note25 ?? $v->note26 ?? $v->note27 ?? $v->note28 ?? '';

            $address_street = $address && $address->address1 != '' ? trim($address->address1 . ", " . $address->address2, ', ') : $address->address2 ?? '';

            $line = array(
                $v->name, $quantity, $v->product_type, $size, $v->item_name, $v->sku,
                $address->name ?? '', $address_street, $address->address1 ?? '', $address->address2 ?? '',
                $address->company ?? '', $address->city ?? '', $address->zip ?? '', $address->province ?? '',
                $address->country ?? '', $address->phone ?? '', $note, $link, ''
            );
            $excelData[] = $line;
        }
        $fileName = "orders_to_supplier_" . time() . '.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }
}
