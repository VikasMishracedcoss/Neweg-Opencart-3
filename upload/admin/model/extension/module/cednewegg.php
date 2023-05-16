<?php
class ModelExtensionModuleCedNewEgg extends Model {
	public function add($key){
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = '" . $this->db->escape($key) . "' ");
		$this->add_account_details($key);
		if(!$result->num_rows){
			$this->add_account_details($key);
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'cedebay_lic', `key` = '" . $this->db->escape($key) . "', `value` = '1', serialized = '1'");
		}
	}

	public function account_details($key){
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_accounts WHERE `id` = '1' ");
		if($result->num_rows > 0){
			return $result->row;
		}
	}

	public function accountdetails($key){
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_accounts WHERE `id` = '1' ");
		if($result->num_rows > 0){
			return $result->rows;
		}
	}

	public function add_account_details($data)
	{
      //print_r($data); die(__DIR__);

   $this->db->query("UPDATE " . DB_PREFIX . "cednewegg_accounts SET `account_code`='".$data['cednewegg_account_code']."',`account_title`='".$data['cednewegg_title']."', `account_location`='".$data['cednewegg_account_location']."', `account_store`='0', `account_status`='".$data['cednewegg_status']."', `root_cat`='".$data['cednewegg_root_category']."', `seller_id`='".$data['cednewegg_seller_id']."', `secret_key`='".$data['cednewegg_secret_key']."', `authorization_key`='".$data['cednewegg_auth_key']."', `warehouse_location`='".$data['cednewegg_warehouse_location']."'  WHERE `id` = '1'");

   /* $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_accounts ( `account_code`, `account_location`, `account_store`, `account_status`, `root_cat`, `seller_id`, `secret_key`, `authorization_key`, `warehouse_location`) VALUES (
	'".$data['cednewegg_account_code']."',
	'".$data['cednewegg_account_location']."',
	'0','".$data['cednewegg_status']."',
	'".$data['cednewegg_root_category']."',
    '".$data['cednewegg_seller_id']."',
    '".$data['cednewegg_secret_key']."', 
    '".$data['cednewegg_auth_key']."', 
    '".$data['cednewegg_warehouse_location']."')");*/

	}


	public function remove($key){
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '" . $this->db->escape($key) . "' ");
	}
}
