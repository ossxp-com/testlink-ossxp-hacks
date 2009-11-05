<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *  
 * @filesource $RCSfile: reqTcAssign.php,v $
 * @version $Revision: 1.12 $
 * @modified $Date: 2009/02/14 10:14:28 $  $Author: franciscom $
 * 
 * @author Martin Havlat
 *
 * 20081130 - franciscom - BUGID 1852 - Bulk Assignment Feature
 * 20080512 - franciscom - new input argument to control display/hide of close button
 * 20070617 - franciscom - refactoring
 * 20070124 - franciscom
 * use show_help.php to apply css configuration to help pages
 *
**/
require_once("../../config.inc.php");
require_once("common.php");
require_once('requirements.inc.php');

testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();
$args=init_args();

$gui = new stdClass();
$gui->showCloseButton = $args->showCloseButton;
$gui->user_feedback='';
$gui->tcTitle = null;
$gui->arrAssignedReq = null;
$gui->arrUnassignedReq = null;
$gui->arrReqSpec = null;
$gui->selectedReqSpec=$args->idReqSpec;

$bulkCounter=0;
$bulkDone=false;
$pfn=null;

switch($args->doAction)
{
    case 'assign':
	    $pfn = "assign_to_tcase";
	    break;  

    case 'unassign':
	    $pfn = "unassign_from_tcase";
	    break;  

    case 'bulkassign':
      $bulkCounter=doBulkAssignment($db,$args);
      $bulkDone=true;
      $args->edit='testsuite';
	    break;  

    case 'switchspec':
      $args->edit='testsuite';
	    break;  

}

if(!is_null($pfn))
{
    $gui=doSingleTestCaseOperation($db,$args,$gui,$pfn);
}


switch($args->edit)
{
    case 'testproject':
	       show_instructions('assignReqs');
	       exit();
    break;
    
    case 'testsuite':
         $gui=processTestSuite($db,$args,$gui);
         $templateCfg->default_template='reqTcBulkAssignment.tpl';
         if( $bulkDone)
         {
             $gui->user_feedback=sprintf(lang_get('bulk_assigment_done'),$bulkCounter); 
         }    
    break;
      
   case 'testcase':
        $gui=processTestCase($db,$args,$gui);
   break;
  
  default:
	tlog("Wrong GET/POST arguments.", 'ERROR');
	exit();
  break;

}

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->assign('modify_req_rights', has_rights($db,"mgt_modify_req")); 
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


/*
  function: init_args()

  args:
  
  returns: 

*/
function init_args()
{
  
    $args = new stdClass();
    $_REQUEST = strings_stripSlashes($_REQUEST);

    $args->idReqSpec = null;
    $args->id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;
    $args->edit = isset($_REQUEST['edit']) ? $_REQUEST['edit'] : null;
    $args->idReq = isset($_REQUEST['req']) ? intval($_REQUEST['req']) : null;
    $args->reqIdSet = isset($_REQUEST['req_id']) ? $_REQUEST['req_id'] : null;
    $args->showCloseButton = isset($_REQUEST['showCloseButton']) ? 1 : 0;
    $args->doAction = isset($_REQUEST['doAction']) ? $_REQUEST['doAction'] : null;
    if(is_null($args->doAction))
    {
        $args->doAction = isset($_REQUEST['unassign']) ? 'unassign' : null;
        
    }
    if(is_null($args->doAction))
    {
        $args->doAction = isset($_REQUEST['assign']) ? 'assign' : null;
        
    }


	  // 20081103 - sisajr - hold choosen SRS (saved for a session)
	  if (isset($_REQUEST['idSRS']) && intval($_REQUEST['idSRS']) > 0)
	  {
	  	$args->idReqSpec = intval($_REQUEST['idSRS']);
	  	$_SESSION['currentSrsId'] = $args->idReqSpec;
	  }
	  else if(isset($_SESSION['currentSrsId']) && intval($_SESSION['currentSrsId']) > 0)
	  {
	  	$args->idReqSpec = intval($_SESSION['currentSrsId']);
	  }

    $args->tproject_id = isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0;

    return $args;
}

/*
  function: processTestSuite

  args:
  
  returns: 

*/
function processTestSuite(&$dbHandler,&$argsObj,&$guiObj)
{
    $tproject_mgr = new testproject($dbHandler);

    $guiObj->bulkassign_warning_msg='';
    $guiObj->tsuite_id=$argsObj->id;
    
    $tsuite_info=$tproject_mgr->tree_manager->get_node_hierachy_info($guiObj->tsuite_id);
    $guiObj->pageTitle = lang_get('test_suite') . config_get('gui_title_separator_1') . $tsuite_info['name'];
     
    $get_not_empty = 1;
	  $guiObj->req_specs = $tproject_mgr->getOptionReqSpec($argsObj->tproject_id,$get_not_empty);
    $guiObj->selectedReqSpec = $argsObj->idReqSpec;
    $guiObj->tcase_number=0;
    $guiObj->has_req_spec=false;
    $guiObj->tsuite_id=$argsObj->id;
    if( !is_null($guiObj->req_specs) && count($guiObj->req_specs) > 0)
    {  
       $guiObj->has_req_spec=true;
       
       if( is_null($argsObj->idReqSpec) )
       {
          $guiObj->selectedReqSpec=key($guiObj->req_specs);
       }
       
       $req_spec_mgr = new requirement_spec_mgr($dbHandler);
       $guiObj->requirements=$req_spec_mgr->get_requirements($guiObj->selectedReqSpec);
       
       $tsuite_mgr = new testsuite($dbHandler);
       $tcase_set=$tsuite_mgr->get_testcases_deep($argsObj->id,'only_id');
       $guiObj->tcase_number=count($tcase_set);    
       $guiObj->bulkassign_warning_msg=sprintf(lang_get('bulk_req_assign_msg'),$guiObj->tcase_number);
    }

    return $guiObj;
}

/*
  function: doBulkAssignment

  args:
  
  returns: 

*/
function doBulkAssignment(&$dbHandler,&$argsObj)
{
  
    
    $req_mgr = new requirement_mgr($dbHandler);
    $assignmentCounter=0;
	  $requirements = array_keys($argsObj->reqIdSet);
    if( !is_null($requirements) && count($requirements) > 0 )
    {
        $tsuite_mgr = new testsuite($dbHandler);
        $tcase_set=$tsuite_mgr->get_testcases_deep($argsObj->id,'only_id');
        $assignmentCounter=$req_mgr->bulk_assignment($requirements,$tcase_set);
    } 
    return $assignmentCounter;
}

  
/*
  function: doSingleTestCaseOperation

  args:
  
  returns: 

*/
function doSingleTestCaseOperation(&$dbHandler,&$argsObj,&$guiObj,$pfn)
{
  $msg='';
	$req_ids = array_keys($argsObj->reqIdSet);
	if (count($req_ids))
	{
    $req_mgr = new requirement_mgr($dbHandler);
		foreach ($req_ids as $idOneReq)
		{
			$result = $req_mgr->$pfn($idOneReq,$argsObj->id);

			if (!$result)
			{
				$msg .= $idOneReq . ', ';
			}	
		}
		if (!empty($msg))
		{
			$guiObj->user_feedback = lang_get('req_msg_notupdated_coverage') . $msg;
		}	
	}
	else
	{
		$guiObj->user_feedback = lang_get('req_msg_noselect');
  }
  return $guiObj;
} 


/*
  function: processTestCase

  args:
  
  returns: 

*/
function processTestCase(&$dbHandler,&$argsObj,&$guiObj)
{
	 $get_not_empty = 1;
   $tproject_mgr = new testproject($dbHandler);
	 $guiObj->arrReqSpec = $tproject_mgr->getOptionReqSpec($argsObj->tproject_id,$get_not_empty);
	 $SRS_qty = count($guiObj->arrReqSpec);
  
	 if($SRS_qty > 0)
	 {
	   	$tc_mgr = new testcase($dbHandler);
	   	$arrTc = $tc_mgr->get_by_id($argsObj->id);
	   	if ($arrTc)
	   	{
	   		$guiObj->tcTitle = $arrTc[0]['name'];
	   	
	   		// get first ReqSpec if not defined
	   		if( is_null($argsObj->idReqSpec) )
	   		{
	   			reset($guiObj->arrReqSpec);
	   			$argsObj->idReqSpec = key($guiObj->arrReqSpec);
	   		}

	   		if($argsObj->idReqSpec)
	   		{
	   		  $req_spec_mgr = new requirement_spec_mgr($dbHandler);
	   			$guiObj->arrAssignedReq = $req_spec_mgr->get_requirements($argsObj->idReqSpec, 'assigned', $argsObj->id);
	   			$guiObj->arrAllReq = $req_spec_mgr->get_requirements($argsObj->idReqSpec);
	   			$guiObj->arrUnassignedReq = array_diff_byId($guiObj->arrAllReq, $guiObj->arrAssignedReq);
	   		}
	   	}
	 }  // if( $SRS_qty > 0 )	
	 return $guiObj;
}

function checkRights(&$db,&$user)
{
	return ($user->hasRight($db,'mgt_view_req') && $user->hasRight($db,'mgt_modify_req'));
}
?>