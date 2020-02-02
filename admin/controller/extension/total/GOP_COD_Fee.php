<?php
/**
 * @extension-total	GOP_COD_Fee
 * @author-name		Michail Gkasios
 * @copyright		Copyright (C) 2013 GKASIOS
 * @license			GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */

class ControllerExtensionTotalGOPCODFEE extends Controller
{
	private $extension_name = "GOP_COD_Fee";

	private $extension_type = "total";

	private $extension_constant = "extension";

	private $split_char = "_";

	private $slash_char = "/";

	private $extension_path = "extension/total/GOP_COD_Fee";

	private $data_fields = ["status", "sort_order"];

	private $error_fields = ["warning", "sort_order"];

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

		$data['extension_name'] = $this->extension_name;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array	(
											'text'	=>	$this->language->get('text_home'),
											'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
										);

		$data['breadcrumbs'][] = array	(
											'text'	=>	$this->language->get('text_total'),
											'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=' . $this->extension_type, true)
										);

		$data['breadcrumbs'][] = array	(
											'text'	=>	$this->language->get('heading_title'),
											'href' => $this->url->link($this->extension_path, 'token=' . $this->session->data['token'], true)
										);

		$data['small_logo'] = "view/image/extension/payment/GKASIOS_Logo_Main_Animated.gif";
		$data['paypal_button_id'] ="TXJMRFUWHCFKG";

		$data['action'] = $this->url->link($this->extension_path, 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true);

		foreach($this->data_fields as $data_field)
		{
			$data_field_name = implode($this->split_char, [$this->extension_name, $data_field]);

			if(isset($this->request->post[$data_field_name]))
			{
				$data[$data_field_name] = $this->request->post[$data_field_name];
			}
			else
			{
				$data[$data_field_name] = $this->config->get($data_field_name);
			}
		}

		//Errors
		foreach($this->error_fields as $error_field)
		{
			$error_field_name = implode($this->split_char, [$this->extension_name, $error_field]);
			$error_field_name_error = implode($this->split_char, [$error_field_name, "error"]);

			if(isset($this->error[$error_field_name]))
			{
				$data[$error_field_name_error] = $this->error[$error_field_name];
			}
			else
			{
				$data[$error_field_name_error] = '';
			}
		}

		//-----------------------Render--------------------------

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->extension_path . 'tpl', $data));
	}

	private function validate()
	{
		$this->language->load($this->extension_path);

		if(!$this->user->hasPermission('modify', $this->extension_path))
		{
			$this->error[$this->extension_name . $this->split_char . 'warning'] = $this->language->get('error_permission');
		}

		$sort_order_constant = $this->extension_name . $this->split_char . 'sort_order';

		if($this->request->post[$sort_order_constant] != '' && !is_numeric($this->request->post[$sort_order_constant]))
		{
			$this->error[$sort_order_constant] = $this->language->get('error_sort_order');
		}

		return !$this->error;
	}
}
?>