<?php

class ControllerExtensionModuleRemarkety extends Controller
{
    const REMARKETY_MODULE_VERSION = 'oc23.1.1.16';

    const API_RESPONSE_STATUS_ERROR = 'FAILED';
    const API_RESPONSE_STATUS_SUCCESS = 'SUCCESS';

    private static $debug = 0;
    private static $logMessages = array();

    public static function debug($msg) {
        if (self::$debug)
            self::$logMessages[] = $msg;
    }

    public function getShopSettings()
    {
        $this->_initApiRequest();

        $storeId = $this->_getStoreId();
        $this->load->model('module/remarkety');

        $results = $this->model_module_remarkety->getStoreSettings($storeId);

        $storeConfiguration = array();
        foreach ($results as $config) {
            $storeConfiguration[$config['key']] = $config['value'];
        }

        $countryRow = $this->model_module_remarkety->getCountryById($storeConfiguration['config_country_id']);
        $countryCode = (isset($countryRow[0]['iso_code_3'])) ? $countryRow[0]['iso_code_3'] : '';

        $zoneRow = $this->model_module_remarkety->getZoneById($storeConfiguration['config_zone_id']);
        $zoneCode = (isset($zoneRow[0]['code'])) ? $zoneRow[0]['code'] : '';

        $locale = $this->model_module_remarkety->getLocale($storeConfiguration['config_language']);

        if (array_key_exists('config_secure', $storeConfiguration) && $storeConfiguration['config_secure']) {
            if (isset($storeConfiguration['config_ssl'])) {
                $server = $storeConfiguration['config_ssl'];
            } else {
                $server = $this->config->get('config_ssl');
            }
            if (empty($server)) {
                $server = array_key_exists('config_url', $storeConfiguration) ? $storeConfiguration['config_url'] : '';
            }
        } elseif (array_key_exists('config_url', $storeConfiguration) && $storeConfiguration['config_url']) {
            $server = $storeConfiguration['config_url'];
        } else {
            $server = "";
            /*
            $this->load->model('setting/store');//$this->model_setting_store->load($storeId);
            $store = $this->model_setting_store->getStore($storeId);
            if (!empty($store) && isset($store['url']))
                $server = $store['url'];
            else
                $server = HTTP_CATALOG;
            */
        }

        if (is_file(DIR_IMAGE . $storeConfiguration['config_logo'])) {
            $logo = $server . 'image/' . $storeConfiguration['config_logo'];
        } else {
            $logo = '';
        }

        $result = array(
            'domain' => str_replace(array('http://','https://'), '', $server),
            'name' => $storeConfiguration['config_name'],
            'contact_name' => $storeConfiguration['config_owner'],
            'phone' => $storeConfiguration['config_telephone'],
            'email' => $storeConfiguration['config_email'],
            'address' => $storeConfiguration['config_address'],
            'country' => $countryCode,
            'region' => $zoneCode,
            'currency' => $storeConfiguration['config_currency'],
            'locale' => $locale,
            'logo_path' => $logo,
            'timezone' => date_default_timezone_get()
        );

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $result);
    }

    public function getOrderStatuses()
    {
        $this->_initApiRequest();

        $this->load->model('module/remarkety');

        $results = $this->model_module_remarkety->getOrderStatuses();

        $orderStatusesData = array();
        foreach ($results as $orderStatus) {
            $orderStatusesData[$orderStatus['order_status_id']] = array(
                'order_status_code' => $orderStatus['order_status_id'],
                'order_status_name' => $orderStatus['name']
            );
        }

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $orderStatusesData);
    }

    public function createCoupon()
    {
        $this->_initApiRequest();

        $this->load->model('module/remarkety');
        $this->load->language('module/remarkety');

        $params = $this->_getCreateCouponParams();

        $error = $this->model_module_remarkety->validateCouponParams($params);
        if ($error) {
            $errorMessage = $this->language->get($error);
            $this->_sendApiResponse(self::API_RESPONSE_STATUS_ERROR, $errorMessage);
        }

        $this->model_module_remarkety->createCoupon($params);

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, true);
    }

    public function getShoppingCarts()
    {
        $params = $this->_initApiRequest();

        $this->load->model('module/remarkety');
        $this->load->model('tool/image');

        $filter_data = array(
            'filter_updated_at_min' => $params['updated_at_min'],
            'filter_updated_at_max' => $params['updated_at_max'],
            'filter_store_id'       => $this->_getStoreId(),
            'start'                 => ($params['page'] - 1) * $params['limit'],
            'limit'                 => $params['limit']
        );
        $results = $this->model_module_remarkety->getShoppingCarts($filter_data);
        $resultsGrouped = $this->model_module_remarkety->getShoppingCartsGrouped($filter_data);

        $cartsData = array();
        $items = array();
        foreach ($results as $cart) {
            $cartTotal[$cart['customer_id']] = 0;
            $modifiedOn[$cart['customer_id']] = 0;
            $orderTotals = null;
            $taxConfig = $this->config->get('config_tax');
            if (isset($cart['cart']) && !empty($cart['cart'])) {
                //handle carts
                $productsMap = array();
                $productsMap[$cart['product_id']] = $cart['quantity'];

                $products = $this->model_module_remarkety->getProducts(array(
                    'products_ids' => array_keys($productsMap),
                    'filter_store_id' => $this->_getStoreId()
                ));

                if (!empty($products)) {
                    foreach ($products as $product) {
                        $image = $this->getProductImage($product);
                        $itemTotal = $productsMap[$product['product_id']] * $product['price'];
                        $items[$cart['customer_id']][$product['product_id']] = array(
                            'product_id' => $product['product_id'],
                            'product_name' => $product['name'],
                            'product_thumb_path' => $image,
                            'product_url' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $product['product_id'])),
                            'product_price' => $product['price'],
                            'sale_price_with_tax'    => $this->tax->calculate($product['price'], $product['tax_class_id'], $taxConfig),
                            'quantity' => $productsMap[$product['product_id']],
                            'total' => $itemTotal,
                            'product_sku'   => $product['sku'],
                            'manufacturer' => $product['manufacturer'],
                        );
                        $cartTotal[$cart['customer_id']] += $itemTotal;
                    }
                }

                $modifiedOn[$cart['customer_id']] = $cart['date_added'];

                if(!isset($createdOn[$cart['customer_id']]))
                    $createdOn[$cart['customer_id']] = $cart['date_added'];

            }
            else{
                //handle missing order
                $products = $this->model_module_remarkety->getOrderProducts($cart['order_id']);
                $orderTotals = $this->model_module_remarkety->getOrderTotals($cart['order_id']);
                if (!empty($products)) {
                    foreach ($products as $k => $product) {
                        if (is_file(DIR_IMAGE . $product['image'])) {
                            $product['image'] = $this->model_tool_image->resize($product['image'], 228, 228);
                        } else {
                            $product['image'] = $this->model_tool_image->resize('placeholder.png', 228, 228);
                        }
                        $product['product_url'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
                        $product['categories'] = $this->model_module_remarkety->getProductCategories($product['product_id']);
                        $product['product_thumb_path'] = $product['image'];
                        $product['product_price'] = $product['price'];
                        $product['sale_price_with_tax'] = $this->tax->calculate($product['price'], $product['tax_class_id'], $taxConfig);
                        $product['product_name'] = $product['name'];
                        $product['product_sku'] = $product['sku'];
                        $items[$cart['order_id']][$product['product_id']] = $product;
                    }
                }


                $cartTotal[$cart['order_id']] = $cart['total'];
            }

        }
        $i = 0;
        foreach ($resultsGrouped as $cart) {

            if (isset($cart['cart']) && !empty($cart['cart'])) {

                if (isset($modifiedOn[$cart['customer_id']])) {
                    $modifiedOnDate = $modifiedOn[$cart['customer_id']];
                } else {
                    $modifiedOnDate = isset($cart['cart_last_change']) ? $cart['cart_last_change'] : $cart['date_added'];
                }

                if (isset($createdOn[$cart['customer_id']])) {
                    $createdOnDate = $createdOn[$cart['customer_id']];
                } else {
                    $createdOnDate = $cart['date_added'];
                }

                $cartsData[$i] = array(
                    'customer' => array(
                        'customer_id' => $cart['customer_id'],
                        'first_name' => $cart['firstname'],
                        'last_name' => $cart['lastname'],
                        'email' => $cart['email'],
                        'customer_group_id' => $cart['customer_group_id'],
                    ),
                    'items' => $items[$cart['customer_id']],
                    'created_on' => $createdOnDate,
                    'modified_on' => $modifiedOnDate,
                    'cart_total' => $cartTotal[$cart['customer_id']],
                    'checkout_url' => $this->url->link('checkout/checkout')
                );
                if (!empty($orderTotals)) {
                    $cartsData[$i]['shipping'] = $this->getOrderShipping($orderTotals);
                    $cartsData[$i]['couponDiscount'] = $this->getCouponDiscount($orderTotals);
                    $cartsData[$i]['coupon_code'] = $this->getCouponCode($orderTotals);
                }
            } else {

                $modifiedOnDate = $cart['cart_last_change'];

                $createdOnDate = $cart['date_added'];


                $cartsData[$i] = array(
                    'customer' => array(
                        'customer_id' => $cart['customer_id'],
                        'first_name' => $cart['firstname'],
                        'last_name' => $cart['lastname'],
                        'email' => $cart['email'],
                        'customer_group_id' => $cart['customer_group_id'],
                    ),
                    'items' => $items[$cart['order_id']],
                    'created_on' => $createdOnDate,
                    'modified_on' => $modifiedOnDate,
                    'cart_total' => $cartTotal[$cart['order_id']],
                    'checkout_url' => $this->url->link('checkout/checkout')
                );
                if (!empty($orderTotals)) {
                    $cartsData[$i]['shipping'] = $this->getOrderShipping($orderTotals);
                    $cartsData[$i]['couponDiscount'] = $this->getCouponDiscount($orderTotals);
                    $cartsData[$i]['coupon_code'] = $this->getCouponCode($orderTotals);
                }
            }
            $i++;
        }
        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null,$cartsData);
    }

    public function getOrdersCount()
    {
        $this->_initApiRequest();

        $this->load->model('module/remarkety');

        $result = $this->model_module_remarkety->getOrdersCount(array(
            'filter_store_id'       => $this->_getStoreId()
        ));

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $result);
    }

    public function getShoppersCount()
    {
        $this->_initApiRequest();

        $this->load->model('module/remarkety');

        $result = $this->model_module_remarkety->getShoppersCount(array(
            'filter_store_id'       => $this->_getStoreId()
        ));

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $result);
    }

    public function getProductsCount()
    {
        $this->_initApiRequest();

        $this->load->model('module/remarkety');

        $result = $this->model_module_remarkety->getProductsCount(array(
            'filter_store_id'       => $this->_getStoreId()
        ));

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $result);
    }

    public function getOrders()
    {
        $params = $this->_initApiRequest();

        $this->load->model('module/remarkety');

        $filter_data = array(
            'filter_updated_at_min' => $params['updated_at_min'],
            'filter_updated_at_max' => $params['updated_at_max'],
            'filter_store_id'       => $this->_getStoreId(),
            'start'                 => ($params['page'] - 1) * $params['limit'],
            'limit'                 => $params['limit']
        );
        $results = $this->model_module_remarkety->getOrders($filter_data);

        $ordersData = array();
        foreach ($results as $order) {

            $products = $this->model_module_remarkety->getOrderProducts($order['order_id']);
            $orderTotals = $this->model_module_remarkety->getOrderTotals($order['order_id']);
            $items = array();
            if (!empty($products)) {
                foreach ($products as $k => $product) {
//                    if (is_file(DIR_IMAGE . $product['image'])) {
//                        $product['image'] = $this->model_tool_image->resize($product['image'], 228, 228);
//                    } else {
//                        $product['image'] = $this->model_tool_image->resize('placeholder.png', 228, 228);
//                    }
                    $product['image'] = $this->getProductImage($product);
                    $product['prod_url'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
                    $product['categories'] = $this->model_module_remarkety->getProductCategories($product['product_id']);
                    $items[$product['order_product_id']] = $product;
                }
            }

            $ordersData[$order['order_id']] = array(
                'order_id' => $order['order_id'],
                'order_total' => $order['total'],
                'order_shipping' => $this->getOrderShipping($orderTotals),
                'coupon_discount' => $this->getCouponDiscount($orderTotals),
                'coupon_code'   => $this->getCouponCode($orderTotals),
                'currency_code' => $order['currency_code'],
                'order_status_id' => $order['order_status_id'],
                'order_status' => $order['order_status'],
                'customer_id' => $order['customer_id'],
                'created_on' => $order['created_on'],
                'modified_on' => $order['modified_on'],
                'customer' => array(
                    'customer_id' => $order['customer_id'],
                    'first_name' => $order['first_name'],
                    'last_name' => $order['last_name'],
                    'email' => $order['email'],
                    'customer_group_id' => $order['customer_group_id'],
                    'modified_on' => $this->model_module_remarkety->getCustomerLastModifiedDate($order['customer_id']),
                ),
                'shipping' => array(
                    'first_name' => $order['shipping_firstname'],
                    'last_name' => $order['shipping_lastname'],
                    'city' => $order['shipping_city'],
                    'postcode' => $order['shipping_postcode'],
                    'country' => $order['shipping_country'],
                    'country_id' => $order['shipping_country_id'],
                    'region' => $order['shipping_region'],
                ),
                'items' => $items
            );
        }

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $ordersData);
    }

    public function getShoppers()
    {
        $params = $this->_initApiRequest();

        $this->load->model('module/remarkety');

        $filter_data = array(
            'filter_updated_at_min' => $params['updated_at_min'],
            'filter_updated_at_max' => $params['updated_at_max'],
            'filter_store_id'       => $this->_getStoreId(),
            'start'                 => ($params['page'] - 1) * $params['limit'],
            'limit'                 => $params['limit']
        );
        $results = $this->model_module_remarkety->getShoppers($filter_data);
        $shoppersData = $this->model_module_remarkety->prepareShoppersData($results);

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $shoppersData);
    }

    public function getProducts()
    {
        $params = $this->_initApiRequest();

        $this->load->model('module/remarkety');
        $this->load->model('tool/image');

        $filter_data = array(
            'filter_updated_at_min' => $params['updated_at_min'],
            'filter_updated_at_max' => $params['updated_at_max'],
            'filter_store_id'       => $this->_getStoreId(),
            'start'                 => ($params['page'] - 1) * $params['limit'],
            'limit'                 => $params['limit']
        );
        $results = $this->model_module_remarkety->getProducts($filter_data);

        $productsData = array();
        foreach ($results as $product) {
            $image = $this->getProductImage($product);
            $taxConfig = $this->config->get('config_tax');
            $productsData[$product['product_id']] = array(
                'product_id' => $product['product_id'],
                'product_sku' => $product['sku'],
                'product_name' => $product['name'],
                'modified_on' => $product['date_modified'],
                'created_on'    => $product['date_added'],
                'available_on'  => $product['date_available'],
                'productThumbPath' => $image,
                'price' => $product['price'],
                'sale_price_with_tax'    => $this->tax->calculate($product['price'], $product['tax_class_id'], $taxConfig),
                'prod_url' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'categories' => $this->model_module_remarkety->getProductCategories($product['product_id']),
                'manufacturer' => $product['manufacturer'],
            );
        }

        $this->_sendApiResponse(self::API_RESPONSE_STATUS_SUCCESS, null, $productsData);
    }

    protected function _getCreateCouponParams()
    {
        $params = array(
            'code' => null,
            'percent_or_total' => null,
            'gift_or_permanent' => null,
            'value' => null,
            'validFromValue' => null,
            'startDate' => null,
            'expiryDate' => null,
        );

        foreach ($params as $key => $param){
            if (isset($this->request->get[$key]) && !empty($this->request->get[$key])) {
                $params[$key] = $this->request->get[$key];
            }
        }

        return $params;
    }

    protected function _initApiRequest()
    {
        $this->load->language('module/remarkety');

        /*if (!$this->config->get('remarkety_installed')) {
            $error = $this->language->get('api_error_remarkety_not_registered');
            $this->_sendApiResponse(self::API_RESPONSE_STATUS_ERROR, $error);
        }*/

        $params = $this->_getApiRequestParams();

        $error = $this->_validateApiRequestParams($params);
        if (!empty($error)) {
            $this->_sendApiResponse(self::API_RESPONSE_STATUS_ERROR, $error);
        }

        return $params;
    }

    protected function _validateApiRequestParams($params)
    {

        // api key
        if (empty($params['remarkety_api_key']) || $this->config->get('remarkety_api_key') != $params['remarkety_api_key']) {
            return $this->language->get('api_error_invalid_api_key');
        }

        // ToDo - add validation for params

        return null;
    }

    protected function _getApiRequestParams()
    {
        $params = array(
            'remarkety_api_key' => null,
            //'task' => null,
            'updated_at_min' => null,
            'updated_at_max' => null,
            'limit' => 250,
            'page' => 1,
            'debug' => false
        );

        foreach ($params as $key => $param){

            if (isset($this->request->get[$key]) && !empty($this->request->get[$key])) {
                $params[$key] = $this->request->get[$key];
            }
        }

        if ($params['debug'])
            self::$debug = true;
        return $params;
    }

    protected function _sendApiResponse($status, $error = null, $data = null)
    {
        header("Content-Type: application/json");
        $response = array(
            'DATA' => $data,
            'STATUS' => $status,
            'ERROR' => $error,
            'PLUGIN_VERSION' => self::REMARKETY_MODULE_VERSION,
            "TIMEZONE"  => date_default_timezone_get()
        );


        if (self::$debug) {
            $response['DEBUG'] = self::$logMessages;
        }

        echo json_encode($response);
        exit();
    }

    /******************************** EVENTS ********************************************/

    public function onPreCustomerEdit($action_name,$data)
    {

        if ($this->config->get('remarkety_installed')) {

            if (isset($this->session->data['remarkety_customer_changed'])) {
                unset($this->session->data['remarkety_customer_changed']);
            }

            $this->load->model('module/remarkety');

            $filter_data = array(
                'customer_id' => $this->customer->getId(),
                'filter_store_id'       => $this->_getStoreId(),
            );
            $results = $this->model_module_remarkety->getShoppers($filter_data);
            if (!isset($results[0])) {
                return false;
            }


            $importantKeys = array('firstname', 'lastname', 'email');

            $hasImportantChanges = false;
            foreach ($importantKeys as $key) {
                if ($results[0][$key] != $data[0][$key]) {
                    $hasImportantChanges = true;
                    break;
                }
            }

            if ($hasImportantChanges) {
                $this->session->data['remarkety_customer_changed'] = true;
            }
        }
    }

    public function onPostCustomerEdit($action_name,$data)
    {
        $customer_id = $this->customer->getId();

        if ($this->config->get('remarkety_installed')
            && isset($this->session->data['remarkety_customer_changed'])
            && $this->session->data['remarkety_customer_changed']
        ) {
            unset($this->session->data['remarkety_customer_changed']);
            $this->load->model('module/remarkety');
            $this->load->model('module/remarkety_queue');

            $filter_data = array(
                'customer_id'           => $customer_id,
                'filter_store_id'       => $this->_getStoreId(),
            );
            $results = $this->model_module_remarkety->getShoppers($filter_data);

            if (!isset($results[0])) {
                return false;
            }


            $shoppersData = $this->model_module_remarkety->prepareShoppersData($results);

            $this->model_module_remarkety_queue->sendEventRequest('customers/update', $shoppersData[$customer_id]);
        }
    }

    private  function getOrderShipping($orderTotals) {
        foreach ($orderTotals as $row) {
            if ($row['code'] == 'shipping')
                return ($row['value']);
        }
        return 0;
    }

    private function getCouponDiscount($orderTotals) {
        foreach ($orderTotals as $row) {
            if ($row['code'] == 'coupon')
                return (-1 * $row['value']);
        }
        return 0;
    }

    private function getCouponCode($orderTotals) {
        foreach ($orderTotals as $row) {
            if ($row['code'] == 'coupon') {
                $matches = array();
                if (preg_match('/\(([^\)]+)\)/', $row['title'], $matches))
                    return ($matches[1]);
            }
        }
        return '';
    }

    protected function _getStoreId()
    {
        $remarketyStoreId = $this->config->get('remarkety_store_id');
        return empty($remarketyStoreId) ? $this->config->get('config_store_id') : $remarketyStoreId;
    }

    /******************************** CRONJOB ********************************************/

    protected function getProductImage($product) {
        $modelToolImage = $this->model_tool_image;
        if (empty($modelToolImage)) {
            $this->load->model('tool/image');
            $modelToolImage = $this->model_tool_image;
        }

        if (is_file(DIR_IMAGE . $product['image'])) {
            $image = $modelToolImage->resize($product['image'], 228, 228);
        } else {
            $image = $modelToolImage->resize('placeholder.png', 228, 228);
        }
        return $image;
    }
    public function cronjob()
    {
        $this->load->model('module/remarkety_queue');

        $this->model_module_remarkety_queue->runQueue();
    }
}