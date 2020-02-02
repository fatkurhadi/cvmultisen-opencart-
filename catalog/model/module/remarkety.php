<?php

require_once(__DIR__.'../../../controller/extension/module/remarkety.php');

class ModelModuleRemarkety extends Model
{
    protected static $_couponTypeMapping = array('percent' => 'P', 'total' => 'F');

    public function getShoppingCarts($data = array())
    {
    	/*** Missing orders - guest customers ***/
    	$sql = sprintf("
                    SELECT
                    o.order_id, o.customer_id, o.customer_group_id, o.firstname, o.lastname, o.email, currency_code, total, o.date_added, o.date_modified AS cart_last_change
                    FROM `" . DB_PREFIX . "order` o
                    WHERE o.store_id = %s AND o.order_status_id = 0
                    ",$data['filter_store_id'] );

    	if (!empty($data['filter_updated_at_min'])) {
    		$sql .= sprintf(" AND o.date_modified >= '%s'", $data['filter_updated_at_min']);
    	}
    	if (!empty($data['filter_updated_at_max'])) {
    		$sql .= sprintf(" AND o.date_modified <= '%s'", $data['filter_updated_at_max']);
    	}

    	$sql .= " ORDER BY o.date_modified ";

    	if (isset($data['start']) || isset($data['limit'])) {
    		if ($data['start'] < 0) {
    			$data['start'] = 0;
    		}

    		if ($data['limit'] < 1) {
    			$data['limit'] = 20;
    		}

    		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    	}
        ControllerExtensionModuleRemarkety::debug($sql);
    	$query = $this->db->query($sql);
    	$rows = $query->rows;


        /*** Carts - registered customers *****/
        $sql = sprintf("
                    SELECT c.customer_id, c.firstname, c.lastname, c.customer_group_id, c.email, ca.cart_id as cart
                    , ca.product_id, ca.quantity, ca.date_added
                    FROM " . DB_PREFIX . "cart ca
                    LEFT JOIN " . DB_PREFIX . "customer c ON ca.customer_id = c.customer_id
                    WHERE c.store_id = %s
                    ", $data['filter_store_id'] );

        if (isset($data['filter_updated_at_min']) && !empty($data['filter_updated_at_min'])) {
            $sql .= sprintf(" AND ca.date_added >= '%s'", $data['filter_updated_at_min']);
        }
        if (isset($data['filter_updated_at_max']) && !empty($data['filter_updated_at_max'])) {
            $sql .= sprintf(" AND ca.date_added <= '%s'",
                $data['filter_updated_at_max']);
        }

        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $sql .= sprintf(" AND c.customer_id = %s", $data['customer_id']);
        }

        $sql .= " ORDER BY ca.date_added ";
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        ControllerExtensionModuleRemarkety::debug($sql);
        $query = $this->db->query($sql);



        return array_merge($rows,$query->rows);
    }

    public function getShoppingCartsGrouped($data = array())
    {
        /*** Missing orders - guest customers ***/
        $sql = sprintf("
                    SELECT
                    o.order_id, o.customer_id, o.customer_group_id, o.firstname, o.lastname, o.email, currency_code, total, o.date_added, o.date_modified AS cart_last_change
                    FROM `" . DB_PREFIX . "order` o
                    WHERE o.store_id = %s AND o.order_status_id = 0
                    ",$data['filter_store_id'] );

        if (!empty($data['filter_updated_at_min'])) {
            $sql .= sprintf(" AND o.date_modified >= '%s'", $data['filter_updated_at_min']);
        }
        if (!empty($data['filter_updated_at_max'])) {
            $sql .= sprintf(" AND o.date_modified <= '%s'", $data['filter_updated_at_max']);
        }

        $sql .= " ORDER BY o.date_modified ";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        ControllerExtensionModuleRemarkety::debug($sql);
        $query = $this->db->query($sql);
        $rows = $query->rows;


        /*** Carts - registered customers *****/
        $sql = sprintf("
                    SELECT c.customer_id, c.firstname, c.lastname, c.customer_group_id, c.email, ca.cart_id as cart
                    , ca.product_id, ca.quantity, ca.date_added
                    FROM " . DB_PREFIX . "cart ca
                    LEFT JOIN " . DB_PREFIX . "customer c ON ca.customer_id = c.customer_id
                    WHERE c.store_id = %s GROUP BY c.customer_id
                    ", $data['filter_store_id'] );

        if (isset($data['filter_updated_at_min']) && !empty($data['filter_updated_at_min'])) {
            $sql .= sprintf(" AND ca.date_added >= '%s'", $data['filter_updated_at_min']);
        }
        if (isset($data['filter_updated_at_max']) && !empty($data['filter_updated_at_max'])) {
            $sql .= sprintf(" AND ca.date_added <= '%s'",
                $data['filter_updated_at_max']);
        }

        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $sql .= sprintf(" AND c.customer_id = %s", $data['customer_id']);
        }

        $sql .= " ORDER BY ca.date_added ";
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        ControllerExtensionModuleRemarkety::debug($sql);
        $query = $this->db->query($sql);



        return array_merge($rows,$query->rows);
    }

    public function createCoupon($data)
    {
        $name = 'Remarkety Coupon';
        $type = self::$_couponTypeMapping[$data['percent_or_total']];
        $logged = 1;
        $shipping = 0;
        $usesTotal = ('gift' == $data['gift_or_permanent']) ? 1 : "";
        $usesCustomer = ('gift' == $data['gift_or_permanent']) ? 1 : "";
        $status = 1;

        $sql = "INSERT INTO " . DB_PREFIX . "coupon SET
            name = '" . $this->db->escape($name) . "',
            code = '" . $this->db->escape($data['code']) . "',
            discount = '" . (float)$data['value'] . "',
            type = '" . $this->db->escape($type) . "',
            total = '" . (float)$data['validFromValue'] . "',
            logged = '" . (int)$logged . "',
            shipping = '" . (int)$shipping . "',
            date_start = '" . $this->db->escape($data['startDate']) . "',
            date_end = '" . $this->db->escape($data['expiryDate']) . "',
            uses_total = '" . (int)$usesTotal . "',
            uses_customer = '" . (int)$usesCustomer . "',
            status = '" . (int)$status . "',
            date_added = NOW()";

        ControllerExtensionModuleRemarkety::debug($sql);
        $this->db->query($sql);
        $coupon_id = $this->db->getLastId();

        return $coupon_id;
    }

    public function validateCouponParams($params)
    {
        if (!(isset($params['code']) && !empty($params['code']))) {
            return 'create_coupon_error_empty_code';
        }

        if (!(isset($params['percent_or_total']) && in_array($params['percent_or_total'], array_keys(self::$_couponTypeMapping)))) {
            return 'create_coupon_error_invalid_percent_or_total';
        }

        if (!(isset($params['gift_or_permanent']) && in_array($params['gift_or_permanent'], array('gift','permanent')))) {
            return 'create_coupon_error_invalid_gift_or_permanent';
        }

        if (!(isset($params['value']) && !empty($params['value']) && is_numeric($params['value']))) {
            return 'create_coupon_error_value';
        }

        if (!(isset($params['startDate']) && !empty($params['startDate']))) {
            return 'create_coupon_error_empty_start_date';
        }

        if (!(isset($params['expiryDate']) && !empty($params['expiryDate']))) {
            return 'create_coupon_error_empty_expiry_date';
        }

        if (!(isset($params['validFromValue']) && !empty($params['validFromValue']) && is_numeric($params['validFromValue']))) {
            return 'create_coupon_error_valid_from_value';
        }

        return null;
    }

    public function getLocale($langCode)
    {
        $sql = sprintf("
            SELECT locale
            FROM `" . DB_PREFIX . "language`
            WHERE code = '%s'
        ", $this->db->escape($langCode));

        $query = $this->db->query($sql);
        $rows = $query->rows;

        return (isset($rows[0]['locale'])) ? $rows[0]['locale'] : '';
    }

    public function getZoneById($zone_id)
    {
        $sql = sprintf("
            SELECT name, code
            FROM `" . DB_PREFIX . "zone`
            WHERE zone_id = '%s'
        ", $this->db->escape($zone_id));

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getCountryById($country_id)
    {
        $sql = sprintf("
            SELECT name, iso_code_3
            FROM `" . DB_PREFIX . "country`
            WHERE country_id = %s
        ", $this->db->escape($country_id));

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getStoreSettings($store_id)
    {
        $sql = sprintf("
            SELECT `key`, value
            FROM `" . DB_PREFIX . "setting`
            WHERE store_id = %s AND `key` IN (
                'config_url', 'config_ssl', 'config_secure', 'config_name', 'config_telephone', 'config_email', 'config_language',
                'config_country_id', 'config_zone_id', 'config_currency', 'config_address', 'config_owner', 'config_logo'
            )
        ", $this->db->escape($store_id));

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderProducts($order_id)
    {
        $sql = sprintf("
            SELECT op.order_product_id, op.product_id, op.name, op.quantity, op.price, op.total, tax, p.date_added,p.date_modified,p.image,pd.name,pd.description, p.sku,p.status,p.stock_status_id, p.tax_class_id, m.name as manufacturer
            FROM " . DB_PREFIX . "order_product op
            JOIN " . DB_PREFIX . "product p ON p.product_id = op.product_id
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "manufacturer m ON p.manufacturer_id=m.manufacturer_id
            WHERE op.order_id = %s AND pd.language_id = '%s'
        ", $this->db->escape($order_id), (int)$this->config->get('config_language_id'));

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderTotals($order_id)
    {
        $sql = sprintf("
            SELECT *
            FROM " . DB_PREFIX . "order_total ot
            WHERE order_id = %s
        ", $this->db->escape($order_id));

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrdersCount($data = array())
    {
        $sql = sprintf("
                    SELECT count(1) AS count
                    FROM `" . DB_PREFIX . "order` o
                    WHERE o.store_id = %s
                    ",$data['filter_store_id'] );

        $query = $this->db->query($sql);

        $result = $query->rows;
        return $result[0]['count'];
    }

    public function getOrders($data = array())
    {
        $sql = sprintf("
                    SELECT
                    o.order_id, o.customer_id, o.customer_group_id, o.firstname, o.lastname, o.email, shipping_firstname,
                    shipping_lastname, CONCAT(o.shipping_address_1, ' ', o.shipping_address_2) AS shipping_address,
                    shipping_city, shipping_postcode, shipping_country, shipping_country_id, shipping_zone AS shipping_region,
                    currency_code, total, o.order_status_id, os.name AS order_status, o.date_added AS created_on, o.date_modified AS modified_on
                    FROM `" . DB_PREFIX . "order` o
                    JOIN " . DB_PREFIX . "order_status os ON os.order_status_id = o.order_status_id
                    WHERE o.store_id = %s
                    ",$data['filter_store_id'] );

        if (!empty($data['filter_updated_at_min'])) {
            $sql .= sprintf(" AND o.date_modified >= '%s'", $data['filter_updated_at_min']);
        }
        if (!empty($data['filter_updated_at_max'])) {
            $sql .= sprintf(" AND o.date_modified <= '%s'", $data['filter_updated_at_max']);
        }

        $sql .= " ORDER BY o.date_modified ";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        ControllerExtensionModuleRemarkety::debug($sql);
        $query = $this->db->query($sql);
        $rows = array();

        foreach($query->rows AS $k => $row) {
            $rows[$k] = $row;
            $rows[$k]['first_name'] = $row['firstname'];
            $rows[$k]['last_name'] = $row['lastname'];
            unset($rows[$k]['customer']['firstname'], $rows[$k]['customer']['lastname']);

        }

        return $rows;
    }
    public function getOrderStatuses()
    {
        $sql = sprintf("
                    SELECT
                    os.order_status_id, os.name
                    FROM `" . DB_PREFIX . "order_status` os
                    WHERE os.language_id = '%s'
                    ", (int)$this->config->get('config_language_id') );

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getShoppersCount($data = array())
    {
        $sql = sprintf("
                    SELECT count(1) AS count
                    FROM " . DB_PREFIX . "customer c
                    WHERE c.store_id = %s
                    ", $data['filter_store_id'] );

        $query = $this->db->query($sql);

        $result = $query->rows;
        return $result[0]['count'];
    }

    public function getShoppers($data = array())
    {
        $sql = sprintf("
                    SELECT c.customer_id, c.firstname, c.lastname, cgd.name AS customer_group, c.customer_group_id,
                    email, c.status, date_added, c.cart_last_change, c.newsletter AS is_marketing_allowed,
                    a.firstname AS address_firstname, a.lastname AS address_lastname,
                    a.city AS address_city, a.postcode AS address_postcode,
                    CONCAT(a.address_1, ' ', a.address_2) AS address_address,
                    cn.iso_code_3 AS address_country, z.name AS address_region
                    FROM " . DB_PREFIX . "customer c
                    LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)
                    LEFT JOIN " . DB_PREFIX . "address a ON a.address_id = c.address_id
                    LEFT JOIN " . DB_PREFIX . "country cn ON cn.country_id = a.country_id
                    LEFT JOIN " . DB_PREFIX . "zone z ON z.zone_id = a.zone_id
                    WHERE cgd.language_id = '%s' AND c.store_id = %s
                    ",(int)$this->config->get('config_language_id'), $data['filter_store_id'] );

        if (isset($data['filter_updated_at_min']) && !empty($data['filter_updated_at_min'])) {
            $sql .= sprintf(" AND c.date_added >= '%s'", $data['filter_updated_at_min']);
        }
        if (isset($data['filter_updated_at_max']) && !empty($data['filter_updated_at_max'])) {
            $sql .= sprintf(" AND c.date_added <= '%s'", $data['filter_updated_at_max']);
        }

        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $sql .= sprintf(" AND c.customer_id = %s", $data['customer_id']);
        }

        $sql .= " ORDER BY c.date_added ";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        ControllerExtensionModuleRemarkety::debug($sql);
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function prepareShoppersData($data)
    {
        $shoppersData = array();
        if (empty($data)) {
            return $shoppersData;
        }
        foreach ($data as $shopper) {

            $shoppersData[$shopper['customer_id']] = array(
                'internal_user_id' => (int) $shopper['customer_id'],
                'first_name' => $shopper['firstname'],
                'last_name' => $shopper['lastname'],
                'email' => $shopper['email'],
                'created_on' => $shopper['date_added'],
                'accepts_marketing' => (bool) $shopper['is_marketing_allowed'],
                'city' => $shopper['address_city'],
                'zip' => $shopper['address_postcode'],
                'address1' => $shopper['address_address'],
                'country_id' => $shopper['address_country'],
                'province' => $shopper['address_region']
            );

            
        }

        return $shoppersData;
    }

    public function getCustomerLastModifiedDate($customer_id) {
        $sql = sprintf("
                SELECT date_added
                FROM " . DB_PREFIX . "customer_activity ca
                WHERE ca.customer_id = %s AND ca.key = 'edit' ORDER BY date_added DESC LIMIT 1
                ",(int)$customer_id);

        $query = $this->db->query($sql);

        if(isset($query->rows[0]) && isset($query->rows[0]['date_added'])) {
            return $query->rows[0]['date_added'];
        }
    }

    public function getProductsCount($data = array())
    {
        $sql = sprintf("
            SELECT count(1) AS count
            FROM " . DB_PREFIX . "product p
            JOIN " . DB_PREFIX . "product_to_store pts ON (p.product_id = pts.product_id)
            WHERE pts.store_id = %s
            "
            , (int)$data['filter_store_id']);

        $query = $this->db->query($sql);

        $result = $query->rows;
        return $result[0]['count'];
    }

    public function getProducts($data = array())
    {
        $sql = sprintf("
            SELECT p.product_id, p.sku, pd.name, p.date_added, p.date_modified, p.date_available, p.image, p.price, p.tax_class_id, m.name as manufacturer
            FROM " . DB_PREFIX . "product p
            JOIN " . DB_PREFIX . "product_to_store pts ON (p.product_id = pts.product_id AND pts.store_id = %s)
            LEFT JOIN " . DB_PREFIX . "manufacturer m ON p.manufacturer_id = m.manufacturer_id
            LEFT JOIN " . DB_PREFIX  . "product_description pd ON (p.product_id = pd.product_id)
            WHERE pd.language_id = '%s'
            "
            , (int)$data['filter_store_id'], (int)$this->config->get('config_language_id'));

        if (!empty($data['filter_updated_at_min'])) {
            $sql .= sprintf(" AND p.date_modified >= '%s'", $data['filter_updated_at_min']);
        }
        if (!empty($data['filter_updated_at_max'])) {
            $sql .= sprintf(" AND p.date_modified <= '%s'", $data['filter_updated_at_max']);
        }

        if (!empty($data['products_ids'])) {
            $sql .= "AND p.product_id IN ('". join("','", $data['products_ids']) ."')";
        }

        $sql .= " GROUP BY p.product_id ";
        $sql .= " ORDER BY p.date_modified ";
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        ControllerExtensionModuleRemarkety::debug($sql);
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getProductCategories($product_id)
    {
        $sql = sprintf("
                SELECT cd.name
                FROM " . DB_PREFIX . "product_to_category ptc
                JOIN " . DB_PREFIX . "category_description cd ON (ptc.category_id = cd.category_id)
                WHERE ptc.product_id = '%s'
                AND cd.language_id = '%s'
            "
            ,(int)$product_id, (int)$this->config->get('config_language_id'));

        $product_category_data = array();
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_category_data[] = $result['name'];
        }

        return $product_category_data;
    }

    public function markCartIfChanged()
    {
        if (!$this->_checkModuleInstalled()) {
            return false;
        }
        $customerId =  (int)$this->session->data['customer_id'];
        $query = $this->db->query("SELECT cart FROM " . DB_PREFIX . "customer  WHERE customer_id = '" .$customerId . "';" );

        if (isset($query->rows[0]['cart']) && !empty($query->rows[0]['cart'])) {
            $oldCartInfo = unserialize($query->rows[0]['cart']);
        } else {
            $oldCartInfo = array();
        }

        $newCartInfo = isset($this->session->data['cart']) ? $this->session->data['cart'] : array();

        if (count($oldCartInfo) != count($newCartInfo) || array_diff_assoc($oldCartInfo, $newCartInfo)) {
            $nowUtc = new \DateTime("now", new \DateTimeZone("UTC"));
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart_last_change='".($nowUtc->format("Y-m-d H:i:s"))."' WHERE customer_id = '" . $customerId . "'");
        }
    }

    protected function _checkModuleInstalled()
    {
        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `code` = 'remarkety'");
        return $result->num_rows ? true : false;
    }
}