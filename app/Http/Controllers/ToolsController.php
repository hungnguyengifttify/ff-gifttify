<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

        if ($id && $action == 'download_csv_v4') {
            $result_v4 = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id, 'tree');
            $spreadsheet_url = Config::get('google.gtf_template_link');
            $this->export_image_links_template($result_v4, $spreadsheet_url);
            return true;
        }

        if ($id && $action == 'download_csv_v3') {
            $result_v3 = GoogleDriveFiles::flat_image_links_from_folder_id_by_mysql_query($id, 'tree');
            $spreadsheet_url = Config::get('google.hiep_template_link');
            $this->export_image_links_template($result_v3, $spreadsheet_url);
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
        $fileName = "image_links_" . time() . '_v2.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }


    protected function export_image_links_template($data, $spreadsheet_url)
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

        $productTypeTable = DB::table('product_type')->get()->keyBy('folder_mk')->toArray();
        $rowData = [];
        $numberValue = count($header);
        for ($i = 0; $i < $numberValue; $i++) {
            $rowData[$i] = '';
        }

        $excelData = [];
        $excelData[] = $header;
        $p = 1;

        // Set default falue
        $rowData[array_search('Variant Inventory Policy', $header)] =  'deny';
        $rowData[array_search('Variant Fulfillment Service', $header)] =  'manual';
        $rowData[array_search('Variant Requires Shipping', $header)] =  'TRUE';
        $rowData[array_search('Variant Taxable', $header)] = 'FALSE';
        $rowData[array_search('Variant Grams', $header)] = 500;
        $rowData[array_search('Variant Inventory Qty', $header)] = '0';
        $rowData[array_search('Status', $header)] = 'active';


        foreach ($data as $key => $dateData) {
            foreach ($dateData->children as $dbProduct) {
                foreach ($dbProduct->children as $product_variable) {
                    $tags = [];
                    $namePTypes = explode('(', $product_variable->name);

                    $title = $namePTypes[0];
                    $folderMk = $dbProduct->name;

                    $title = str_replace('@NamePType', $productTypeTable[$folderMk]->product_type_name??'', $title);
                    $codePType = $productTypeTable[$folderMk]->product_type_code??'';

                    if(isset($namePTypes[1])) {
                        $namePTypes[1] = str_replace(['(', ')'], '', $namePTypes[1]);
                        preg_match('/vendor_([^!]+)/', $namePTypes[1], $attData);
                        $vendor = isset($attData[1]) ? trim($attData[1]) : '';

                        $attData = null;
                        preg_match('/sku_([^!]+)/', $namePTypes[1], $attData);
                        $sku = isset($attData[1]) ? trim($attData[1]) : '';

                        $attData = null;
                        preg_match('/tags_([^!]+)/', $namePTypes[1], $attData);
                        if(isset($attData[1])) {
                            $tags[] = trim($attData[1]);
                        }
                    }

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
                                $rowData[array_search('Option1 Name', $header)] = $product['Option1 Name'] ?? '';
                                $rowData[array_search('Option1 Value', $header)] = $product['Option1 Value'] ?? '';
                                $rowData[array_search('Option2 Name', $header)] = $product['Option2 Name'] ?? '';
                                $rowData[array_search('Option2 Value', $header)] = $product['Option2 Value'] ?? '';
                                $rowData[array_search('Option3 Name', $header)] = $product['Option3 Name'] ?? '';
                                $rowData[array_search('Option3 Value', $header)] = $product['Option3 Value'] ?? '';
                                $rowData[array_search('Variant Price', $header)] = $product['Price'] ?? '';
                                $rowData[array_search('Variant Compare At Price', $header)] = $product['Price'] ?? '';
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
                        $rowData[array_search('Variant Price', $header)] = $csvProductTypeData[0]['Price'] ?? 0;
                        $rowData[array_search('Variant Compare At Price', $header)] = $csvProductTypeData[0]['Price'] ?? 0;
                        $rowData[array_search('Body (HTML)', $header)] = '';


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
        $fileName = "image_links_" . time() . '_v2.xls';
        return Spreadsheet::exportFromArray($excelData, $fileName);
    }


    public function create_shopify_csv(Request $request)
    {
        $spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vR4zFgf2v83U8DmJO-p8P8xxdkW2z_fi0DPjZBxton_eUhWg-kUgEImGSUYY_YtH1ldcwyn93eISrSe/pub?gid=0&single=true&output=csv";
        $data = GoogleDriveFiles::getGoogleDriveCsvFile($spreadsheet_url);
        dd($data);
    }
}
