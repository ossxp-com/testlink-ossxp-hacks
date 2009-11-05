<?php
/** TestLink Open Source Project - http://testlink.sourceforge.net/ 
* $Id: builds.inc.php,v 1.25 2009/01/14 20:06:24 schlundus Exp $
* 
* @author Martin Havlat
*
* Functions for Test Plan management - build related
*
* 20070120 - franciscom - changes to getBuilds()
*
*/
require_once('../../config.inc.php');
require_once("../functions/common.php");

/**
 * Collect all builds for the Test Plan
 *
 * 20070120 - franciscom
 * 
 * args:
 *
 *       [active]: default:null -> all, 1 -> active, 0 -> inactive
 *       [open]  : default:null -> all, 1 -> open  , 0 -> closed/completed
 */
//@TODO, schlundus, delete if not needed
function DEPR_getBuilds(&$db,$idPlan, $order_by="ORDER BY builds.id DESC",$active=null,$open=null)
{
 	$sql = "SELECT builds.id, name FROM builds WHERE testplan_id = " . $idPlan;
 	
 	
 	// 20070120 - franciscom
 	if( !is_null($active) )
 	{
 	   $sql .= " AND active=" . intval($active) . " ";   
 	}
 	if( !is_null($open) )
 	{
 	   $sql .= " AND is_open=" . intval($open) . " ";   
 	}
 		
 	
 	if (strlen(trim($order_by)))
 	{
 		$sql .= " " . $order_by;
 	}
	return getBuildInfo($db,$sql);
}

//@TODO, schlundus, delete if not needed
function DEPR_getBuildInfo(&$db,$sql)
{
	$arrBuilds = $db->fetchColumnsIntoMap($sql,'id','name');

 	return $arrBuilds;
}

//@TODO: schlundus, should be moved inside class testplan
function getBuild_by_id(&$db,$buildID)
{
	$sql = "SELECT builds.* FROM builds WHERE builds.id = " . $buildID;
	$result = $db->exec_query($sql);
	$myrow = $db->fetch_array($result);

	return $myrow;
}

//@TODO, schlundus, delete if not needed
function DEPR_delete_build(&$db,$build_id)
{
	//DEPENDENT DATA?
	$sql = "DELETE FROM builds WHERE builds.id = {$build_id}";
	$result = $db->exec_query($sql);
	
	return $result ? 1 : 0;
}
?>