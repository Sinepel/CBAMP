<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'cbamp/classes/utils/DescriptionHelper.php';

/**
 * @author Constantin Boulanger <constantin.boulanger@gmail.com>
 */
class CBAMP extends Module
{
    private $hooks = [
        'header',
        'moduleRoutes',
        'ampAnalytics',
    ];

    private $output = null;

    public function __construct()
    {
        $this->name = 'cbamp';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'MDWeb';
        $this->need_instance = 0;
        $this->bootstrap = true;

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

    public function getContent()
    {
        $this->postProcess();
        return $this->output . $this->displayForm();
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submit' . $this->name)) {
            $form_values = $this->getConfigFormValues();
            $return = true;
            foreach (array_keys($form_values) as $key) {
                $return &= Configuration::updateValue($key, Tools::getValue($key));
            }

            if (!$return) {
                $this->warnings[] = $this->l('Problem during saving data');
            } else {
                $this->output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CBAMP_GACODE' => Configuration::get('CBAMP_GACODE'),
            'CBAMP_SHARE_BTN' => Configuration::get('CBAMP_SHARE_BTN'),
            'CBAMP_FULL_VERSION_BTN' => Configuration::get('CBAMP_FULL_VERSION_BTN'),
            'CBAMP_ENABLE_CATEGORY' => Configuration::get('CBAMP_ENABLE_CATEGORY'),
            'CBAMP_ENABLE_PRODUCT' => Configuration::get('CBAMP_ENABLE_PRODUCT'),
        );
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('AMP Product page'),
                    'name' => 'CBAMP_ENABLE_PRODUCT',
                    'is_bool' => true,
                    'desc' => $this->l('Enable AMP version of the product page'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('AMP Category page'),
                    'name' => 'CBAMP_ENABLE_CATEGORY',
                    'is_bool' => true,
                    'desc' => $this->l('Enable AMP version of the category page'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Google Analytics Identifer'),
                    'name' => 'CBAMP_GACODE',
                    'size' => 20,
                    'required' => false
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Share buttons'),
                    'name' => 'CBAMP_SHARE_BTN',
                    'is_bool' => true,
                    'desc' => $this->l('Active share buttons at the product\'s bottom page'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Full version link'),
                    'name' => 'CBAMP_FULL_VERSION_BTN',
                    'is_bool' => true,
                    'desc' => $this->l('Show link to go to the full page at the bottom of product / category page'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
        );

        return $helper->generateForm($fieldsForm);
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
                $ipa = null;

                if (!Validate::isLoadedObject($product)) {
                    return;
                }

                if (Tools::getIsset('id_product_attribute')) {
                    $ipa = (int) Tools::getValue('id_product_attribute');
                }

                $cacheId = 'amp_header|product|' . $product->id;
                if ($ipa) {
                    $cacheId .= "|{$ipa}";
                }
                $ampLink = $this->context->link->getModuleLink('cbamp', 'product', array('id' => $product->id, 'ipa' => $ipa, 'link_rewrite' => $product->link_rewrite), true, $this->context->language->id, $this->context->shop->id, true);

                break;

            case 'category':
                $category = $this->context->controller->getCategory();

                if (!Validate::isLoadedObject($category)) {
                    return;
                }

                $cacheId = 'amp_header|category|' . $category->id;
                $ampLink = $this->context->link->getModuleLink('cbamp', 'category', array('id' => $category->id, 'link_rewrite' => $category->link_rewrite), true, $this->context->language->id, $this->context->shop->id, true);

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
            'module-cbamp-product' => array(
                'controller' => 'product',
                'rule' => 'amp/product/{id}{-:ipa}-{link_rewrite}.html',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    'ipa' => array('regexp' => '[0-9]+', 'param' => 'ipa'),
                    'link_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'cbamp',
                ),
            ),
            'module-cbamp-category' => array(
                'controller' => 'category',
                'rule' => 'amp/category/{id}-{link_rewrite}',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    'link_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'cbamp',
                ),
            ),
        );
    }

    public function hookAmpAnalytics($params)
    {
        if (Configuration::get('CBAMP_GACODE')) {
            $cacheId = 'cbamp|analytics';
            if (!$this->isCached('analytics.tpl', $this->getCacheId($cacheId))) {
                $this->context->smarty->assign(
                    [
                        'codeGA' => Configuration::get('CBAMP_GACODE'),
                    ]
                );
            }

            return $this->display(__FILE__, 'analytics.tpl', $this->getCacheId($cacheId));
        }
    }
}
