<?php
namespace Cart;
class indo {
	private $weights = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');

		$indo_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "indo_class wc LEFT JOIN " . DB_PREFIX . "indo_class_description wcd ON (wc.indo_class_id = wcd.indo_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($indo_class_query->rows as $result) {
			$this->weights[$result['indo_class_id']] = array(
				'indo_class_id' => $result['indo_class_id'],
				'title'           => $result['title'],
				'unit'            => $result['unit'],
				'value'           => $result['value']
			);
		}
	}

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->weights[$from])) {
			$from = $this->weights[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->weights[$to])) {
			$to = $this->weights[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $indo_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->weights[$indo_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$indo_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($indo_class_id) {
		if (isset($this->weights[$indo_class_id])) {
			return $this->weights[$indo_class_id]['unit'];
		} else {
			return '';
		}
	}
}