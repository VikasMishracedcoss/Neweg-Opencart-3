<?php 
/**
* CedCommerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the End User License Agreement (EULA)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://cedcommerce.com/license-agreement.txt
*
* @category  modules
* @package   CedCatch
* @author    CedCommerce Core Team 
* @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license   http://cedcommerce.com/license-agreement.txt
*/
class ModelExtensionModuleCedNewEggCron extends Model
{
	protected $error=array();

	public function getallcron($data = array()) { 
		
		$sql = "SELECT * FROM `" . DB_PREFIX . "cednewegg_cron`";
        if($data['filter_date_added']!='' || $data['filter_date_modified']!='' || $data['filter_status']!='' || $data['filter_job_code']!='' ){ 
		 	$sql .= " WHERE ";
		 }
		if (!empty($data['filter_date_added'])) {
			$sql .= " DATE(created_at) >= DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(finished_at) <= DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}
		if (!empty($data['filter_status'])) {
			$sql .= " LIKE '%" . $this->db->escape($data['filter_status']) . "%'";
		}
		if (!empty($data['filter_job_code'])) {
			$sql .= " job_code LIKE '%" . $this->db->escape($data['filter_job_code']) . "%'";
		}
		if (!empty($data['filter_total'])) {
			$sql .= "'" . (float)$data['filter_total'] . "'";
		}
		$sort_data = array(
			'id',
			'job_code',
			'status',
			'date_added',
			'date_modified',
			'total'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
		}
		if (isset($data['id']) && ($data['id'] == 'DESC')) {
			$sql .= " DESC ";
		} else {
			$sql .= " ASC ";
		}

		if(isset($data['start']) || isset($data['limit'])) {
			  if($data['start'] < 0) {
				$data['start'] = 0;
			    }
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		//echo $sql; die(); 
		$query = $this->db->query($sql);
		//print_r($query->rows); die();
		return $query->rows;
	}
	

	public function get_total_cron()
    {   
        $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "cednewegg_cron");
        if($query->num_rows) {
            return $query->num_rows;
        }
        return null;
    }
	public function getCronjobcode($job_code)
    {
        $order_query = $this->db->query("SELECT `job_code` FROM `" . DB_PREFIX . "cednewegg_cron` 
        WHERE id = '" . (int)$id . "'");
        if($order_query->num_rows) {
            return $order_query->row['newegg_order_id'];
        }
        return null;

    }
	public function getcronbyid($id) { 
		//echo $order_id; die();
		$order_query = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "cednewegg_cron` WHERE id = '" . (int)$id . "'");

		if ($order_query->num_rows) {
		    return $order_query->num_rows;
		} else {
			return array();
		}
	}
	
}