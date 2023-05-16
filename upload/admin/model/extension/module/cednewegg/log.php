<?php
class ModelExtensionModuleCedneweggLog extends Model {


    public function deleteLog($log_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "cednewegg_logs WHERE id = '" . (int)$log_id . "'");
    }

    public function getLog($log_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_logs WHERE id = '" . (int)$log_id . "'");
        if($query->num_rows) {
            return $query->row;
        }
        return array();
    }

    public function getLogs($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "cednewegg_logs cp WHERE id >= '0'";

        $sort_data = array(
            'type',
            'method'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY cp.type";
        }

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

    public function getTotalLogs() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cednewegg_logs");
         if($query->row['total']) {
            return $query->row['total'];
        }

        return array();
    }


}
?>
