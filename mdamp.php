<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'mdamp/classes/utils/DescriptionHelper.php';

class MDAMP extends Module
{
    private $hooks = [
        'header',
        'moduleRoutes',
        'ampAnalytics',
    ];

    public function __construct()
    {
        $this->name = 'mdamp';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'MDWeb';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('AMP for product & category page');
        $this->description = $this->l('Implement AMP for product & category page !');
    }

    public function install()
    {
        if (parent::install() && $this->registerHook($this->hooks)) {
            return true;
        }

        return false;
    }

    public function hookDisplayHeader($params)
    {
        $cacheId = null;
        if (!isset($this->context->controller->php_self)) {
            return;
        }

        switch ($this->context->controller->php_self) {
            case 'product':
                $product = $this->context->controller->getProduct();

                if (!Validate::isLoadedObject($product)) {
                    return;
                }

                $cacheId = 'amp_header|product|' . $product->id;
                $ampLink = $this->context->link->getModuleLink('mdamp', 'product', array('id' => $product->id, 'link_rewrite' => $product->link_rewrite), true, $this->context->language->id, $this->context->shop->id, true);

                break;

            case 'category':
                $category = $this->context->controller->getCategory();

                if (!Validate::isLoadedObject($category)) {
                    return;
                }

                $cacheId = 'amp_header|category|' . $category->id;
                $ampLink = $this->context->link->getModuleLink('mdamp', 'category', array('id' => $category->id, 'link_rewrite' => $category->link_rewrite), true, $this->context->language->id, $this->context->shop->id, true);

                break;

            default:
                return;
        }

        if (!$this->isCached('header.tpl', $this->getCacheId($cacheId))) {
            $this->context->smarty->assign(array('amp_link' => $ampLink));
        }

        return $this->display(__FILE__, 'header.tpl', $this->getCacheId($cacheId));
    }

    public function hookModuleRoutes()
    {
        return array(
            'module-mdamp-product' => array(
                'controller' => 'product',
                'rule' => 'amp/product/{id}-{link_rewrite}.html',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    'link_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'mdamp',
                ),
            ),
            'module-mdamp-category' => array(
                'controller' => 'category',
                'rule' => 'amp/category/{id}-{link_rewrite}',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    'link_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'mdamp',
                ),
            ),
        );
    }

    public function hookAmpAnalytics($params)
    {
        $cacheId = 'mdamp|analytics';
        if (!$this->isCached('analytics.tpl', $this->getCacheId($cacheId))) {
            $this->context->smarty->assign(
                [
                    'codeGA' => Configuration::get('MDAMP_GACODE'),
                ]
            );
        }

        return $this->display(__FILE__, 'analytics.tpl', $this->getCacheId($cacheId));
    }
}
