<?php

class Product extends ProductCore
{

    public $id_schedule;
    public static $definition = array(
        'table' => 'product',
        'primary' => 'id_product',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            // Classic fields
            'id_shop_default' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_manufacturer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_supplier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 32),
            'supplier_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 32),
            'location' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'width' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'height' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'depth' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'quantity_discount' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'ean13' => array('type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13),
            'upc' => array('type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12),
            'cache_is_pack' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'cache_has_attachments' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_virtual' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_schedule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            /* Shop fields */
            'id_category_default' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'on_sale' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'online_only' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'ecotax' => array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'),
            'minimal_quantity' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'price' => array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
            'wholesale_price' => array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'),
            'unity' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'),
            'unit_price_ratio' => array('type' => self::TYPE_FLOAT, 'shop' => true),
            'additional_shipping_cost' => array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'),
            'customizable' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'text_fields' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'uploadable_files' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'redirect_type' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'),
            'id_product_redirected' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'available_for_order' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'available_date' => array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
            'condition' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isGenericName', 'values' => array('new', 'used', 'refurbished'), 'default' => 'new'),
            'show_price' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'indexed' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'visibility' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isProductVisibility', 'values' => array('both', 'catalog', 'search', 'none'), 'default' => 'both'),
            'cache_default_attribute' => array('type' => self::TYPE_INT, 'shop' => true),
            'advanced_stock_management' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
            /* Lang fields */
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description_short' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'available_now' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'available_later' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'IsGenericName', 'size' => 255),
        ),
        'associations' => array(
            'manufacturer' => array('type' => self::HAS_ONE),
            'supplier' => array('type' => self::HAS_ONE),
            'default_category' => array('type' => self::HAS_ONE, 'field' => 'id_category_default', 'object' => 'Category'),
            'tax_rules_group' => array('type' => self::HAS_ONE),
            'categories' => array('type' => self::HAS_MANY, 'field' => 'id_category', 'object' => 'Category', 'association' => 'category_product'),
            'stock_availables' => array('type' => self::HAS_MANY, 'field' => 'id_stock_available', 'object' => 'StockAvailable', 'association' => 'stock_availables'),
        ),
    );

    public static function findProductIdByScheduleId($id_schedule)
    {
        $sql = sprintf('SELECT id_product FROM %s WHERE id_schedule = %d', _DB_PREFIX_ . 'product', (int) $id_schedule);
        return Db::getInstance()->getValue($sql);
    }

    public static function getScheduleId($id_product)
    {
        $sql = sprintf('SELECT id_schedule FROM %s WHERE id_product = %d', _DB_PREFIX_ . 'product', (int) $id_product);
        return Db::getInstance()->getValue($sql);
    }

}
