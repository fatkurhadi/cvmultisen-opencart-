<?php
/**
 * @extension-total	GOP_COD_Fee
 * @author-name		Michail Gkasios
 * @copyright		Copyright (C) 2013 GKASIOS
 * @license			GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */

class ModelExtensionTotalGOPCODFEE extends Model
{
	private $extension_name = "GOP_COD_Fee";

	private $extension_parent_name = "GOP_COD";

	private $extension_type = "total";

	private $extension_constant = "extension";

	private $split_char = "_";

	private $slash_char = "/";

	private $extension_path = "extension/total/GOP_COD_Fee";

	public function getTotal($total)
	{
		if(isset($this->session->data['payment_method']['code']))
		{
			if($this->session->data['payment_method']['code'] == $this->extension_parent_name && $this->session->data['payment_method']['order_total'] == true)
			{
				$title = $this->session->data['payment_method']['order_total_title'];
				if($title == null)
				{
					$this->language->load($this->extension_path);
					$title = $this->language->get('text_cod_fee');
				}

				$sort_order = $this->session->data['payment_method']['order_total_sort_order'];
				if($sort_order == null)
				{
					$sort_order = $this->config->get($this->extension_name . $this->split_char . 'sort_order');
				}

				$total['totals'][] = array	(
												'code'			=>	$this->extension_name,
												'title'			=>	$title,
												'text'			=>	$this->formatCurrency($this->session->data['payment_method']['cost']),
												'value'			=>	$this->session->data['payment_method']['cost'],
												'sort_order'	=>	$sort_order
											);

				if($this->session->data['payment_method']['tax_class_id'])
				{
					$tax_rates = $this->tax->getRates($this->session->data['payment_method']['cost'], $this->session->data['payment_method']['tax_class_id']);

					foreach($tax_rates as $tax_rate)
					{
						if(!isset($total['taxes'][$tax_rate['tax_rate_id']]))
						{
							$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
						}
						else
						{
							$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
						}
					}
				}

				$total['total'] += $this->session->data['payment_method']['cost'];
			}
		}
	}

	private function formatCurrency($amount)
	{
		$currency = $this->session->data['currency'];

		return $this->currency->format($amount, $currency, '', true);
	}
}
?>