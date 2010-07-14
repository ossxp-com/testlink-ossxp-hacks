<?php
/** 
 * 	TestLink Open Source Project - http://testlink.sourceforge.net/
 * 
 * 	@version 	$Id: archiveData.php,v 1.73 2010/06/28 16:19:37 asimon83 Exp $
 * 	@author 	Martin Havlat
 * 
 * 	Allows you to show test suites, test cases.
 * 	Normally launched from tree navigator.
 *	Also called when search option on Navigation Bar is used
 *
 *	@internal revision
 *  20100628 - asimon - removal of constants from filter control class
 *  20160625 - asimon - refactoring for new filter features and BUGID 3516
 *  20100624 - asimon - CVS merge (experimental branch to HEAD)
 *	20100621 - eloff - BUGID 3241 - Implement vertical layout
 *	20100502 - franciscom - BUGID 3405: Navigation Bar - Test Case Search - Crash when search a nonexistent testcase	
 *  20100315 - franciscom - fixed refesh tree logic	
 *  20100223 - asimon - BUGID 3049
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once('testsuite.class.php');
testlinkInitPage($db);

$templateCfg = templateConfiguration();
$viewerArgs = null;
$args = init_args($viewerArgs);
$smarty = new TLSmarty();
$gui = new stdClass();
$gui->page_title = lang_get('container_title_' . $args->feature);

switch($args->feature)
{
	case 'testproject':
	case 'testsuite':
		$item_mgr = new $args->feature($db);
		$gui->attachments = getAttachmentInfosFrom($item_mgr,$args->id);
		$gui->id = $args->id;
		if($args->feature == 'testproject')
		{
			$item_mgr->show($smarty,$gui,$templateCfg->template_dir,$args->id);
		}
		else
		{
			$item_mgr->show($smarty,$gui,$templateCfg->template_dir,$args->id,array('show_mode' => $args->show_mode));
        }
        
		break;

	// BUGID 3049
	case 'testplan': 
		$tplan_mgr = new testplan($db);
		$tproject_mgr = new testproject($db);
		$gui->id = $args->id;
		$gui->tplan_id = $args->id;
		
		$options = array('output' => 'array');
		$linked_tcversions=$tplan_mgr->get_linked_tcversions($gui->tplan_id,null,$options);
		
		$tplan = $tplan_mgr->get_by_id($args->id);
		$gui->tplan_name = $tplan['name'];
		$gui->container_data['name'] = $tplan['name'];
		$gui->tplan_description = $tplan['notes'];
		$tproject = $tproject_mgr->get_by_id($tplan['testproject_id']);
		$gui->tproject_name = $tproject['name'];
		$gui->tproject_description = $tproject['notes'];
				
		foreach ($linked_tcversions as $tc_id => $tc) {
			if (!isset($tc['user_id']) || !is_numeric($tc['user_id'])) {
				unset($linked_tcversions[$tc_id]);
			}
		}
		
		if (count($linked_tcversions) != 0) {
			// yes, we have testcases to unassign, draw the button
			$gui->draw_tc_unassign_button = true;
		} else {
			// nothing to unassign --> no button, but a little message
			$gui->draw_tc_unassign_button = false;
			$gui->result = sprintf(lang_get('nothing_to_unassign_msg'), $gui->tplan_name);
		}
				
		$gui->level = 'testplan';
		$gui->mainTitle = lang_get('remove_assigned_testcases');
		$gui->page_title = lang_get('testplan');
		$gui->refreshTree = false;
		$gui->unassign_all_tcs_warning_msg = sprintf(lang_get('unassign_all_tcs_warning_msg'), $gui->tplan_name);
		
		$smarty->assign('gui', $gui);
		$smarty->display($templateCfg->template_dir . 'containerView.tpl');
		
		break;
		
	case 'testcase':
		$path_info = null;
		$get_path_info = false;
		$item_mgr = new testcase($db);
		$viewerArgs['refresh_tree'] = 'no';

	    $gui->platforms = null;
        $gui->tableColspan = 5;
		$gui->loadOnCancelURL = '';
		$gui->attachments = null;
		$gui->direct_link = null;
		$gui->steps_results_layout = config_get('spec_cfg')->steps_results_layout;
    	
   		// has been called from a test case search
		if(!is_null($args->targetTestCase) && strcmp($args->targetTestCase,$args->tcasePrefix) != 0)
		{
			$viewerArgs['show_title'] = 'no';
			$viewerArgs['display_testproject'] = 1;
			$viewerArgs['display_parent_testsuite'] = 1;
			$args->id = $item_mgr->getInternalID($args->targetTestCase);
            $get_path_info = ($args->id > 0);
		}

		if( $args->id > 0 )
		{
			if( $get_path_info || $args->show_path )
			{
			    $path_info = $item_mgr->tree_manager->get_full_path_verbose($args->id);
			}
			
		  	$platform_mgr = new tlPlatform($db,$args->tproject_id);
	    	$gui->platforms = $platform_mgr->getAllAsMap();
      		$gui->attachments[$args->id] = getAttachmentInfosFrom($item_mgr,$args->id);
			$gui->direct_link = $item_mgr->buildDirectWebLink($_SESSION['basehref'],$args->id);
		}
	    $gui->id = $args->id;
		$item_mgr->show($smarty,$gui,$templateCfg->template_dir,$args->id,$args->tcversion_id,
		                $viewerArgs,$path_info,$args->show_mode);
		break;

	default:
		tLog('Argument "edit" has invalid value: ' . $args->feature , 'ERROR');
		trigger_error($_SESSION['currentUser']->login.'> Argument "edit" has invalid value.', E_USER_ERROR);
}

/**
 * 
 *
 */
function init_args(&$viewerCfg)
{
	$iParams = array("edit" => array(tlInputParameter::STRING_N,0,50),
			         "id" => array(tlInputParameter::INT_N),
			         "tcase_id" => array(tlInputParameter::INT_N),
			         "tcversion_id" => array(tlInputParameter::INT_N),
			         "targetTestCase" => array(tlInputParameter::STRING_N,0,24),
			         "show_path" => array(tlInputParameter::INT_N),
			         "show_mode" => array(tlInputParameter::STRING_N,0,50),
			         "tcasePrefix" => array(tlInputParameter::STRING_N,0,16));
	 				 //"setting_refresh_tree_on_action" => array(tlInputParameter::STRING_N,0,1));

	$args = new stdClass();
    R_PARAMS($iParams,$args);
	
	// BUGID 3516
	// For more information about the data accessed in session here, see the comment
	// in the file header of lib/functions/tlTestCaseFilterControl.class.php.
	$form_token = isset($_REQUEST['form_token']) ? $_REQUEST['form_token'] : 0;
	
	$mode = 'edit_mode';
	
	$session_data = isset($_SESSION[$mode]) && isset($_SESSION[$mode][$form_token])
	                ? $_SESSION[$mode][$form_token] : null;
	
	$args->refreshTree = isset($session_data['setting_refresh_tree_on_action']) ?
                         $session_data['setting_refresh_tree_on_action'] : 0;
	
    $args->user_id = isset($_SESSION['userID']) ? $_SESSION['userID'] : 0;
    //@TODO schlundus, rename Parameter from edit to feature
    $args->feature = $args->edit;

    
   	if (!$args->tcversion_id)
   	{
   		 $args->tcversion_id = testcase::ALL_VERSIONS;
    }
  
  	// used to manage goback  
    if(intval($args->tcase_id) > 0)
    {
    	$args->feature = 'testcase';
    	$args->id = intval($args->tcase_id);
    }
    
   	switch($args->feature)
    {
		case 'testsuite':
        	$_SESSION['setting_refresh_tree_on_action'] = ($args->refreshTree) ? 1 : 0;
        	break;
     
        case 'testcase':
			$args->id = is_null($args->id) ? 0 : $args->id;
			$spec_cfg = config_get('spec_cfg');
			$viewerCfg = array('action' => '', 'msg_result' => '','user_feedback' => '');
			$viewerCfg['disable_edit'] = 0;

			// need to understand if using this logic is ok
			// Why I'm ignoring $args->setting_refresh_tree_on_action ?
			// Seems here I have to set refresh always to NO!!!
			//
			// $viewerCfg['refresh_tree'] = $spec_cfg->automatic_tree_refresh ? "yes" : "no";
			// if(isset($_SESSION['setting_refresh_tree_on_action']))
			// {
			// 	$viewerCfg['refresh_tree'] = $_SESSION['setting_refresh_tree_on_action'];
            // }
            $viewerCfg['refreshTree'] = 0;
			break;
    }
    $cfg = config_get('testcase_cfg');
    if (strpos($args->targetTestCase,$cfg->glue_character) === false)
    {
    	$args->targetTestCase = $args->tcasePrefix . $args->targetTestCase;
 	}
   	$args->tproject_id = isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0;
    return $args;
}
?>