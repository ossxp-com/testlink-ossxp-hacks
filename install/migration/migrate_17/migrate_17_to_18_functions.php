<?php
/*
TestLink Open Source Project - http://testlink.sourceforge.net/
$Id: migrate_17_to_18_functions.php,v 1.9.2.2 2009/08/30 00:56:03 havlat Exp $ 

Support function for migration from 1.7.2 to 1.8.0

Author: Francisco Mancardi (francisco.mancardi@gmail.com)

rev: 
     20090717 - franciscom - updateTestCaseExternalID() - now external ID will
                             be aligned with internal ID.
                             There is code commented to allow users to use old method
                             where external ID was computed fresh from scratch.
                             Some times users do not like this solution because
                             breaks links done with other applications.
                              
     20090714 - franciscom - refactoring of updateTestCaseExternalID() to improve
                             speed and memory usage (issue SubQuery).
     20090127 - franciscom - added checkTableFields()
     20081210 - BUGID 1921 - missing update of attachment table
     added updateExecutionsTCVersionInfo()
*/
?>

<?php
/*
  function: 

  args:
  
  returns: 

*/
function reqSpecMigration(&$source_db,&$treeMgr)
{
	$hhmmss=date("H:i:s");
	$msg_click_to_show="click to show";
	echo "<a onclick=\"return DetailController.toggle('details-req_spec_table')\" href=\"tplan/\">
	<img src='../../img/icon-foldout.gif' align='top' title='show/hide'> Requirement Specification: {$msg_click_to_show} {$hhmmss}</a>";
	echo '<div class="detail-container" id="details-req_spec_table" style="display: none;">';
	
  $sql="SELECT * from req_specs";
	$rspec=$source_db->fetchRowsIntoMap($sql,'id');
	if(is_null($rspec)) 
	{
		echo "<span class='notok'>There are no req specs to be migrated!</span></b>";
	}
	else
	{
	  $mapping_old_new->req_spec=migrateReqSpecs($source_db,$treeMgr,$rspec);
	}
	echo "</div><p>";
	return $mapping_old_new;
}

/*
  function: migrateReqSpecs

  args:
  
  returns: 

*/
function migrateReqSpecs(&$source_db,&$treeMgr,&$rspec)
{
    $oldNewMapping=array();
    $mappingDescrID=$treeMgr->get_available_node_types();
    $counter=0;
    $rspec_qty=count($rspec);
    echo "<pre>Number of Requirements Specifications (SRS): " . $rspec_qty; echo "</pre>";
        
    foreach($rspec as $req_id => $rdata)
    {
        $nodeID=$treeMgr->new_node($rdata['testproject_id'],
                                   $mappingDescrID['requirement_spec'],$rdata['title']);
        $oldNewMapping[$req_id]=$nodeID;
    }
    
    return $oldNewMapping;  
} // end function


/*
  function: requirementsMigration

  args:
  
  returns: 

*/
function requirementsMigration(&$source_db,&$treeMgr,&$oldNewMapping)
{
  	$msg_click_to_show="click to show";
	  $hhmmss=date("H:i:s");
	  echo "<a onclick=\"return DetailController.toggle('details-reqtable')\" href=\"tplan/\">
	  <img src='../../img/icon-foldout.gif' align='top' title='show/hide'> Requirements: {$msg_click_to_show} {$hhmmss}</a>";
	  echo '<div class="detail-container" id="details-reqtable" style="display: none;">';
    
	  $sql="SELECT * from requirements";
	  $req=$source_db->fetchRowsIntoMap($sql,'id');
	  if(is_null($req)) 
	  {
	  	echo "<span class='notok'>There are no requirements to be migrated!</span></b>";
	  }
	  else
	  {
	    $oldNewMapping=migrateRequirements($source_db,$treeMgr,$req,$oldNewMapping);
	  }
	  echo "</div><p>";
	  
	  return $oldNewMapping;
}

/*
  function: migrateRequirements

  args:
  
  returns: 

*/
function migrateRequirements(&$source_db,&$treeMgr,&$req,&$oldNewMapping)
{
  
    $mappingDescrID=$treeMgr->get_available_node_types();
    $req_qty=count($req);
    echo "<pre>Number of requirements: " . $req_qty; echo "</pre>";
       
    foreach($req as $req_id => $rdata)
    {
        $parentID=$oldNewMapping->req_spec[$rdata['srs_id']];
        $nodeID=$treeMgr->new_node($parentID,
                                   $mappingDescrID['requirement'],
                                   $rdata['title'],$rdata['node_order']);
        $oldNewMapping->req[$req_id]=$nodeID;
    }
    return $oldNewMapping;  
} // end function


/*
  function: updateReqInfo

  args:
  
  returns: 

*/
function updateReqInfo(&$source_db,&$treeMgr,&$oldNewMapping)
{

    $sql="SELECT id,srs_id FROM requirements ";
	  $requirements=$source_db->fetchRowsIntoMap($sql,'id');

    // Update ID in descending order to avoid wrong replacement
    // because we can not be certain that new generated ID will
    // be crash with old IDs.
    //
    // krsort
    krsort($oldNewMapping->req_spec);
    krsort($oldNewMapping->req);

    foreach($oldNewMapping->req_spec as $oldID => $newID)
    {
        $sql="UPDATE req_specs " .
             "SET id={$newID} WHERE id={$oldID}";
        $source_db->exec_query($sql);       
    } 

    foreach($oldNewMapping->req as $oldID => $newID)
    {
        $parentID=$oldNewMapping->req_spec[$requirements[$oldID]['srs_id']];
        
        $sql="UPDATE requirements " .
             " SET id={$newID}, srs_id={$parentID} " .
             " WHERE id={$oldID}";
        $source_db->exec_query($sql);       
        
        $sql="UPDATE req_coverage " .
             " SET req_id={$newID} " .
             " WHERE req_id={$oldID}";
        $source_db->exec_query($sql);       

        // BUGID - Missing update of attachments
        $sql="UPDATE attachments " .
             "SET fk_id={$newID} ".
             " WHERE fk_id={$oldID} AND fk_table='requirements'";
        $source_db->exec_query($sql);       
        
    } 
}


/*
  function: updateTProjectInfo

  args:
  
  returns: 

*/
function updateTProjectInfo(&$source_db,&$tprojectMgr)
{
    $all_tprojects=$tprojectMgr->get_all();
    if( !is_null($all_tprojects) )
    {
        initNewTProjectProperties($source_db,$all_tprojects,$tprojectMgr);  
        updateTestCaseExternalID($source_db,$all_tprojects,$tprojectMgr);
    }
  
}


/*
  function: initNewTProjectProperties

  args:
  
  returns: 

*/
function initNewTProjectProperties(&$db,&$tprojectMap,&$tprojectMgr)
{
    if( !is_null($tprojectMap) )
    {
        // test case prefix
        foreach($tprojectMap as $key => $value)
        {
            // More human friendly
            // $tcPrefix=trim(substr($value['name'],0,5) . " (ID={$value['id']})"); 
            // 16 -> maxsize for prefix
            $tcPrefix=trim(substr($value['name'],0,8));
            $sql="UPDATE testprojects " .
                 "SET prefix='" . $db->prepare_string($tcPrefix) ."', " . 
                 "    tc_counter=0 " .
                 "WHERE id={$value['id']}";
            $db->exec_query($sql);     
        }  
    }
}


/*
  function: updateTestCaseExternalID

  args:
  
  returns: 

*/ 
function updateTestCaseExternalID(&$db,&$all_tprojects,&$tprojectMgr)
{
    echo "Update Test Case ExternalID <br>";
    $CUMULATIVE=1;
    
    $show_memory=true && function_exists('memory_get_usage') && function_exists('memory_get_peak_usage');
    if( !is_null($all_tprojects) )
    {
        $numtproject=count($all_tprojects);
        echo "Total number of Test Projects to process: {$numtproject}<br>";
        ob_flush();flush();
        $feedback_counter=0;
        $tproject_counter=0;
        
        foreach($all_tprojects as $tproject_key => $tproject_value)
        {
            $eid=0;
            $feedback_counter=0;
            $tproject_counter++;
            $tcaseSet = array();
            $tprojectMgr->get_all_testcases_id($tproject_value['id'],$tcaseSet);
            echo "Working on Test Project ({$tproject_counter}/{$numtproject}) : {$tproject_value['name']}<br>";
            if( $show_memory)
            {
               echo "(Memory Usage: ".memory_get_usage() . " | Peak: " . memory_get_peak_usage() . ")<br><br>";
            }
            ob_flush();flush();

            if( !is_null($tcaseSet) && ($numtc=count($tcaseSet)) > 0 )
            {
               $do_feedback=$numtc > 100;
               echo "Test Cases to process: {$numtc}<br><br>";
               ob_flush();flush();

               // Now get test case children => tcase version
               // Added order by clause
               $inClause=implode(",",$tcaseSet);
               $sql="SELECT id,parent_id AS testcase FROM nodes_hierarchy " . 
                    " WHERE parent_id IN ({$inClause}) ORDER BY testcase";
               $rs=$db->fetchColumnsIntoMap($sql,'testcase','id',$CUMULATIVE);
               foreach($rs as $tcaseID => $tcversionSet)
               {
                   $feedback_counter++;
                   $eid++;
                   $inClause=implode(",",$tcversionSet);
                   
                   // SOLUTION 1
                   // with this sql external ID will be a new number, but will be
                   // progressive
                   // $sql="UPDATE tcversions SET tc_external_id={$eid} " .
                   //      "WHERE id IN ($inClause)";
                   //
                   // SOLUTION 2
                   // With this sql external ID will be setted to internal ID     
                   $sql="UPDATE tcversions SET tc_external_id={$tcaseID} " .
                        "WHERE id IN ($inClause)";
                        
                        
                   $db->exec_query($sql);
                   
                   if( $do_feedback && $feedback_counter%100 == 0)
                   {
                       echo "Test Cases Processed: {$feedback_counter} - " . date("H:i:s") . "<br>";
                       ob_flush();flush();
                   }
               }         
               echo "ALL Test Cases Processed: {$feedback_counter} - " . date("H:i:s") ."<br><br>";
 
  
               // SOLUTION 1
               // $sql="UPDATE testprojects " .
               //      "SET tc_counter={$eid} " .
               //      "WHERE id={$tproject_value['id']}";
               // 
               // SOLUTION 2
               $testCaseIdentity=array_keys($rs);
               asort($testCaseIdentity);
               $maxTestCaseNumber = end($testCaseIdentity)+1 ;
               $sql = " UPDATE testprojects SET tc_counter = {$maxTestCaseNumber} " .
                      " WHERE id = {$tproject_value['id']} ";

               $db->exec_query($sql);
            }
           unset($tcaseSet);
        }
    }
  
}

/*
  function: updateExecutionsTCVersionInfo

  args:
  
  returns: 

*/ 
function updateExecutionsTCVersionInfo(&$db)
{
	if (!isset($cfg['db_type']) || strtolower($cfg['db_type']) == 'postgres') 
	{
		// Bug #2325
    	$sql = "UPDATE executions SET tcversion_number = " .
    	 "(SELECT version FROM tcversions WHERE id = executions.tcversion_id)";
	} else {
    	$sql = "UPDATE executions E,tcversions TCV " .
			"SET tcversion_number=TCV.version " .
			"WHERE TCV.id = E.tcversion_id";
	}
    $db->exec_query($sql);
}


/*
  function: checkTableFields

  args: adoObj: reference to ado object
        table: table name
        fields2check: array with field names
  
  returns: array($status_ok,$msg)
  
  rev: 20090127 - franciscom

*/ 
function checkTableFields(&$adoObj,$table,$fields2check)
{
    $status_ok=true;
    $msg='';
    $fields=$adoObj->MetaColumns($table);
    foreach($fields2check as $field_name)
    {
        if( !isset($fields[strtoupper($field_name)]) )  
        {
            $msg="Table {$table} - Missing field {$field_name}";
            $status_ok=false;
            break;  
        }
    }
    return array($status_ok,$msg);
}
    


?>