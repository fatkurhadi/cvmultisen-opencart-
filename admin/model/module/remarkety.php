<?php
class ModelModuleRemarkety extends Model
{
    private $frontInstance;
    public function fromFront()
    {
        if(!$this->frontInstance){
            include_once __DIR__.'/../../../catalog/model/module/remarkety_queue.php';
            $this->frontInstance = new ModelModuleRemarketyQueue($this->registry);
        }

        return $this->frontInstance;
    }

    public function getQueueRows($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "remarkety_queue ";

        $sql .= " ORDER BY " . $data['sort'];

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalQueueRows() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "remarkety_queue");

        return $query->row['total'];
    }
}