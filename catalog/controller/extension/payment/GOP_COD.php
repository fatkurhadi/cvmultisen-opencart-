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

	private $split_char = "_";

	private $slash_char = "/";

	public function index()
	{
		$extension_path = implode($this->slash_char, [$this->extension_constant, $this->extension_type, $this->extension_name]);

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['continue'] = $this->url->link('checkout/success');

		return $this->load->view($extension_path, $data);
	}

	public function confirm()
	{
		if($this->session->data['payment_method']['code'] == $this->extension_name)
		{
			$this->load->model('checkout/order');
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->session->data['payment_method']['order_status_id']);
		}
	}
}
?>