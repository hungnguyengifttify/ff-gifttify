<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GoogleDriveFiles;
use App\Services\OdooService;
use App\Services\Spreadsheet;
use Carbon\Carbon;

class ToolsController extends Controller {

    public function get_image_links(Request $request)
    {
        $link = $request->input('link') ?? '';
        $action = $request->input('action') ?? '';
        $uriArr = parse_url($link);
        $path = $uriArr['path'] ? explode('/', $uriArr['path']) : '';

        $id = '';
        if ($path) {
            $id = isset($path[count($path)-1]) ? $path[count($path)-1] : '';
        }

        $result = array();

        if ($id && $action == 'download_csv_v2') {
            $result_v2 = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id, 'tree');
            $this->export_image_links_v2($result_v2);
        }

        if ($id) {
            //$result = GoogleDriveFiles::flat_image_links_from_folder_id($id);
            $result = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id);
        }
        if ($action == 'download_csv') {
            $this->export_image_links($result);
            return true;
        }
        return view('tools.image_links', compact('link', 'result') );
    }

    protected function export_image_links ($data) {
        $excelData = array();
        foreach ($data as $v) {
            $line = array();
            for ($i = 0; $i < $v['level'] - 1; $i++) {
                $line[] = '';
            }
            $line[] = $v['name'];
            if ($v['type'] == 'image') {
                $line[] = $v['link'];
            }

            $excelData[] = $line;
        }
        $fileName = "image_links_" . time() . '.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }

    protected function export_image_links_v2($data)
    {
        $odooService = new OdooService();

        $header = [
            'Handle',
            'Title',
            'Body (HTML)',
            'Vendor',
            'Type',
            'Tags',
            'Published',
            'Option1 Name',
            'Option1 Value',
            'Option2 Name',
            'Option2 Value',
            'Option3 Name',
            'Option3 Value',
            'Variant SKU',
            'Variant Grams',
            'Variant Inventory Tracker',
            'Variant Inventory Qty',
            'Variant Inventory Policy',
            'Variant Fulfillment Service',
            'Variant Price',
            'Variant Compare At Price',
            'Variant Requires Shipping',
            'Variant Taxable',
            'Variant Barcode',
            'Image Src',
            'Image Position',
            'Image Alt Text',
            'Gift Card',
            'SEO Title',
            'SEO Description',
            'Google Shopping / Google Product Category',
            'Google Shopping / Gender',
            'Google Shopping / Age Group',
            'Google Shopping / MPN',
            'Google Shopping / AdWords Grouping',
            'Google Shopping / AdWords Labels',
            'Google Shopping / Condition',
            'Google Shopping / Custom Product',
            'Google Shopping / Custom Label 0',
            'Google Shopping / Custom Label 1',
            'Google Shopping / Custom Label 2',
            'Google Shopping / Custom Label 3',
            'Google Shopping / Custom Label 4',
            'Variant Image',
            'Variant Weight Unit',
            'Variant Tax Code',
            'Cost per item',
            'Status'
        ];

        $productTypeTable = DB::table('product_type')->get()->keyBy('product_type_code')->toArray();
        $rowData = [];
        $numberValue = count($header);
        for ($i = 0; $i < $numberValue; $i++) {
            $rowData[$i] = '';
        }

        $excelData = [];
        $excelData[] = $header;
        $p = 1;

        foreach ($data as $key => $dateData) {
            foreach ($dateData->children as $product) {
                foreach ($product->children as $product_variable) {
                    $tags = [];

                    $namePTypes = explode(' @NamePType ', $product_variable->name);
                    $tCodes = explode('! ', str_replace(['(', ')'], '', $namePTypes[2]));
                    
                    $codePType = str_replace(['t^ptype_', '@CodePType^'], '', trim($tCodes[2]));
                    $typeName = isset($productTypeTable[$codePType]) ? $productTypeTable[$codePType]->product_type_name : $codePType;
                    $productTempate = $odooService->getProductByProductType($codePType);

                    $listProductVariants = [];
                    if (count($productTempate) && isset($productTempate['id'])) {
                        $listProductVariants = $odooService->getProductVariantByTemplateId($productTempate['id']);
                    }
                    $productTypeDesc = $odooService->getProductTypeInfo($codePType)['x_studio_description_html'] ?? '';

                    $title = $namePTypes[0] . ' ' .  $typeName . ' ' . $namePTypes[1];
                    $colection = str_replace(['t^collection'], 'collection', trim($tCodes[3]));
                    $tags[] = str_replace(['t^name_'], '', trim($tCodes[1]));
                    $tags[] = $typeName;
                    $tags[] = $colection;

                    $rowData[array_search('Vendor', $header)] = str_replace(['t^code_', '@CodePType^', ' '], '', trim($tCodes[0]));
                    $rowData[array_search('Tags', $header)] = implode(', ', $tags);
                    $rowData[array_search('Title', $header)] =  $title;
                    $rowData[array_search('Type', $header)] =  $codePType;
                    $rowData[array_search('Handle', $header)] =  strtolower(str_replace([',',')','('], '', str_replace([' '], '_', $title))) . '_' . Carbon::now()->format('dmy') . '_p' . $p;
                    $rowData[array_search('Body (HTML)', $header)] = $productTypeDesc;

                    if (isset($listProductVariants)) {
                        foreach ($listProductVariants as $indexKey => $product) {
                            $attributes = [];
                            if ($product) {
                                $attributes =  $odooService->getVariantAttributeNameByValue($product['product_template_attribute_value_ids']);
                                if (count($attributes)) {
                                    $rowData[array_search('Option1 Name', $header)] = $attributes[0]['attribute_id'][1] ?? '';
                                    $rowData[array_search('Option1 Value', $header)] = $attributes[0]['name'] ?? '';
                                    $rowData[array_search('Option2 Name', $header)] = $attributes[1]['attribute_id'][1] ?? '';
                                    $rowData[array_search('Option2 Value', $header)] = $attributes[1]['name'] ?? '';
                                    $rowData[array_search('Option3 Name', $header)] = $attributes[2]['attribute_id'][1] ?? '';
                                    $rowData[array_search('Option3 Value', $header)] = $attributes[2]['name'] ?? '';
                                }
                            }
                            if ($indexKey > 0) {
                                $rowData[array_search('Title', $header)] = '';
                            }
                            $rowData[array_search('Variant Price', $header)] = $product['lst_price'];
                            $rowData[array_search('Image Src', $header)] = $product_variable->children[$indexKey]->link ?? '';
                            $rowData[array_search('Image Position', $header)] =  isset($product_variable->children[$indexKey]) ? ($indexKey + 1) : '';
                            $rowData[array_search('Image Alt Text', $header)] = $product_variable->children[$indexKey]->name ?? '';
                            if (isset($product_variable->children[$indexKey])) {
                                unset($product_variable->children[$indexKey]);
                            }
                            $excelData[] = $rowData;
                        }
                    }

                    if (isset($product_variable->children) && count($product_variable->children)) {
                        $rowData[array_search('Option1 Name', $header)] = '';
                        $rowData[array_search('Option1 Value', $header)] = '';
                        $rowData[array_search('Option2 Name', $header)] = '';
                        $rowData[array_search('Option2 Value', $header)] = '';
                        $rowData[array_search('Option3 Name', $header)] = '';
                        $rowData[array_search('Option3 Value', $header)] = '';

                        foreach ($product_variable->children as $keyPrv => $image) {
                            if ($keyPrv > 0) {
                                $rowData[array_search('Title', $header)] = '';
                            }

                            $rowData[array_search('Image Src', $header)] = $image->link;
                            $rowData[array_search('Image Position', $header)] = ($keyPrv + 1);
                            $rowData[array_search('Image Alt Text', $header)] = $image->name;
                            $excelData[] = $rowData;
                        }
                    }

                    $p++;
                }
            }
        }
        $fileName = "image_links_" . time() . '_v2.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }

    public function create_shopify_csv(Request $request)
    {
        $url = 'https://gtferp.gifttify.com';
        $db = 'gtferp.gifttify.com';
        $username = 'hungnq@gifttify.com';
        $password = 'hunglan123';
        $common = \ripcord::client("$url/xmlrpc/2/common");
        $common->version();
        $uid = $common->authenticate($db, $username, $password, array());

        $models = \ripcord::client("$url/xmlrpc/2/object");
        $records = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(array(array('is_company', '=', true))), array('fields'=>array('name', 'country_id', 'comment'), 'limit'=>5));

        dd($records);





        $link = $request->input('link') ?? '';
        $action = $request->input('action') ?? '';
        $uriArr = parse_url($link);
        $path = $uriArr['path'] ? explode('/', $uriArr['path']) : '';

        $id = '';
        if ($path) {
            $id = isset($path[count($path)-1]) ? $path[count($path)-1] : '';
        }

        $result = array();
        if ($id) {
            //$result = GoogleDriveFiles::flat_image_links_from_folder_id($id);
            $result = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id);
        }
        if ($action == 'download_csv') {
            $this->export_image_links($result);
            return true;
        }
        return view('tools.create_shopify_csv', compact('link', 'result') );
    }
}
