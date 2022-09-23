<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\GoogleDriveFiles;
use App\Models\ImportProductsCsv;
use App\Services\OdooService;
use App\Services\Spreadsheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class ToolsController extends Controller {
    public function create_shopify_csv(Request $request) {
        if ($request->getMethod() == 'POST') {
            $file = $_FILES['json_file']['tmp_name'] ?? '';
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            $spreadsheet_url = Config::get('google.gtf_template_link');

            if ($request->input('action') == 'download_csv_test') {
                $this->export_image_links_local_template_test($data, $spreadsheet_url);
                return true;
            }
            $this->export_image_links_local_template($data, $spreadsheet_url);
            return true;
        }
        return view('tools.create_shopify_csv');
    }

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

        if ($id && $action == 'download_csv_v5') {
            $result_v5 = '';
            $spreadsheet_url = Config::get('google.gtf_template_link');
            $this->export_image_links_local_template($result_v5, $spreadsheet_url);
            return true;
        }

        if ($id && $action == 'download_csv_v4') {
            $result_v4 = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id, 'tree');
            $spreadsheet_url = Config::get('google.gtf_template_link');
            $this->export_image_links_template($result_v4, $spreadsheet_url);
            return true;
        }

        if ($id && $action == 'download_csv_v3') {
            $result_v3 = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id, 'tree');
            $spreadsheet_url = Config::get('google.hiep_template_link');
            $this->export_image_links_template($result_v3, $spreadsheet_url, true);
            return true;
        }

        if ($id && $action == 'download_csv_v2') {
            $result_v2 = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id, 'tree');
            $this->export_image_links_v2($result_v2);
            return true;
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
        $fileName = "image_links_" . time() . '.csv';
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
        $productTypeDesc = $odooService->getProductTypeInfo() ?? '';

        // Set default falue
        $rowData[array_search('Variant Inventory Policy', $header)] =  'deny';
        $rowData[array_search('Variant Fulfillment Service', $header)] =  'manual';
        $rowData[array_search('Variant Requires Shipping', $header)] =  'TRUE';
        $rowData[array_search('Variant Taxable', $header)] = 'FALSE';
        $rowData[array_search('Variant Grams', $header)] = 500;
        $rowData[array_search('Variant Inventory Qty', $header)] = '0';
        $rowData[array_search('Status', $header)] = 'active';


        foreach ($data as $key => $dateData) {
            foreach ($dateData->children as $product) {
                foreach ($product->children as $product_variable) {
                    $tags = [];

                    $namePTypes = explode(' @NamePType ', $product_variable->name);
                    if (count($namePTypes) < 2 ) {
                        dd("Vui lòng kiểm tra lại cấu trúc và cách đặt tên file.");
                    }
                    $tCodes = explode('! ', str_replace(['(', ')'], '', $namePTypes[2]));
                    $codePType = str_replace(['t^ptype_', '@CodePType^'], '', trim($tCodes[2]));
                    $typeName = isset($productTypeTable[$codePType]) ? $productTypeTable[$codePType]->product_type_name : $codePType;
                    //get template
                    $productTempate = $odooService->getProductByProductType($codePType);

                    $listProductVariants = [];
                    if (count($productTempate) && isset($productTempate['id'])) {
                        // get Variant by template ID (Or get variant by codeType)
                        $listProductVariants = $odooService->getProductVariantByTemplateId($productTempate['id']);
                    }

                    $bodyHtml = collect($productTypeDesc)->first(function ($item) use ($codePType) {
                        return $item['display_name'] == $codePType;
                    });

                    $title = $namePTypes[0] . ' ' .  $typeName . ' ' . $namePTypes[1];
                    $colection = str_replace(['t^collection'], 'collection', trim($tCodes[3]));
                    $tags[] = str_replace(['t^name_'], '', trim($tCodes[1]));
                    $tags[] = $typeName;
                    $tags[] = $colection;

                    $rowData[array_search('Vendor', $header)] = str_replace(['t^code_', '@CodePType^', ' '], '', trim($tCodes[0]));
                    $rowData[array_search('Tags', $header)] = implode(', ', $tags);
                    $rowData[array_search('Title', $header)] =  $title;
                    $rowData[array_search('SEO Title', $header)] =  $title;
                    $rowData[array_search('Type', $header)] =  $codePType;
                    $rowData[array_search('Handle', $header)] =  strtolower(str_replace([',',')','('], '', str_replace([' '], '_', $title))) . '_' . Carbon::now()->format('dmy') . '_p' . $p;
                    $rowData[array_search('Body (HTML)', $header)] = $bodyHtml['x_studio_description_html'] ?? '';
                    $rowData[array_search('Published', $header)] =  'TRUE';
                    $rowData[array_search('Gift Card', $header)] = 'FALSE';
                    $rowData[array_search('Variant Weight Unit', $header)] = $productTempate['weight_uom_name'] ?? 'kg';

                    if (isset($listProductVariants)) {
                        foreach ($listProductVariants as $indexKey => $product) {
                            $attributes = [];
                            if (count($product)) {
                                //get Variant option
                                $attributes =  $odooService->getVariantAttributeNameByAttrIds($product['product_template_attribute_value_ids']);
                                if (count($attributes)) {
                                    $rowData[array_search('Option1 Name', $header)] = $attributes[0]['attribute_id'][1] ?? '';
                                    $rowData[array_search('Option1 Value', $header)] = $attributes[0]['name'] ?? '';
                                    $rowData[array_search('Option2 Name', $header)] = $attributes[1]['attribute_id'][1] ?? '';
                                    $rowData[array_search('Option2 Value', $header)] = $attributes[1]['name'] ?? '';
                                    $rowData[array_search('Option3 Name', $header)] = $attributes[2]['attribute_id'][1] ?? '';
                                    $rowData[array_search('Option3 Value', $header)] = $attributes[2]['name'] ?? '';
                                }
                                $rowData[array_search('Variant Price', $header)] = (($product['x_studio_custom_price']) > 0) ? $product['x_studio_custom_price'] : $product['lst_price'];
                                $rowData[array_search('Variant Compare At Price', $header)] = $product['lst_price'];
                            }
                            if ($indexKey > 0) {
                                $rowData[array_search('Title', $header)] = '';
                                $rowData[array_search('Option1 Name', $header)] = '';
                                $rowData[array_search('Option2 Name', $header)] = '';
                                $rowData[array_search('Option3 Name', $header)] = '';
                                $rowData[array_search('SEO Title', $header)] =  '';
                                $rowData[array_search('Published', $header)] =  '';
                                $rowData[array_search('Gift Card', $header)] = '';
                                $rowData[array_search('Vendor', $header)] = '';
                                $rowData[array_search('Type', $header)] =  '';
                                $rowData[array_search('Tags', $header)] =  '';
                            }

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
                        $rowData[array_search('Variant Price', $header)] = $productTempate['lst_price'] ?? 0;
                        $rowData[array_search('Variant Compare At Price', $header)] = $productTempate['lst_price'] ?? 0;

                        foreach ($product_variable->children as $keyPrv => $image) {
                            if ($keyPrv > 0) {
                                $rowData[array_search('Title', $header)] = '';
                                $rowData[array_search('SEO Title', $header)] =  '';
                                $rowData[array_search('Published', $header)] =  '';
                                $rowData[array_search('Gift Card', $header)] = '';
                                $rowData[array_search('Vendor', $header)] = '';
                                $rowData[array_search('Type', $header)] =  '';
                                $rowData[array_search('Tags', $header)] =  '';
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
        $fileName = "image_links_" . time() . '_v2.csv';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }


    protected function export_image_links_template($data, $spreadsheet_url, $ignoreCheckDb = false)
    {
        $csvData = GoogleDriveFiles::getGoogleDriveCsvFile($spreadsheet_url);
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

        $productTypeTable = array();
        if ($ignoreCheckDb == false) {
            $productTypeTable = DB::table('product_type')->get()->keyBy('folder_mk')->toArray();
        }

        $rowData = [];
        $numberValue = count($header);
        for ($i = 0; $i < $numberValue; $i++) {
            $rowData[$i] = '';
        }

        $excelData = [];
        $excelData[] = $header;
        $p = 1;

        $checkKeyArr = array();
        foreach ($data as $key => $dateData) {
            foreach ($dateData->children as $dbProduct) {
                if (!isset($dbProduct->children)) continue;
                foreach ($dbProduct->children as $product_variable) {
                    $tags = [];

                    if (str_contains($product_variable->name, '@NamePType')) { //Rule cu
                        $strIncTitle = $product_variable->name;
                    } else { // rule moi
                        $strIncTitle = $dateData->name;
                        if (str_contains($strIncTitle, '^')) {
                            $strIncTitle = substr($strIncTitle, 0, strpos($strIncTitle, '^'));
                        }
                        $strIncTitle = str_ireplace('@FileName', $product_variable->name, $strIncTitle);
                    }
                    $namePTypes = explode('(', $strIncTitle);

                    if ($dateData->name == 'Lovely Halloween @FIleName (tags_collection,name_@FileName)') {
                        //dd($strIncTitle, $namePTypes, substr($dateData->name, 0, strpos($dateData->name, '^')));
                    }

                    $title = $namePTypes[0];
                    $folderMk = $dbProduct->name;

                    $title = str_ireplace(array('@NamePType', '@NamePtype'), $productTypeTable[$folderMk]->product_type_name ?? $folderMk, $title);
                    $codePType = $productTypeTable[$folderMk]->product_type_name ?? $folderMk;
                    $productTypeCode = $productTypeTable[$folderMk]->product_type_code ?? '';

                    $vendor = $sku = "";
                    if(isset($namePTypes[1])) {
                        $namePTypes[1] = str_replace(['(', ')'], '', $namePTypes[1]);
                        preg_match('/vendor_([^!]+)/', $namePTypes[1], $attData);
                        $vendor = isset($attData[1]) ? trim($attData[1]) : '';
                        $vendor = str_ireplace(array('@PCode'), $productTypeCode, $vendor);
                        $vendor = strtoupper($vendor);

                        $fName = strtoupper($product_variable->name);
                        $fName = str_replace(array(' ', '"', "'"),'', $fName);
                        $sku = $vendor . "-$fName";
                        if (isset($checkKeyArr[$sku])) {
                            $checkKeyArr[$sku] = $checkKeyArr[$sku] + 1;
                            $sku = $vendor . "-" . $checkKeyArr[$sku];
                        } else {
                            $checkKeyArr[$sku] = 1;
                        }

                        $attData = null;
                        preg_match('/tags_([^!]+)/', $namePTypes[1], $attData);
                        if(isset($attData[1])) {
                            $tags[] = trim($attData[1]);
                        }
                    }

                    $rowData[array_search('Variant Inventory Policy', $header)] =  'deny';
                    $rowData[array_search('Variant Fulfillment Service', $header)] =  'manual';
                    $rowData[array_search('Variant Requires Shipping', $header)] =  'TRUE';
                    $rowData[array_search('Variant Taxable', $header)] = 'FALSE';
                    $rowData[array_search('Variant Grams', $header)] = 500;
                    $rowData[array_search('Variant Inventory Qty', $header)] = '0';
                    $rowData[array_search('Status', $header)] = 'active';

                    $csvProductTypeData = $csvData[$folderMk] ?? array();
                    $bodyHtml = $csvProductTypeData[0]['Description'] ?? '';

                    $tags[] = $folderMk;

                    $rowData[array_search('Vendor', $header)] = $vendor;
                    $rowData[array_search('Tags', $header)] = implode(', ', $tags);
                    $rowData[array_search('Title', $header)] =  $title;
                    $rowData[array_search('SEO Title', $header)] =  $title;
                    $rowData[array_search('Type', $header)] =  $codePType;
                    $rowData[array_search('Handle', $header)] =  strtolower(str_replace([',',')','('], '', str_replace([' '], '_', $title))) . '_' . Carbon::now()->format('dmy') . '_p' . $p;
                    $rowData[array_search('Body (HTML)', $header)] = $bodyHtml;
                    $rowData[array_search('Published', $header)] =  'TRUE';
                    $rowData[array_search('Gift Card', $header)] = 'FALSE';
                    $rowData[array_search('Variant Weight Unit', $header)] = $productTempate['weight_uom_name'] ?? 'kg';

                    if (isset($csvProductTypeData)) {
                        foreach ($csvProductTypeData as $indexKey => $product) {
                            if (count($product)) {
                                $option1 = strtoupper($product['Option1 Value'] != 'Default Title' ? $product['Option1 Value'] : '');
                                $option1 = str_replace(array(' ', '"', "'"),'', $option1);
                                $option1 = $option1 != '' ? '-' . $option1 : '';

                                $option2 = strtoupper($product['Option2 Value'] != 'Default Title' ? $product['Option2 Value'] : '');
                                $option2 = str_replace(array(' ', '"', "'"),'', $option2);
                                $option2 = $option2 != '' ? '-' . $option2 : '';

                                $option3 = strtoupper($product['Option3 Value'] != 'Default Title' ? $product['Option3 Value'] : '');
                                $option3 = str_replace(array(' ', '"', "'"),'', $option3);
                                $option3 = $option3 != '' ? '-' . $option3 : '';

                                $varSku = $sku != '' ? $sku . $option1 . $option2 . $option3 : '';

                                $rowData[array_search('Option1 Name', $header)] = $product['Option1 Name'] ?? '';
                                $rowData[array_search('Option1 Value', $header)] = $product['Option1 Value'] ?? '';
                                $rowData[array_search('Option2 Name', $header)] = $product['Option2 Name'] ?? '';
                                $rowData[array_search('Option2 Value', $header)] = $product['Option2 Value'] ?? '';
                                $rowData[array_search('Option3 Name', $header)] = $product['Option3 Name'] ?? '';
                                $rowData[array_search('Option3 Value', $header)] = $product['Option3 Value'] ?? '';
                                $rowData[array_search('Variant Price', $header)] = $product['Price'] ?? '';
                                $rowData[array_search('Variant Compare At Price', $header)] = $product['Compare Price'] ?? '';
                                $rowData[array_search('Variant SKU', $header)] = $varSku;
                            }
                            if ($indexKey > 0) {
                                $rowData[array_search('Title', $header)] = '';
                                $rowData[array_search('Option1 Name', $header)] = '';
                                $rowData[array_search('Option2 Name', $header)] = '';
                                $rowData[array_search('Option3 Name', $header)] = '';
                                $rowData[array_search('SEO Title', $header)] =  '';
                                $rowData[array_search('Published', $header)] =  '';
                                $rowData[array_search('Gift Card', $header)] = '';
                                $rowData[array_search('Vendor', $header)] = '';
                                $rowData[array_search('Type', $header)] =  '';
                                $rowData[array_search('Tags', $header)] =  '';
                                $rowData[array_search('Body (HTML)', $header)] = '';
                            }

                            $rowData[array_search('Image Src', $header)] = $product_variable->children[$indexKey]->link ?? '';
                            $rowData[array_search('Image Position', $header)] =  isset($product_variable->children[$indexKey]) ? ($indexKey + 1) : '';
                            $rowData[array_search('Image Alt Text', $header)] = '';
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
                        $rowData[array_search('Variant Price', $header)] = '';//$csvProductTypeData[0]['Price'] ?? 0;
                        $rowData[array_search('Variant Compare At Price', $header)] = '';//$csvProductTypeData[0]['Compare Price'] ?? 0;
                        $rowData[array_search('Variant SKU', $header)] = '';
                        $rowData[array_search('Body (HTML)', $header)] = '';

                        $rowData[array_search('Variant Inventory Policy', $header)] = '';
                        $rowData[array_search('Variant Fulfillment Service', $header)] = '';
                        $rowData[array_search('Variant Requires Shipping', $header)] = '';
                        $rowData[array_search('Variant Taxable', $header)] = '';
                        $rowData[array_search('Variant Grams', $header)] = '';
                        $rowData[array_search('Variant Inventory Qty', $header)] = '';
                        $rowData[array_search('Status', $header)] = '';
                        $rowData[array_search('Variant Weight Unit', $header)] = '';

                        foreach ($product_variable->children as $keyPrv => $image) {
                            if ($keyPrv > 0) {
                                $rowData[array_search('Title', $header)] = '';
                                $rowData[array_search('SEO Title', $header)] =  '';
                                $rowData[array_search('Published', $header)] =  '';
                                $rowData[array_search('Gift Card', $header)] = '';
                                $rowData[array_search('Vendor', $header)] = '';
                                $rowData[array_search('Type', $header)] =  '';
                                $rowData[array_search('Tags', $header)] =  '';
                            }

                            $rowData[array_search('Image Src', $header)] = $image->link;
                            $rowData[array_search('Image Position', $header)] = ($keyPrv + 1);
                            $rowData[array_search('Image Alt Text', $header)] = '';
                            $excelData[] = $rowData;
                        }
                    }

                    $p++;
                }
            }
        }
        $fileName = "image_links_" . time() . '_v2.csv';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }

    protected function export_image_links_local_template_test($data, $spreadsheet_url, $ignoreCheckDb = false)
    {
        dd('Test_v5');
    }

    protected function export_image_links_local_template($data, $spreadsheet_url, $ignoreCheckDb = false)
    {
        $csvData = GoogleDriveFiles::getGoogleDriveCsvFile($spreadsheet_url);
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

        $productTypeTable = array();
        if ($ignoreCheckDb == false) {
            $productTypeTable = DB::table('product_type')->get()->keyBy('folder_mk')->toArray();
        }

        $rowData = [];
        $numberValue = count($header);
        for ($i = 0; $i < $numberValue; $i++) {
            $rowData[$i] = '';
        }

        $excelData = [];
        $excelData[] = $header;
        $p = 1;

        $checkKeyArr = array();
        $dateData = $data;
        $folderS3 = $dateData['folderS3'];
        $colorArr = $dateData['color'] ?? array();

        foreach ($dateData['pType'] as $pType => $dbProduct) {
            foreach ($dbProduct as $fileNameP => $product_variable) {
                $csvProductTypeData = $csvData[$pType] ?? array();
                if (count($csvProductTypeData) > 0) {
                    $updatedCsvProductTypeData = $csvProductTypeData;
                    foreach ($csvProductTypeData as $kCsv => $vCsv) {
                        if (isset($colorArr[$pType][$fileNameP])) {
                            foreach ($colorArr[$pType][$fileNameP] as $kColor => $color) {
                                if ($kColor == 0) {
                                    if ($csvProductTypeData[$kCsv]['Option1 Name'] == 'Title' || $csvProductTypeData[$kCsv]['Option1 Name'] == '') {
                                        $updatedCsvProductTypeData[$kCsv]['Option1 Name'] = 'Color';
                                        $updatedCsvProductTypeData[$kCsv]['Option1 Value'] = $color;
                                    } elseif ($csvProductTypeData[$kCsv]['Option2 Name'] == '') {
                                        $updatedCsvProductTypeData[$kCsv]['Option2 Name'] = 'Color';
                                        $updatedCsvProductTypeData[$kCsv]['Option2 Value'] = $color;
                                    } elseif ($csvProductTypeData[$kCsv]['Option3 Name'] == '') {
                                        $updatedCsvProductTypeData[$kCsv]['Option3 Name'] = 'Color';
                                        $updatedCsvProductTypeData[$kCsv]['Option3 Value'] = $color;
                                    }
                                    $updatedCsvProductTypeData[$kCsv]['Variant Image'] = $color;
                                    foreach ($product_variable as $imgVariable) {
                                        if (str_contains($imgVariable, $color)) {
                                            $updatedCsvProductTypeData[$kCsv]['Variant Image'] = $imgVariable;
                                            break;
                                        }
                                    }
                                } else {
                                    $newValue = $csvProductTypeData[$kCsv];
                                    if ($newValue['Option1 Name'] == 'Title' || $newValue['Option1 Name'] == '') {
                                        $newValue['Option1 Name'] = 'Color';
                                        $newValue['Option1 Value'] = $color;
                                    } elseif ($newValue['Option2 Name'] == '') {
                                        $newValue['Option2 Name'] = 'Color';
                                        $newValue['Option2 Value'] = $color;
                                    } elseif ($newValue['Option3 Name'] == '') {
                                        $newValue['Option3 Name'] = 'Color';
                                        $newValue['Option3 Value'] = $color;
                                    }
                                    $newValue['Variant Image'] = $color;
                                    foreach ($product_variable as $imgVariable) {
                                        if (str_contains($imgVariable, $color)) {
                                            $newValue['Variant Image'] = $imgVariable;
                                            break;
                                        }
                                    }
                                    $updatedCsvProductTypeData[] = $newValue;
                                }
                            }
                        }
                    }

                }
            }
            if (!empty($csvProductTypeData)) {
                $csvData[$pType] = $updatedCsvProductTypeData;
            }
        }

        foreach ($dateData['pType'] as $pType => $dbProduct) {
            foreach ($dbProduct as $fileNameP => $product_variable) {
                $tags = [];

                $strIncTitle = $dateData['name'];
                if (str_contains($strIncTitle, '^')) {
                    $strIncTitle = substr($strIncTitle, 0, strpos($strIncTitle, '^'));
                }
                $strIncTitle = str_ireplace('@FileName', $fileNameP, $strIncTitle);
                $namePTypes = explode('(', $strIncTitle);

                $title = $namePTypes[0];
                $folderMk = $pType;

                $title = str_ireplace(array('@NamePType', '@NamePtype'), $productTypeTable[$folderMk]->product_type_name ?? $folderMk, $title);
                $codePType = $productTypeTable[$folderMk]->product_type_name ?? $folderMk;
                $productTypeCode = $productTypeTable[$folderMk]->product_type_code ?? '';

                $vendor = $sku = "";
                if(isset($namePTypes[1])) {
                    $namePTypes[1] = str_replace(['(', ')'], '', $namePTypes[1]);
                    preg_match('/vendor_([^!]+)/', $namePTypes[1], $attData);
                    $vendor = isset($attData[1]) ? trim($attData[1]) : '';
                    $vendor = str_ireplace(array('@PCode'), $productTypeCode, $vendor);
                    $vendor = strtoupper($vendor);

                    $fName = strtoupper($fileNameP);
                    $fName = str_replace(array(' ', '"', "'"),'', $fName);
                    if ($vendor) {
                        $sku = $vendor . "-$fName";
                        if (isset($checkKeyArr[$sku])) {
                            $checkKeyArr[$sku] = $checkKeyArr[$sku] + 1;
                            $sku = $vendor . "-" . $checkKeyArr[$sku];
                        } else {
                            $checkKeyArr[$sku] = 1;
                        }
                    }

                    $attData = null;
                    preg_match('/tags_([^!]+)/', $namePTypes[1], $attData);
                    if(isset($attData[1])) {
                        $tags[] = trim($attData[1]);
                    }
                }

                $rowData[array_search('Variant Inventory Policy', $header)] =  'deny';
                $rowData[array_search('Variant Fulfillment Service', $header)] =  'manual';
                $rowData[array_search('Variant Requires Shipping', $header)] =  'TRUE';
                $rowData[array_search('Variant Taxable', $header)] = 'FALSE';
                $rowData[array_search('Variant Grams', $header)] = 500;
                $rowData[array_search('Variant Inventory Qty', $header)] = '0';
                $rowData[array_search('Status', $header)] = 'active';
                $rowData[array_search('Variant Image', $header)] = '';

                $csvProductTypeData = $csvData[$folderMk] ?? array();

                $bodyHtml = $csvProductTypeData[0]['Description'] ?? '';

                $tags[] = $folderMk;

                $rowData[array_search('Vendor', $header)] = $vendor;
                $rowData[array_search('Tags', $header)] = implode(', ', $tags);
                $rowData[array_search('Title', $header)] =  $title;
                $rowData[array_search('SEO Title', $header)] =  $title;
                $rowData[array_search('Type', $header)] =  $codePType;
                $rowData[array_search('Handle', $header)] =  strtolower(str_replace([',',')','('], '', str_replace([' '], '_', $title))) . '_' . Carbon::now()->format('dmy') . '_p' . $p;
                $rowData[array_search('Body (HTML)', $header)] = $bodyHtml;
                $rowData[array_search('Published', $header)] =  'TRUE';
                $rowData[array_search('Gift Card', $header)] = 'FALSE';
                $rowData[array_search('Variant Weight Unit', $header)] = $productTempate['weight_uom_name'] ?? 'kg';

                if (isset($csvProductTypeData)) {
                    foreach ($csvProductTypeData as $indexKey => $product) {
                        if (count($product)) {
                            $option1 = strtoupper($product['Option1 Value'] != 'Default Title' ? $product['Option1 Value'] : '');
                            $option1 = str_replace(array(' ', '"', "'"),'', $option1);
                            $option1 = $option1 != '' ? '-' . $option1 : '';

                            $option2 = strtoupper($product['Option2 Value'] != 'Default Title' ? $product['Option2 Value'] : '');
                            $option2 = str_replace(array(' ', '"', "'"),'', $option2);
                            $option2 = $option2 != '' ? '-' . $option2 : '';

                            $option3 = strtoupper($product['Option3 Value'] != 'Default Title' ? $product['Option3 Value'] : '');
                            $option3 = str_replace(array(' ', '"', "'"),'', $option3);
                            $option3 = $option3 != '' ? '-' . $option3 : '';

                            $varSku = $sku != '' ? $sku . $option1 . $option2 . $option3 : '';

                            $rowData[array_search('Option1 Name', $header)] = $product['Option1 Name'] ?? '';
                            $rowData[array_search('Option1 Value', $header)] = $product['Option1 Value'] ?? '';
                            $rowData[array_search('Option2 Name', $header)] = $product['Option2 Name'] ?? '';
                            $rowData[array_search('Option2 Value', $header)] = $product['Option2 Value'] ?? '';
                            $rowData[array_search('Option3 Name', $header)] = $product['Option3 Name'] ?? '';
                            $rowData[array_search('Option3 Value', $header)] = $product['Option3 Value'] ?? '';
                            $rowData[array_search('Variant Price', $header)] = $product['Price'] ?? '';
                            $rowData[array_search('Variant Compare At Price', $header)] = $product['Compare Price'] ?? '';
                            $rowData[array_search('Variant SKU', $header)] = $varSku;
                            $rowData[array_search('Variant Image', $header)] = isset($product['Variant Image']) ? "{$folderS3}{$product['Variant Image']}" : '';;
                        }
                        if ($indexKey > 0) {
                            $rowData[array_search('Title', $header)] = '';
                            $rowData[array_search('Option1 Name', $header)] = '';
                            $rowData[array_search('Option2 Name', $header)] = '';
                            $rowData[array_search('Option3 Name', $header)] = '';
                            $rowData[array_search('SEO Title', $header)] =  '';
                            $rowData[array_search('Published', $header)] =  '';
                            $rowData[array_search('Gift Card', $header)] = '';
                            $rowData[array_search('Vendor', $header)] = '';
                            $rowData[array_search('Type', $header)] =  '';
                            $rowData[array_search('Tags', $header)] =  '';
                            $rowData[array_search('Body (HTML)', $header)] = '';
                        }

                        $rowData[array_search('Image Src', $header)] = isset($product_variable[$indexKey]) ? "{$folderS3}{$product_variable[$indexKey]}" : '';
                        $rowData[array_search('Image Position', $header)] =  isset($product_variable[$indexKey]) ? ($indexKey + 1) : '';
                        $rowData[array_search('Image Alt Text', $header)] = '';
                        if (isset($product_variable[$indexKey])) {
                            unset($product_variable[$indexKey]);
                        }
                        $excelData[] = $rowData;
                    }
                }

                if (isset($product_variable) && count($product_variable)) {
                    $rowData[array_search('Option1 Name', $header)] = '';
                    $rowData[array_search('Option1 Value', $header)] = '';
                    $rowData[array_search('Option2 Name', $header)] = '';
                    $rowData[array_search('Option2 Value', $header)] = '';
                    $rowData[array_search('Option3 Name', $header)] = '';
                    $rowData[array_search('Option3 Value', $header)] = '';
                    $rowData[array_search('Variant Price', $header)] = '';//$csvProductTypeData[0]['Price'] ?? 0;
                    $rowData[array_search('Variant Compare At Price', $header)] = '';//$csvProductTypeData[0]['Compare Price'] ?? 0;
                    $rowData[array_search('Variant SKU', $header)] = '';
                    $rowData[array_search('Variant Image', $header)] = '';
                    $rowData[array_search('Body (HTML)', $header)] = '';

                    $rowData[array_search('Variant Inventory Policy', $header)] = '';
                    $rowData[array_search('Variant Fulfillment Service', $header)] = '';
                    $rowData[array_search('Variant Requires Shipping', $header)] = '';
                    $rowData[array_search('Variant Taxable', $header)] = '';
                    $rowData[array_search('Variant Grams', $header)] = '';
                    $rowData[array_search('Variant Inventory Qty', $header)] = '';
                    $rowData[array_search('Status', $header)] = '';
                    $rowData[array_search('Variant Weight Unit', $header)] = '';

                    foreach ($product_variable as $keyPrv => $image) {
                        if ($keyPrv > 0) {
                            $rowData[array_search('Title', $header)] = '';
                            $rowData[array_search('SEO Title', $header)] =  '';
                            $rowData[array_search('Published', $header)] =  '';
                            $rowData[array_search('Gift Card', $header)] = '';
                            $rowData[array_search('Vendor', $header)] = '';
                            $rowData[array_search('Type', $header)] =  '';
                            $rowData[array_search('Tags', $header)] =  '';
                        }

                        $rowData[array_search('Image Src', $header)] = $image ? "{$folderS3}{$image}" : '';
                        $rowData[array_search('Image Position', $header)] = ($keyPrv + 1);
                        $rowData[array_search('Image Alt Text', $header)] = '';
                        $excelData[] = $rowData;
                    }
                }

                $p++;
            }
        }
        $fileName = "image_links_" . time() . '_v5.csv';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }

    public function upload_products_csv(Request $request) {
        return view('tools.upload_products_csv' );
    }

    public function post_products_csv(Request $request) {
        $csvFile = $_FILES['csv_file']['tmp_name'];

        $csvData = GoogleDriveFiles::getGoogleDriveCsvFile($csvFile);
        $products = array();

        $productTypeTable = array();
        $productTypeTable = DB::table('product_type')->get()->keyBy('product_type_name')->toArray();

        foreach ($csvData as $k => $variants) {
            $id = uniqid('g_', true) . rand(1000,9999);

            $prod = $variants[0];
            if (count($prod) != 48) {
                //dump($prod);
                continue;
            }

            $products[$k] = array(
                'shopifyId' => $id,
                'slug' => $prod['Handle'],
                'title' => $prod['Title'],
                'productType' => $productTypeTable[$prod['Type']]->product_type_code ?? $prod['Type'],
                'status' => 'publish',
                'tags' => $prod['Tags'],
                'tagsArr' => array_map('trim', explode(',', $prod['Tags'])),
                'images' => array(),
                'options' => array(),
                'variants' => array(),
                'seo' => array(
                    'title' => $prod['Title'],
                    'description' => $prod['Title']
                )
            );

            $imagesArr = $option1Arr = $option2Arr = $option3Arr = $var_arr = array();
            foreach ($variants as $variant) {
                $img = $variant['Image Src'] ?? '';
                if ($img) {
                    $imagesArr[] = array(
                        'src' => $img,
                        'alt' => '',
                    );
                }

                if ($variant['Variant Price'] > 0) {
                    $varId = uniqid('gv_', true) . rand(1000,9999);
                    $varValue = array(
                        'id' => $varId,
                        'sku' => $variant['Variant SKU'],
                        'quantity' => 9999,
                        'price' => $variant['Variant Price'],
                        'option1' => $variant['Option1 Value'] != 'Default Title' ? $variant['Option1 Value'] : '',
                        'option2' => $variant['Option2 Value'] != 'Default Title' ? $variant['Option2 Value'] : '',
                        'option3' => $variant['Option3 Value'] != 'Default Title' ? $variant['Option3 Value'] : '',
                        'image' => array(
                            'src' => $variant['Variant Image'] != '' ? $variant['Variant Image'] : $prod['Image Src'] ?? '',
                            'alt' => '',
                            'position' => 1
                        ),
                    );
                    if ($variant['Variant Compare At Price']) {
                        $varValue['compareAtPrice'] = $variant['Variant Compare At Price'];
                    }
                    $var_arr[] = $varValue;
                }

                $opt1 = $variant['Option1 Value'];
                if ($opt1) {
                    $option1Arr[$opt1] = $opt1;
                }

                $opt2 = $variant['Option2 Value'];
                if ($opt2) {
                    $option2Arr[$opt2] = $opt2;
                }

                $opt3 = $variant['Option3 Value'];
                if ($opt3) {
                    $option3Arr[$opt3] = $opt3;
                }
            }
            $products[$k]['images'] = $imagesArr;
            $products[$k]['variants'] = $var_arr;

            if ($prod['Option1 Name'] != 'Title' && $prod['Option1 Name'] != '' && !empty($option1Arr)) {
                $products[$k]['options'][] = array(
                    'name' => $prod['Option1 Name'],
                    'type' => '',
                    'values' => array_values($option1Arr),
                );
            }

            if ($prod['Option2 Name'] != 'Title' && $prod['Option2 Name'] != '' && !empty($option2Arr)) {
                $products[$k]['options'][] = array(
                    'name' => $prod['Option2 Name'],
                    'type' => '',
                    'values' => array_values($option2Arr),
                );
            }

            if ($prod['Option3 Name'] != 'Title' && $prod['Option3 Name'] != '' && !empty($option3Arr)) {
                $products[$k]['options'][] = array(
                    'name' => $prod['Option3 Name'],
                    'type' => '',
                    'values' => array_values($option3Arr),
                );
            }
        }

        foreach ($products as $v) {
            ImportProductsCsv::insertOrIgnore([
                'slug' => $v['slug'] ?? '',
                'shopifyId' => $v['shopifyId'] ?? '',
                'title' => $v['title'] ?? '',
                'productType' => $v['productType'] ?? '',
                'status' => $v['status'] ?? '',
                'tags' => $v['tags'] ?? '',
                'tagsArr' => json_encode($v['tagsArr'] ?? '') ?? '',
                'images' => json_encode($v['images'] ?? '') ?? '',
                'options' => json_encode($v['options'] ?? '') ?? '',
                'variants' => json_encode($v['variants'] ?? '') ?? '',
                'seo' => json_encode($v['seo'] ?? '') ?? '',
                'syncedStatus' => $v['syncedStatus'] ?? 0,
                'syncedImage' => $v['syncedImage'] ?? 0,
            ]);
        }

        Artisan::queue('products_csv:import');
        return redirect('/upload_products_csv')->with('status', 'Uploaded products! Please check after 10 minutes!');

    }
}
