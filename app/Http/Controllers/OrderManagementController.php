<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Spreadsheet;
use App\Models\Orders;
use App\Models\GoogleDriveFiles;

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
        $orders = $this->getMoreData($orders);

        if ($action == 'export') {
            return $this->exportOrdersToSupplier($orders);
        }
        return view('orders.list', compact('orders', 'params'));
    }

    protected function getMoreData ($data) {
        foreach ($data as $k => $v) {
            $order_image = json_decode($v->image);
            $order_image = $order_image->src ? str_replace('.jpg', '_180x.jpg', $order_image->src) : '';

            $address = json_decode($v->shipping_address);
            $properties = json_decode($v->properties);

            $variant_title = str_replace(
                array('(Best For Single)', '(Best For Couple)', '(Best For Family)', '- Best Seller'),
                array('', '', '', ''),
                $v->variant_title
            );

            $color = '';
            $pSize = '';
            foreach ($properties as $p) {
                if ($p->name != '_cl_options') {
                    $pSize .= "{$p->name}:{$p->value}, ";
                }

                if (strtolower($p->name) == 'color') {
                    $color = $p->value;
                }
            }
            $pSize = trim($pSize, ', ');
            $size = $pSize ? $pSize . ', ' . $variant_title : $variant_title;
            $size = str_replace(array('"', "'"), array('', ''), $size);

            $result = array();
            preg_match('/([1-4])\s?pcs/', $size, $result);
            $pc = $result[1] ?? 1;
            $quantity = $v->quantity * $pc;

            $type = $v->product_type;
            if (strtoupper($type) == 'DOORMAT' && str_contains(strtoupper($size), 'RUBBER') == false && str_contains(strtoupper($size), 'PVC') == false && str_contains(strtoupper($size), 'FLANNEL') == false ) {
                $type = 'Rubber Doormat';
            }

            if ($color != '') {
                $t = Orders::getDesignLinkFromSkuAndColor($v->store, $v->sku, $color);
                $link = $t->link1 ?? $t->link2 ?? $t->link3 ?? $t->link4 ?? $t->link5 ?? $t->link6 ?? $t->link7 ?? $t->link8 ?? $t->link9 ?? $t->link10 ?? $t->link11 ?? $t->link12 ?? $t->link13 ?? $t->link14 ?? $t->link15 ?? $t->link16 ?? $t->link17 ?? $t->link18 ?? $t->link19 ?? $t->link20 ?? $t->link21 ?? $t->link22 ?? $t->link23 ?? $t->link24 ?? $t->link25 ?? $t->link26 ?? $t->link27 ?? $t->link28 ?? '';
                $note = $t->note1 ?? $t->note2 ?? $t->note3 ?? $t->note4 ?? $t->note5 ?? $t->note6 ?? $t->note7 ?? $t->note8 ?? $t->note9 ?? $t->note10 ?? $t->note11 ?? $t->note12 ?? $t->note13 ?? $t->note14 ?? $t->note15 ?? $t->note16 ?? $t->note17 ?? $t->note18 ?? $t->note19 ?? $t->note20 ?? $t->note21 ?? $t->note22 ?? $t->note23 ?? $t->note24 ?? $t->note25 ?? $t->note26 ?? $t->note27 ?? $t->note28 ?? '';
            } else {
                $link = $v->link1 ?? $v->link2 ?? $v->link3 ?? $v->link4 ?? $v->link5 ?? $v->link6 ?? $v->link7 ?? $v->link8 ?? $v->link9 ?? $v->link10 ?? $v->link11 ?? $v->link12 ?? $v->link13 ?? $v->link14 ?? $v->link15 ?? $v->link16 ?? $v->link17 ?? $v->link18 ?? $v->link19 ?? $v->link20 ?? $v->link21 ?? $v->link22 ?? $v->link23 ?? $v->link24 ?? $v->link25 ?? $v->link26 ?? $v->link27 ?? $v->link28 ?? '';
                $note = $v->note1 ?? $v->note2 ?? $v->note3 ?? $v->note4 ?? $v->note5 ?? $v->note6 ?? $v->note7 ?? $v->note8 ?? $v->note9 ?? $v->note10 ?? $v->note11 ?? $v->note12 ?? $v->note13 ?? $v->note14 ?? $v->note15 ?? $v->note16 ?? $v->note17 ?? $v->note18 ?? $v->note19 ?? $v->note20 ?? $v->note21 ?? $v->note22 ?? $v->note23 ?? $v->note24 ?? $v->note25 ?? $v->note26 ?? $v->note27 ?? $v->note28 ?? '';
            }

            $address_street = $address && $address->address1 != '' ? trim($address->address1 . ", " . $address->address2, ', ') : $address->address2 ?? '';

            $data[$k]->link = $link;

            if (preg_match("/[A-Z]{2}\d{4,5}[A-Z]{2,7}\d{2}/", $data[$k]->sku) && $link == '' ) {
                $data[$k]->link = GoogleDriveFiles::getLinkByNewFormat($data[$k]->sku);
            }

            $data[$k]->note = $note;
            $data[$k]->size = $size;
            $data[$k]->type = $type;
            $data[$k]->quantity = $quantity;
            $data[$k]->address = $address;
            $data[$k]->address_street = $address_street;
            $data[$k]->order_image = $order_image;
            $data[$k]->link_image = GoogleDriveFiles::getFirstImageFromParentFolder($note, $link);
        }
        return $data;
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
            $line = array(
                $v->name, $v->quantity, $v->type, $v->size, $v->item_name, $v->sku,
                $v->address->name ?? '', $v->address_street, $v->address->address1 ?? '', $v->address->address2 ?? '',
                $v->address->company ?? '', $v->address->city ?? '', $v->address->zip ?? '', $v->address->province ?? '',
                $v->address->country ?? '', $v->address->phone ?? '', $v->note, $v->link, ''
            );
            $excelData[] = $line;
        }
        $fileName = "orders_to_supplier_" . time() . '.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }
}
