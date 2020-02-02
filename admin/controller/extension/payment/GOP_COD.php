<?php
/**
 * @extension-payment	GOP_COD
 * @author-name			Michail Gkasios
 * @copyright			Copyright (C) 2013 GKASIOS
 * @license				GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */

class ControllerExtensionPaymentGOPCOD extends Controller
{
	private $extension_name = "GOP_COD";

	private $extension_type = "payment";

	private $extension_constant = "extension";

	private $extension_default = "default";

	private $split_char = "_";

	private $slash_char = "/";

	private $extension_path = "extension/payment/GOP_COD";

	private $general_data_fields = ["status", "shipping_geo_zone", "sort_order"];

	private $geo_data_fields = ["tax_class_id", "method", "flat", "flat_currency", "percent", "custom", "enable_rule", "order_status_id", "order_total", "order_total_sort_order", "status"];

	private $geo_error_fields = ["flat", "percent", "order_total_sort_order"];

	private $error = array();

	public function index()
	{
		$this->load->model('setting/setting');

		if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
		{
			$this->model_setting_setting->editSetting($this->extension_name, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=' . $this->extension_type, true));
		}

		//Language Loading
		$data = array();
		$data += $this->language->load($this->extension_path);

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array	(
											'text' => $this->language->get('text_home'),
											'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
										);

		$data['breadcrumbs'][] = array	(
											'text' => $this->language->get('text_payment'),
											'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=' . $this->extension_type, true)
										);

		$data['breadcrumbs'][] = array	(
											'text' => $this->language->get('heading_title'),
											'href' => $this->url->link($this->extension_path, 'token=' . $this->session->data['token'], true)
										);

		$data['small_logo'] = "view/image/extension/payment/GKASIOS_Logo_Main_Animated.gif";
		$data['paypal_button_id'] = "TXJMRFUWHCFKG";

		$data['action'] = $this->url->link($this->extension_path, 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=' . $this->extension_type, true);

		$this->load->model('localisation/geo_zone');

		$geo_zones = Array(0 => Array("geo_zone_id" => 0, "name" => $data['text_all_zones'], "description" => $data['text_all_zones'], "date_modified" => '0000-00-00 00:00:00', "date_added" => '0000-00-00 00:00:00'));
		$geo_zones = array_merge($geo_zones, $this->model_localisation_geo_zone->getGeoZones());
		$data['geo_zones'] = $geo_zones;

		$this->load->model('customer/customer_group');

		$customer_groups = $this->model_customer_customer_group->getCustomerGroups();

		$data['customer_groups'] = $customer_groups;

		$this->load->model('extension/extension');

		$extensions = $this->model_extension_extension->getInstalled('shipping');

		foreach($extensions as $key => $value)
		{
			if(!file_exists(DIR_APPLICATION . 'controller/extension/shipping/' . $value . '.php'))
			{
				$this->model_setting_extension->uninstall('shipping', $value);
				unset($extensions[$key]);
			}
		}

		$data['extensions'] = array();
		$data['extensions'][] = array	(
											'name'	=>	'noshipping',
											'title'	=>	$this->language->get('tab_noshipping')
										);

		$files = glob(DIR_APPLICATION . 'controller/extension/shipping/*.php');

		if($files)
		{
			foreach($files as $file)
			{
				$extension = basename($file, '.php');
				$this->language->load('extension/shipping/' . $extension);

				if(in_array($extension, $extensions))
				{
					$data['extensions'][] = array	(
														'name'	=>	$extension,
														'title'	=>	$this->language->get('heading_title')
													);
				}
			}
		}

		$geo_zones = Array(0 => Array("geo_zone_id" => 0, "name" => $data['text_all_zones'], "description" => $data['text_all_zones'], "date_modified" => '0000-00-00 00:00:00', "date_added" => '0000-00-00 00:00:00'));
		$geo_zones = array_merge($geo_zones, $this->model_localisation_geo_zone->getGeoZones());

		$this->language->load($this->extension_path);

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency");

		$currencies = array();
		foreach($query->rows as $result)
		{
			$currencies[$result['code']] = array(
													'currency_id'	=>	$result['currency_id'],
													'title'			=>	$result['title'],
													'symbol_left'	=>	$result['symbol_left'],
													'symbol_right'	=>	$result['symbol_right'],
													'decimal_place'	=>	$result['decimal_place'],
													'value'			=>	$result['value']
												);
		}

		$data['currencies'] = $currencies;

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


		//----------------------Data-----------------------

		//Default

		$extension_status_field_name = implode($this->split_char, [$this->extension_name, "status"]);

		if(isset($this->request->post['GOP_COD_status']))
		{
			$data[$extension_status_field_name] = $this->request->post[$extension_status_field_name];
		}
		else
		{
			$data[$extension_status_field_name] = $this->config->get($extension_status_field_name);
		}

		foreach($this->general_data_fields as $general_data_field)
		{
			$general_data_field_name = implode($this->split_char, [$this->extension_name, $this->extension_default, $general_data_field]);

			if(isset($this->request->post[$general_data_field_name]))
			{
				$data[$general_data_field_name] = $this->request->post[$general_data_field_name];
			}
			else
			{
				$data[$general_data_field_name] = $this->config->get($general_data_field_name);
			}
		}

		foreach($geo_zones as $geo_zone)
		{
			foreach($customer_groups as $customer_group)
			{
				foreach($this->geo_data_fields as $geo_data_field)
				{
					$geo_data_field_name = implode($this->split_char, [$this->extension_name, $this->extension_default, $geo_zone['geo_zone_id'], $customer_group['customer_group_id'], $geo_data_field]);

					if(isset($this->request->post[$geo_data_field_name]))
					{
						$data[$geo_data_field_name] = $this->request->post[$geo_data_field_name];
					}
					else
					{
						$data[$geo_data_field_name] = $this->config->get($geo_data_field_name);
					}
				}
			}
		}

		//Extension

		foreach($data['extensions'] as $extension)
		{
			foreach($this->general_data_fields as $general_data_field)
			{
				$general_data_field_name = implode($this->split_char, [$this->extension_name, $extension['name'], $general_data_field]);

				if(isset($this->request->post[$general_data_field_name]))
				{
					$data[$general_data_field_name] = $this->request->post[$general_data_field_name];
				}
				else
				{
					$data[$general_data_field_name] = $this->config->get($general_data_field_name);
				}
			}

			foreach($geo_zones as $geo_zone)
			{
				foreach($customer_groups as $customer_group)
				{
					foreach($this->geo_data_fields as $geo_data_field)
					{
						$geo_data_field_name = implode($this->split_char, [$this->extension_name, $extension['name'], $geo_zone['geo_zone_id'], $customer_group['customer_group_id'], $geo_data_field]);

						if(isset($this->request->post[$geo_data_field_name]))
						{
							$data[$geo_data_field_name] = $this->request->post[$geo_data_field_name];
						}
						else
						{
							$data[$geo_data_field_name] = $this->config->get($geo_data_field_name);
						}
					}
				}
			}
		}

		//----------------------Errors-----------------------

		$extension_warning_field_name = implode($this->split_char, [$this->extension_name, "warning"]);
		$extension_warning_field_name_error = implode($this->split_char, [$extension_warning_field_name, "error"]);

		if(isset($this->error[$extension_warning_field_name]))
		{
			$data[$extension_warning_field_name_error] = $this->error[$extension_warning_field_name];
		}
		else
		{
			$data[$extension_warning_field_name_error] = '';
		}

		//Default

		$extension_default_sort_order_field_name = implode($this->split_char, [$this->extension_name, $this->extension_default, "sort_order"]);
		$extension_default_sort_order_field_name_error = implode($this->split_char, [$extension_default_sort_order_field_name, "error"]);

		if(isset($this->error[$extension_default_sort_order_field_name]))
		{
			$data[$extension_default_sort_order_field_name_error] = $this->error[$extension_default_sort_order_field_name];
		}
		else
		{
			$data[$extension_default_sort_order_field_name_error] = '';
		}

		$geo_zones = Array(0 => Array("geo_zone_id" => 0, "name" => $data['text_all_zones'], "description" => $data['text_all_zones'], "date_modified" => '0000-00-00 00:00:00', "date_added" => '0000-00-00 00:00:00'));
		$geo_zones = array_merge($geo_zones, $this->model_localisation_geo_zone->getGeoZones());

		foreach($geo_zones as $geo_zone)
		{
			foreach($customer_groups as $customer_group)
			{
				foreach($this->geo_error_fields as $geo_error_field)
				{
					$geo_error_field_name = implode($this->split_char, [$this->extension_name, $this->extension_default, $geo_zone['geo_zone_id'], $customer_group['customer_group_id'], $geo_error_field]);
					$geo_error_field_name_error = implode($this->split_char, [$geo_error_field_name, "error"]);

					if(isset($this->error[$geo_error_field_name]))
					{
						$data[$geo_error_field_name_error] = $this->error[$geo_error_field_name];
					}
					else
					{
						$data[$geo_error_field_name_error] = '';
					}
				}
			}
		}

		//Extensions

		foreach($data['extensions'] as $extension)
		{
			$extension_extension_sort_order_field_name = implode($this->split_char, [$this->extension_name, $extension['name'], "sort_order"]);
			$extension_extension_sort_order_field_name_error = implode($this->split_char, [$extension_extension_sort_order_field_name, "error"]);

			if(isset($this->error[$extension_extension_sort_order_field_name]))
			{
				$data[$extension_extension_sort_order_field_name_error] = $this->error[$extension_extension_sort_order_field_name];
			}
			else
			{
				$data[$extension_extension_sort_order_field_name_error] = '';
			}

			foreach($geo_zones as $geo_zone)
			{
				foreach($customer_groups as $customer_group)
				{
					foreach($this->geo_error_fields as $geo_error_field)
					{
						$geo_error_field_name = implode($this->split_char, [$this->extension_name, $extension['name'], $geo_zone['geo_zone_id'], $customer_group['customer_group_id'], $geo_error_field]);
						$geo_error_field_name_error = implode($this->split_char, [$geo_error_field_name, "error"]);

						if(isset($this->error[$geo_error_field_name]))
						{
							$data[$geo_error_field_name_error] = $this->error[$geo_error_field_name];
						}
						else
						{
							$data[$geo_error_field_name_error] = '';
						}
					}
				}
			}
		}

		//-----------------------Render--------------------------

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/GOP_COD.tpl', $data));
	}

	private function validate()
	{
		$this->language->load($this->extension_path);

		$this->load->model('extension/extension');

		$extensions = $this->model_extension_extension->getInstalled('shipping');

		foreach($extensions as $key => $value)
		{
			if(!file_exists(DIR_APPLICATION . 'controller/extension/shipping/' . $value . '.php'))
			{
				$this->model_setting_extension->uninstall('shipping', $value);
				unset($extensions[$key]);
			}
		}

		$data['extensions'] = array();
		$data['extensions'][] = array	(
											'name'	=>	'noshipping',
											'title'	=>	$this->language->get('tab_noshipping'),
										);

		$files = glob(DIR_APPLICATION . 'controller/extension/shipping/*.php');

		if($files)
		{
			foreach($files as $file)
			{
				$extension = basename($file, '.php');

				$this->language->load('shipping/' . $extension);
				if(in_array($extension, $extensions))
				{

					$data['extensions'][] = array	(
														'name'	=>	$extension,
														'title'	=>	$this->language->get('heading_title')
													);
				}
			}
		}

		$this->language->load($this->extension_path);

		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$this->load->model('customer/customer_group');

		$customer_groups = $this->model_customer_customer_group->getCustomerGroups();

		$this->load->model('localisation/geo_zone');

		$geo_zones = Array(0 => Array( "geo_zone_id" => 0, "name" => $data['text_all_zones'], "description" => $data['text_all_zones'], "date_modified" => '0000-00-00 00:00:00', "date_added" => '0000-00-00 00:00:00'));
		$geo_zones = array_merge($geo_zones, $this->model_localisation_geo_zone->getGeoZones());

		if(!$this->user->hasPermission('modify', $this->extension_path))
		{
			$this->error[implode($this->split_char, [$this->extension_name, "warning"])] = $this->language->get('error_permission');
		}

		//Default

		$extension_default_sort_order_field_name = implode($this->split_char, [$this->extension_name, $this->extension_default, "sort_order"]);

		if(isset($this->request->post[$extension_default_sort_order_field_name]) && !is_numeric($this->request->post[$extension_default_sort_order_field_name]) && $this->request->post[$extension_default_sort_order_field_name] != '')
		{
			$this->error[$extension_default_sort_order_field_name] = $this->language->get('error_number');
		}

		foreach($geo_zones as $geo_zone)
		{
			foreach($customer_groups as $customer_group)
			{
				$extension_default_geo_field_name = implode($this->split_char, [$this->extension_name, $this->extension_default, $geo_zone['geo_zone_id'], $customer_group['customer_group_id']]);

				$extension_default_geo_field_name_flat = implode($this->split_char, [$extension_default_geo_field_name, "flat"]);

				if(isset($this->request->post[$extension_default_geo_field_name_flat]) && !is_numeric($this->request->post[$extension_default_geo_field_name_flat]) && $this->request->post[$extension_default_geo_field_name_flat] != '')
				{
					$this->error[$extension_default_geo_field_name_flat] = $this->language->get('error_number');
				}

				$extension_default_geo_field_name_percent = implode($this->split_char, [$extension_default_geo_field_name, "percent"]);

				if(isset($this->request->post[$extension_default_geo_field_name_percent]) && !is_numeric($this->request->post[$extension_default_geo_field_name_percent]) && $this->request->post[$extension_default_geo_field_name_percent] != '')
				{
					$this->error[$extension_default_geo_field_name_percent] = $this->language->get('error_number');
				}

				$extension_default_geo_field_name_order_total_sort_order = implode($this->split_char, [$extension_default_geo_field_name, "order_total_sort_order"]);

				if(isset($this->request->post[$extension_default_geo_field_name_order_total_sort_order]) && !is_numeric($this->request->post[$extension_default_geo_field_name_order_total_sort_order]) && $this->request->post[$extension_default_geo_field_name_order_total_sort_order] != '')
				{
					$this->error[$extension_default_geo_field_name_order_total_sort_order] = $this->language->get('error_number');
				}
			}
		}

		//Extensions

		foreach($data['extensions'] as $extension)
		{
			$extension_extension_sort_order_field_name = implode($this->split_char, [$this->extension_name, $extension['name'], "sort_order"]);

			if(isset($this->request->post[$extension_extension_sort_order_field_name]) && !is_numeric($this->request->post[$extension_extension_sort_order_field_name]) && $this->request->post[$extension_extension_sort_order_field_name] != '')
			{
				$this->error[$extension_extension_sort_order_field_name] = $this->language->get('error_number');
			}

			foreach($geo_zones as $geo_zone)
			{
				foreach($customer_groups as $customer_group)
				{
					$extension_extension_geo_field_name = implode($this->split_char, [$this->extension_name, $extension['name'], $geo_zone['geo_zone_id'], $customer_group['customer_group_id']]);

					$extension_extension_geo_field_name_flat = implode($this->split_char, [$extension_extension_geo_field_name, "flat"]);

					if(isset($this->request->post[$extension_extension_geo_field_name_flat]) && !is_numeric($this->request->post[$extension_extension_geo_field_name_flat]) && $this->request->post[$extension_extension_geo_field_name_flat] != '' && $this->request->post[$this->extension_name . $this->split_char . $extension['name'] . $this->split_char . $geo_zone['geo_zone_id'] . $this->split_char . $customer_group['customer_group_id'] . '_method'] == 1)
					{
						$this->error[$extension_extension_geo_field_name_flat] = $this->language->get('error_number');
					}

					$extension_extension_geo_field_name_percent = implode($this->split_char, [$extension_extension_geo_field_name, "percent"]);

					if(isset($this->request->post[$extension_extension_geo_field_name_percent]) && !is_numeric($this->request->post[$extension_extension_geo_field_name_percent]) && $this->request->post[$extension_extension_geo_field_name_percent] != '' && $this->request->post[$this->extension_name . $this->split_char . $extension['name'] . $this->split_char . $geo_zone['geo_zone_id'] . $this->split_char . $customer_group['customer_group_id'] . '_method'] == 2)
					{
						$this->error[$extension_extension_geo_field_name_percent] = $this->language->get('error_number');
					}

					$extension_extension_geo_field_name_order_total_sort_order = implode($this->split_char, [$extension_extension_geo_field_name, "order_total_sort_order"]);

					if(isset($this->request->post[$extension_extension_geo_field_name_order_total_sort_order]) && !is_numeric($this->request->post[$extension_extension_geo_field_name_order_total_sort_order]) && $this->request->post[$extension_extension_geo_field_name_order_total_sort_order] != '')
					{
						$this->error[$extension_extension_geo_field_name_order_total_sort_order] = $this->language->get('error_number');
					}
				}
			}
		}

		return !$this->error;
	}

	public function confirm()
	{
		$this->load->model('checkout/order');
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
	}

	public function install()
	{
	}

	public function uninstall()
	{
	}
}
?>