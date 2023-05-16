<?php

class ModelExtensionModuleCedNewEggTemplate extends Model
{

    public function addTemplate($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_description_templates SET 
		   template_name = '" . $data['template_name'] . "',
			template = '" . $this->db->escape($data['template_data']) . "'
			");
        $template_id = $this->db->getLastId();
        $this->cache->delete('template');
    }

    public function editTemplate($template_id, $data)
    {
         $this->db->query("UPDATE " . DB_PREFIX . "cednewegg_description_templates SET 
	        template_name = '" . $data['template_name'] . "',
			template = '" . $this->db->escape($data['template_data']) . "'
			WHERE id = '" . (int)$template_id . "'");

        $this->cache->delete('template');
    }

    public function deleteTemplate($template_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "cednewegg_description_templates WHERE id = '" . (int)$template_id . "'");
        $this->cache->delete('template');
    }

    public function getTemplate($template_id)
    {
        $data = array(
            'template_name' => '',
            'template_data' => '',
        );
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_description_templates WHERE id = '" . (int)$template_id . "'");
        $result = $query->row;
        if (isset($result['template_name'])) {
            $data['template_name'] = $result['template_name'];
        }
        if (isset($result['template'])) {
            $data['template_data'] = $result['template'];
        }

        return $data;
    }



    public function getTemplates($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "cednewegg_description_templates cp WHERE id >= '0'";

        $sort_data = array(
            'template_name',
            'template',
            'id'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY cp.template_name";
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

    public function getTotalTemplates()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cednewegg_description_templates");

        return $query->row['total'];
    }


}

?>
