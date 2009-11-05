<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Filename $RCSfile: assignment_mgr.class.php,v $
 *
 * @version $Revision: 1.5 $
 * @modified $Date: 2008/10/21 17:23:53 $ by $Author: schlundus $
 * @author Francisco Mancardi
 *
 * Manager for assignment activities
 *
 * 20060908 - franciscom - 
*/
class assignment_mgr 
{
	var $db;

	function assignment_mgr(&$db) 
	{
		$this->db = &$db;
	}

	/*
	 $key_field: contains the filename that has to be used as the key of
	             the returned hash.    
	*/
	function get_available_types($key_field='description') 
	{
		static $s_hash_types;
		if (!$s_hash_types)
		{
			$sql = "SELECT * FROM assignment_types";
			$s_hash_types = $this->db->fetchRowsIntoMap($sql,$key_field);
		}
		return $s_hash_types;
	}

  /*
   $key_field: contains the name column that has to be used as the key of
               the returned hash.    
  */
	function get_available_status($key_field='description') 
	{
		static $s_hash_types;
		if (!$s_hash_types)
		{
			$sql = " SELECT * FROM assignment_status "; 
			$s_hash_types = $this->db->fetchRowsIntoMap($sql,$key_field);
		}
		
		return $s_hash_types;
	}

	// $feature_id can be an scalar or an array
	 
	function delete_by_feature_id($feature_id) 
	{
	    if( is_array($feature_id) )
	    {
			$feature_id_list = implode(",",$feature_id);
			$where_clause = " WHERE feature_id IN ($feature_id_list) ";
	    }
	    else
	    {
			$where_clause = " WHERE feature_id={$feature_id}";
	    }
		$sql = " DELETE FROM user_assignments {$where_clause}"; 
		$result = $this->db->exec_query($sql);
	}

  // $feature_map['feature_id']['user_id']
  // $feature_map['feature_id']['type']
  // $feature_map['feature_id']['status']
  // $feature_map['feature_id']['assigner_id']
  //
  //
	function assign($feature_map) 
	{
		foreach($feature_map as $feature_id => $elem)
		{
			$sql = "INSERT INTO user_assignments " .
					"(feature_id,user_id,assigner_id," .
					"type,status,creation_ts";
			
			$values = "VALUES({$feature_id},{$elem['user_id']},{$elem['assigner_id']}," .
					"{$elem['type']},{$elem['status']}," . $elem['creation_ts'];
			
			if(isset($elem['deadline_ts']) )
			{
				$sql .=",deadline_ts";
				$values .="," . $elem['deadline_ts']; 
			}     
			
			$sql .= ") " . $values . ")";
			$this->db->exec_query($sql);
		}
	}
	

  // 
  // $feature_map: key   => feature_id
  //               value => hash with optional keys 
  //                        that have the same name of user_assignment fields
  //
  //
	function update($feature_map) 
	{
	  
		foreach($feature_map as $feature_id => $elem)
		{
			$sepa = "";
			$sql = "UPDATE user_assignments SET ";
			$simple_fields = array('user_id','assigner_id','type','status');
			$date_fields = array('deadline_ts','creation_ts');  
		
			foreach($simple_fields as $idx => $field)
			{
				if(isset($elem[$field]))
				{
					$sql .= $sepa . "$field={$elem[$field]} ";
					$sepa=",";
				}
			}
			
			foreach($date_fields as $idx => $field)
			{
				if(isset($elem[$field]))
				{
					$sql .= $sepa . "$field=" . $elem[$field] . " ";
					$sepa = ",";
				}
			}
			
			$sql .= "WHERE feature_id={$feature_id}";
			
			$this->db->exec_query($sql);
		}
	}
}
?>