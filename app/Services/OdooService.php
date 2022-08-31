<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use ripcord;

class OdooService
{
    const CACHE_TIME = 60 * 3600;
    private $db;
    private $username;
    private $password;
    private $url;
    private $uid;

    function __construct()
    {
        $this->db = Config::get('odoo.db');
        $this->username = Config::get('odoo.username');
        $this->password = Config::get('odoo.password');
        $this->url = Config::get('odoo.url');

        $common = ripcord::client($this->url . "/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, array());
    }

    function getOdooVersion($typeName)
    {
        $common = ripcord::client($this->url . "/xmlrpc/2/common");
        return $common->version();
    }

    function getProductTypeInfo()
    {
        $models = ripcord::client($this->url . "/xmlrpc/2/object");
        return  Cache::remember(
            'product_type_info',
            OdooService::CACHE_TIME,
            function () use ($models) {
                return $models->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'x_producttype',
                    'search_read',
                    [],
                    [
                        'fields' => [ // Comment if get all
                            'display_name',
                            'x_studio_description_html',
                            'x_name'
                        ]
                    ]
                );
            }
        );
    }

    function getProductByProductType($productTypeName)
    {
        $models = ripcord::client($this->url . "/xmlrpc/2/object");
        return Cache::remember(
            'product_by_product_type_' . $productTypeName,
            OdooService::CACHE_TIME,
            function () use ($productTypeName, $models) {
                return $models->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'product.template',
                    'search_read',
                    [
                        [
                            ['x_studio_product_type', '=', $productTypeName]
                        ]
                    ],
                    [
                        'limit' => 1,
                        'fields' => [
                            'id',
                            'name',
                            'list_price',
                            'lst_price',
                            'type',
                            'description',
                            'product_variant_count',
                            'product_variant_ids',
                            'weight_uom_name'
                        ]
                    ]
                )[0] ?? [];
            }
        );
    }

    function getProductVariantByTemplateId($templateId)
    {
        $models = ripcord::client($this->url . "/xmlrpc/2/object");
        return  Cache::remember(
            'product_variant_by_template_id_' . $templateId,
            OdooService::CACHE_TIME,
            function () use ($templateId, $models) {
                return $models->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'product.product',
                    'search_read',
                    [
                        [
                            ['product_tmpl_id', '=', $templateId]
                        ]
                    ],
                    [
                        'limit' => 100,
                        'fields' => [ // Comment if get all
                            "barcode",
                            "name",
                            "product_template_attribute_value_ids",
                            "x_studio_product_root",
                            "x_studio_kit_and_combo",
                            "x_studio_custom_price",
                            "company_id",
                            "lst_price",
                            "standard_price",
                            "categ_id",
                            "type",
                            "price",
                            "qty_available",
                            "virtual_available",
                            "uom_id",
                            "product_tmpl_id",
                            "active",
                        ]
                    ]
                );
            }
        );
    }

    function getProductVariantByProductTypeId($productTypeName)
    {
        $models = ripcord::client($this->url . "/xmlrpc/2/object");
        return  Cache::remember(
            'product_variant_by_product_type_id_' . $productTypeName,
            OdooService::CACHE_TIME,
            function () use ($productTypeName, $models) {
                return $models->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'product.product',
                    'search_read',
                    [
                        [
                            ['x_studio_product_type', '=', $productTypeName]
                        ]
                    ],
                    [
                        'limit' => 100,
                        'fields' => [ // Comment if get all
                            "lst_price",
                            "barcode",
                            "name",
                            "product_template_attribute_value_ids",
                            "x_studio_product_root",
                            "x_studio_kit_and_combo",
                            "x_studio_custom_price",
                            "company_id",
                            "lst_price",
                            "standard_price",
                            "categ_id",
                            "type",
                            "price",
                            "qty_available",
                            "virtual_available",
                            "uom_id",
                            "product_tmpl_id",
                            "active",
                        ]
                    ]
                );
            }
        );
    }

    function getVariantAttributeNameByAttrIds($attributeIds)
    {
        $models = ripcord::client($this->url . "/xmlrpc/2/object");
        return  Cache::remember(
            'variant_attribute_name_by_' . implode('_', $attributeIds),
            OdooService::CACHE_TIME,
            function () use ($attributeIds, $models) {
                return $models->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'product.template.attribute.value',
                    'read',
                    [$attributeIds, ['attribute_id', 'name']]
                );
            }
        );
    }
}
