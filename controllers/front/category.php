<?php

class CBAMPCategoryModuleFrontController extends ModuleFrontController
{
    public $display_header = false;
    public $display_footer = false;
    public $display_column_left = false;
    public $display_column_right = false;

    private $list = [
        0 => 'name',
        1 => 'price',
        2 => 'date_add',
        3 => 'date_upd',
        4 => 'position',
        5 => 'manufacturer_name',
        6 => 'quantity',
        7 => 'reference',
    ];

    private $category = null;
    private $categoryAMP = [];

    public function init()
    {
        parent::init();
        $this->category = new Category((int) Tools::getValue('id'), $this->context->language->id, $this->context->shop->id);
        if (!Validate::isLoadedObject($this->category)) {
            Tools::redirect($this->context->link->getPageLink('PageNotFoundController'));
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        $page = (int) Tools::getValue('p', 1);
        $this->p = $page;

        $this->categoryAMP['clean_description'] = DescriptionHelper::cleanDescription($this->category->description);
        $this->categoryAMP['name'] = $this->category->name;

        $this->orderBy = $this->list[Configuration::get('PS_PRODUCTS_ORDER_BY')];
        $this->orderWay = (Configuration::get('PS_PRODUCTS_ORDER_WAY') == 0) ? 'ASC' : 'DESC';
        $this->n = Configuration::get('PS_PRODUCTS_PER_PAGE');

        // Count Products
        $this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
        $this->pagination((int) $this->nbProducts);
        $this->cat_products = $this->category->getProducts($this->context->language->id, (int) $this->p, (int) $this->n, $this->orderBy, $this->orderWay);

        $currentStart = (($this->p - 1) * $this->n) + 1;
        $currentStop = ($this->p * $this->n);
        $currentStop = ($currentStop < $this->nbProducts) ? $currentStop : $this->nbProducts;
        // in case when no product in category
        if ($currentStop < $currentStart) {
            $currentStart = $currentStop;
        }

        // if products available get product add to cart link
        if ($this->cat_products) {
            $priceDisplay = Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);

            foreach ($this->cat_products as &$product) {
                $product['addToCartLink'] = $this->context->link->getPageLink('cart', true, $this->context->language->id, array('add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)), false, $this->context->shop->id);

                $tmpProduct = new Product((int) $product['id_product'], false, $this->context->language->id);

                $product['ampLink'] = $this->context->link->getModuleLink('cbamp', 'product', ['id' => $tmpProduct->id, 'ipa' => null, 'link_rewrite' => $tmpProduct->link_rewrite]);

                if (!$priceDisplay || $priceDisplay == 2) {
                    $product['price'] = $tmpProduct->getPrice(true, null, 2, null, false, true);
                    $product['price_old'] = $tmpProduct->getPrice(true, null, 2, null, false, false);
                } elseif ($priceDisplay == 1) {
                    $product['price'] = $tmpProduct->getPrice(false, null, 2, null, false, true);
                    $product['price_old'] = $tmpProduct->getPrice(false, null, 2, null, false, true);
                }

                if ($product['price'] == $product['price_old']) {
                    $product['price'] = Tools::displayPrice($product['price']);
                    $product['price_old'] = null;
                } else {
                    $product['price'] = Tools::displayPrice($product['price']);
                    $product['price_old'] = Tools::displayPrice($product['price_old']);
                }
            }
        }

        $this->context->smarty->assign([
            'catProducts' => $this->cat_products,
            'link' => $this->context->link,
            'css' => Media::minifyCSS(Tools::file_get_contents(_PS_MODULE_DIR_ . 'cbamp/views/css/front.css')),
            'canonical' => $this->context->link->getCategoryLink($this->category, null, $this->context->language->id, null, $this->context->shop->id),
            'meta' => Meta::getCategoryMetas((int) $this->category->id, $this->context->language->id, 'category'),
            'noOfPages' => $this->nbProducts / $this->n,
            'currentPage' => $this->p,
            'category' => $this->category,
            'categoryAMP' => $this->categoryAMP,
            'currentStop' => $currentStop,
            'currentStart' => $currentStart,
            'nbProducts' => $this->nbProducts,
        ]);

        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $this->setTemplate('module:cbamp/views/templates/front/category_17.tpl');
        } else {
            $this->setTemplate('category.tpl');
        }

        parent::initContent();
    }

    /**
     * @return bool
     */
    public function setMedia()
    {
        return false;
    }

    /**
     * Renders controller templates and generates page content
     *
     * @param array|string $content Template file(s) to be rendered
     *
     * @throws Exception
     * @throws SmartyException
     */
    protected function smartyOutputContent($content)
    {
        if (!Configuration::get('PS_JS_DEFER')) {
            parent::smartyOutputContent($content);

            return;
        }

        Configuration::set('PS_JS_DEFER', 0);

        parent::smartyOutputContent($content);

        Configuration::set('PS_JS_DEFER', 1);
    }

    public function pagination($total_products = null)
    {
        if (!self::$initialized) {
            $this->init();
        } elseif (!$this->context) {
            $this->context = Context::getContext();
        }

        // Retrieve the default number of products per page and the other available selections
        $default_products_per_page = max(1, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $nArray = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

        if ((int) Tools::getValue('n') && (int) $total_products > 0) {
            $nArray[] = $total_products;
        }
        // Retrieve the current number of products per page (either the default, the GET parameter or the one in the cookie)
        $this->n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page) && in_array($this->context->cookie->nb_item_per_page, $nArray)) {
            $this->n = (int) $this->context->cookie->nb_item_per_page;
        }

        if ((int) Tools::getValue('n') && in_array((int) Tools::getValue('n'), $nArray)) {
            $this->n = (int) Tools::getValue('n');
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int) Tools::getValue('p', 1);
        // If the parameter is not correct then redirect (do not merge with the previous line, the redirect is required in order to avoid duplicate content)
        if (!is_numeric($this->p) || $this->p < 1) {
            Tools::redirect(self::$link->getPaginationLink(false, false, $this->n, false, 1, false));
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $current_url = preg_replace('/(\?)?(&amp;)?p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }

        $pages_nb = ceil($total_products / (int) $this->n);
        if ($this->p > $pages_nb && $total_products != 0) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));
        }

        $range = 2; /* how many pages around page selected */
        $start = (int) ($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }
        $stop = (int) ($this->p + $range);
        if ($stop > $pages_nb) {
            $stop = (int) $pages_nb;
        }

        $this->context->smarty->assign(array(
            'nb_products' => $total_products,
            'products_per_page' => $this->n,
            'pages_nb' => $pages_nb,
            'p' => $this->p,
            'n' => $this->n,
            'nArray' => $nArray,
            'range' => $range,
            'start' => $start,
            'stop' => $stop,
            'current_url' => $current_url,
        ));
    }
}
