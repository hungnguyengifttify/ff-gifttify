<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Track17Service;

class TrackingController extends Controller
{

    private $track17Service;

    public function __construct(Track17Service $track17Service)
    {
        $this->track17Service = $track17Service;
    }

    public function registerTrackingNumber(Request $request)
    {
        $response = [
            'status' => false,
            'data' => [],
            'message' => 'Mã đơn vận được khai báo rồi!'
        ];

        $id = $request->id;
        $checkRegisted = $this->track17Service->getTrackInfo($id);
        if (isset($checkRegisted['error']['code']) &&  $checkRegisted['error']['code'] == '-18019902') {
            $response['status'] = true;
            $response['message'] = 'Khai báo thành công!';
            $register = $this->track17Service->registerTrackingNumber($id);

            if (isset($register['error']) && count($register['error'])) {
                $response['status'] = false;
                $response['message'] = $checkRegisted['error']['message'];
            }
        }

        return response()->json($response);
    }

    public function getTrackInfo(Request $request)
    {
        $response = [
            'status' => true,
            'data' => [],
            'error' => [],
            'message' => 'Get tracking success!'
        ];

        $id = $request->id;
        $dataTrack = $this->track17Service->getTrackInfo($id);
        if (isset($dataTrack['error']['code']) &&  $dataTrack['error']['code'] == '-18019902') {
            $register = $this->track17Service->registerTrackingNumber($id);
            if (isset($register['error']) && count($register['error'])) {
                $response['status'] = false;
                $response['message'] = $register['error']['message'];
                goto next;
            }else{
                $dataTrack = $this->track17Service->getTrackInfo($id);
            }
        }

        if (isset($dataTrack['error'])) {
            $response['status'] = false;
            $response['message'] = $dataTrack['error']['message'];
        } else {
            $response['data'] = $dataTrack;
        }

        next:

        $getHtml = $request->getHtml ?? false;
        if ($getHtml) {
            $html = 'No information. We seem can\'t identify the number.';
            if (isset($response['data']['tracking'])) {
                $html .= "<h5>Shipping from {$response['data']['shipper_address']['country']} to {$response['data']['recipient_address']['country']}</h5>";
                $html .= "<table>";
                foreach ($response['data']['tracking'] as $k => $v) {
                    $html .= "<tr>";
                    $html .= "<td style='width:210px;'>{$v['time']}</td>";
                    $html .= "<td>{$v['content']}</td>";
                    $html .= "</tr>";
                }
                $html .= "<table>";
            }
            $response['html'] = $html;
        }
        return response()->json($response);
    }
}
