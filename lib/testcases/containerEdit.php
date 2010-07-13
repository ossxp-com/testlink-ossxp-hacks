<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @version $Revision: 1.114 $
 * @modified $Date: 2010/06/28 16:19:37 $ by $Author: asimon83 $
 * @author Martin Havlat
 *
 *	@internal revisions
 *  20100628 - asimon - removal of constants from filter control class
 *  20100625 - asimon - refactoring for new filter features and BUGID 3516
 *  20100624 - asimon - CVS merge (experimental branch to HEAD)
 *  20100314 - franciscom - added logic to refresh tree when copying N test cases 	
 * 						    added logic to get user choice regarding refresh tree from SESSION.
 *  20100223 - asimon - added removeTestcaseAssignments() for BUGID 3049
 *	20100204 - franciscom - changes in $tsuiteMgr->copy_to() call	
 *	20100202 - franciscom - BUGID 3130: TestSuite: Edit - rename Test Suite Name causes PHP Fatal Error
 *                          (bug created due change in show() interface
 *	20091206 - franciscom - addTestSuite() - new test suites are order set to last on tree branch
 *	20081225 - franciscom - Postgres SQL Error
 *	20080827 - franciscom - BUGID 1692
 *	20080329 - franciscom - added contribution by Eugenia Drosdezki - Move/copy testcases
 *	20080223 - franciscom - BUGID 1408
 *	20080129 - franciscom - contribution - tuergeist@gmail.com - doTestSuiteReorder() remove global coupling
 *	20080122 - franciscom - BUGID 1312
 */
require_once("../../config.inc.php");
require_once("common.php");
require_once("opt_transfer.php");
require_once("web_editor.php");
require_once("specview.php"); //BUGID 3049
$editorCfg=getWebEditorCfg('design');
require_once(require_web_editor($editorCfg['type']));

testlinkInitPage($db);
$tree_mgr = new tree($db);
$tproject_mgr = new testproject($db);
$tplan_mgr = new testplan($db);
$tsuite_mgr = new testsuite($db);
$tcase_mgr = new testcase($db);

$template_dir = 'testcases/';
$refreshTree = false;
$level = null;

// Option Transfer configuration
$opt_cfg=new stdClass();
$opt_cfg->js_ot_name = 'ot';

$args = init_args($opt_cfg);
$gui_cfg = config_get('gui');
// $spec_cfg = config_get('spec_cfg');
$smarty = new TLSmarty();
$smarty->assign('editorType',$editorCfg['type']);

$a_keys['testsuite'] = array('details');
$a_tpl = array( 'move_testsuite_viewer' => 'containerMove.tpl',
                'delete_testsuite' => 'containerDelete.tpl',
                'del_testsuites_bulk' => 'containerDeleteBulk.tpl',
                'updateTCorder' => 'containerView.tpl',
                'move_testcases_viewer' => 'containerMoveTC.tpl',
                'do_copy_tcase_set' => 'containerMoveTC.tpl');   
   
$a_actions = array ('edit_testsuite' => 0,'new_testsuite' => 0,'delete_testsuite' => 0,'do_move' => 0,
					'do_copy' => 0,'reorder_testsuites' => 1,'do_testsuite_reorder' => 0,
                    'add_testsuite' => 1,'move_testsuite_viewer' => 0,'update_testsuite' => 1,
                    'move_testcases_viewer' => 0,'do_move_tcase_set' => 0,
                    'do_copy_tcase_set' => 0, 'del_testsuites_bulk' => 0, 'doUnassignFromPlan' => 0);

$a_init_opt_transfer=array('edit_testsuite' => 1,'new_testsuite'  => 1,'add_testsuite'  => 1,
                           'update_testsuite' => 1);

$the_tpl = null;
$action = null;

foreach ($a_actions as $the_key => $the_val)
{
	if (isset($_POST[$the_key]) )
	{
		$the_tpl = isset($a_tpl[$the_key]) ? $a_tpl[$the_key] : null;
		$init_opt_transfer = isset($a_init_opt_transfer[$the_key])?1:0;

		$action = $the_key;
		$get_c_data = $the_val;
		$level = 'testsuite';
		$warning_empty_name = lang_get('warning_empty_com_name');
		break;
	}
}

$smarty->assign('level', $level);
$smarty->assign('page_title',lang_get('container_title_' . $level));

if($init_opt_transfer)
{
    $opt_cfg = initializeOptionTransfer($tproject_mgr,$tsuite_mgr,$args,$action);
}
// create  web editor objects
list($oWebEditor,$webEditorHtmlNames,$webEditorTemplateKey)=initWebEditors($a_keys,$level,$editorCfg);

if($get_c_data)
{
	$name_ok = 1;
	$c_data = getValuesFromPost($webEditorHtmlNames);

	if($name_ok && !check_string($c_data['container_name'],$g_ereg_forbidden))
	{
		$msg = lang_get('string_contains_bad_chars');
		$name_ok = 0;
	}

	if($name_ok && ($c_data['container_name'] == ""))
	{
		$msg = $warning_empty_name;
		$name_ok = 0;
	}
}

switch($action)
{
	case 'edit_testsuite':
	case 'new_testsuite':
		keywords_opt_transf_cfg($opt_cfg, $args->assigned_keyword_list);
		$smarty->assign('opt_cfg', $opt_cfg);
		$tsuite_mgr->viewer_edit_new($smarty,$template_dir,$webEditorHtmlNames,$oWebEditor,$action,
		                             $args->containerID, $args->testsuiteID,null,$webEditorTemplateKey);
		break;

    case 'delete_testsuite':
   		$refreshTree = deleteTestSuite($smarty,$args,$tsuite_mgr,$tree_mgr,$tcase_mgr,$level);
    break;

    case 'del_testsuites_bulk':
   		$refreshTree = deleteTestSuitesBulk($smarty,$args,$tsuite_mgr,$tree_mgr,$tcase_mgr,$level);
    break;


    case 'move_testsuite_viewer':
		moveTestSuiteViewer($smarty,$tproject_mgr,$args);
	    break;

    case 'move_testcases_viewer':
    	moveTestCasesViewer($db,$smarty,$tproject_mgr,$tree_mgr,$args);
    	break;

	case 'reorder_testsuites':
    	$ret = reorderTestSuiteViewer($smarty,$tree_mgr,$args);
    	$level = is_null($ret) ? $level : $ret;
    	break;

    // case 'do_testsuite_reorder':
    // 	doTestSuiteReorder($smarty,$template_dir,$tproject_mgr,$tsuite_mgr,$args);
    // 	break;

    case 'do_move':
    	moveTestSuite($smarty,$template_dir,$tproject_mgr,$args);
    	break;

    case 'do_copy':
    	copyTestSuite($smarty,$template_dir,$tsuite_mgr,$args);
    	break;

    case 'update_testsuite':
	  	if ($name_ok)
	  	{
        	$msg = updateTestSuite($tsuite_mgr,$args,$c_data,$_REQUEST);
    	}
    	// 20100202 - franciscom -
		$guiObj = new stdClass();
  	  	$guiObj->attachments = getAttachmentInfosFrom($tsuite_mgr,$args->testsuiteID);
	  	$guiObj->id = $args->testsuiteID;
		$guiObj->page_title = lang_get('container_title_testsuite');

     	$tsuite_mgr->show($smarty,$guiObj,$template_dir,$args->testsuiteID,null,$msg);
    	break;

    case 'add_testsuite':
	    $messages = null;
	    $op['status'] = 0;
		if ($name_ok)
		{
	    	$op = addTestSuite($tsuite_mgr,$args,$c_data,$_REQUEST);
	    	$messages = array( 'result_msg' => $op['messages']['msg'], 
	    	                   'user_feedback' => $op['messages']['user_feedback']);
	  	}
    	
        // $userInput is used to maintain data filled by user if there is
        // a problem with test suite name.
        $userInput = $op['status'] ? null : $_REQUEST; 
        $assignedKeywords = $op['status'] ? "" : $args->assigned_keyword_list;
        keywords_opt_transf_cfg($opt_cfg, $assignedKeywords);
      	$smarty->assign('opt_cfg', $opt_cfg);
  	    $tsuite_mgr->viewer_edit_new($smarty,$template_dir,$webEditorHtmlNames, $oWebEditor, $action,
	                                 $args->containerID, null,$messages,
	                                 $webEditorTemplateKey,$userInput);
    	break;


    case 'do_move_tcase_set':
    	moveTestCases($smarty,$template_dir,$tsuite_mgr,$tree_mgr,$args);
    	break;

    case 'do_copy_tcase_set':
    	$op = copyTestCases($smarty,$template_dir,$tsuite_mgr,$tcase_mgr,$args);
    	$refreshTree = $op['refreshTree'];
    	moveTestCasesViewer($db,$smarty,$tproject_mgr,$tree_mgr,$args,$op['userfeedback']);
    	break;

    // BUGID 3049
    case 'doUnassignFromPlan':
    	removeTestcaseAssignments($db, $args, $tplan_mgr, $smarty, $template_dir);
    	break;
    	
    default:
    	trigger_error("containerEdit.php - No correct GET/POST data", E_USER_ERROR);
    	break;
}

if($the_tpl)
{
	// echo "DEBUG - \$action;$action<br>";	echo "DEBUG - \$the_tpl:$the_tpl<br>";	
	// echo "DEBUG - \$refreshTree:$refreshTree<br>";
	$smarty->assign('refreshTree',$refreshTree && $args->refreshTree);
	$smarty->display($template_dir . $the_tpl);
}


function getValuesFromPost($akeys2get)
{
	$akeys2get[] = 'container_name';
	$c_data = array();
	foreach($akeys2get as $key)
	{
		$c_data[$key] = isset($_POST[$key]) ? strings_stripSlashes($_POST[$key]) : null;
	}
	return $c_data;
}

/*
  function:

  args :

  returns:

*/
function build_del_testsuite_warning_msg(&$tree_mgr,&$tcase_mgr,&$testcases,$tsuite_id)
{
	$msg = null;
	$msg['warning'] = null;
	$msg['link_msg'] = null;
	$msg['delete_msg'] = null;

	if(!is_null($testcases))
	{
    	$show_warning = 0;
    	$delete_msg = '';
  		$verbose = array();
  		$msg['link_msg'] = array();
	
    	$status_warning = array('linked_and_executed' => 1,
    	                    	'linked_but_not_executed' => 1,
    	                    	'no_links' => 0);

		$delete_notice = array('linked_and_executed' => lang_get('delete_notice'),
    	                    	'linked_but_not_executed' => '',
    	                    	'no_links' => '');

  		foreach($testcases as $the_key => $elem)
  		{
  			$verbose[] = $tree_mgr->get_path($elem['id'],$tsuite_id);

  			$status = $tcase_mgr->check_link_and_exec_status($elem['id']);
  			$msg['link_msg'][] = $status;

  			if($status_warning[$status])
  			{
  		  		$show_warning = 1;
  		  		$msg['delete_msg'] = $delete_notice[$status];
  			}
	  	}

	  	$idx = 0;
	  	if($show_warning)
	  	{
	  		$msg['warning'] = array();
	  		foreach($verbose as $the_key => $elem)
	  		{
	  			$msg['warning'][$idx] = '';
	  			$bSlash = false;
	  			foreach($elem as $tkey => $telem)
	  			{
	  				if ($bSlash)
	  				{
	  					$msg['warning'][$idx] .= "\\";
	  				}
	  				$msg['warning'][$idx] .= $telem['name'];
	  				$bSlash = true;
	  			}
	  			$idx++;
	  		}
	  	}
	  	else
	  	{
	  	  	$msg['link_msg'] = null;
	  		$msg['warning'] = null;
	  	}
 	}
	return $msg;
}


/*
  function:

  args :

  returns:

*/
function init_args($optionTransferCfg)
{
   	$args = new stdClass();
    $_REQUEST = strings_stripSlashes($_REQUEST);

    $args->tprojectID = $_SESSION['testprojectID'];
    $args->tprojectName = $_SESSION['testprojectName'];
    $args->userID = $_SESSION['userID'];

    $keys2loop=array('nodes_order' => null, 'tcaseSet' => null,
                     'target_position' => 'bottom', 'doAction' => '');
    foreach($keys2loop as $key => $value)
    {
       $args->$key = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $value;
    }


    $args->tsuite_name = isset($_REQUEST['testsuiteName']) ? $_REQUEST['testsuiteName'] : null;
    $args->bSure = (isset($_REQUEST['sure']) && ($_REQUEST['sure'] == 'yes'));
    $rl_html_name = $optionTransferCfg->js_ot_name . "_newRight";
    $args->assigned_keyword_list = isset($_REQUEST[$rl_html_name])? $_REQUEST[$rl_html_name] : "";


    // integer values
    $keys2loop=array('testsuiteID' => null, 'containerID' => null,
                     'objectID' => null, 'copyKeywords' => 0, 'tplan_id' => 0);
    foreach($keys2loop as $key => $value)
    {
       $args->$key = isset($_REQUEST[$key]) ? intval($_REQUEST[$key]) : $value;
    }

    if(is_null($args->containerID))
    {
    	$args->containerID = $args->tprojectID;
    }

//	// BUGID 
//	// Get user choice for refresh tree after each operation
//	if(isset($_SESSION['setting_refresh_tree_on_action']))
//	{
//		$args->refreshTree = $_SESSION['setting_refresh_tree_on_action'] == 'yes' ? 1 : 0;
//    }
//    else
//    {
//    	// use default from config.inc.php
//    	$spec_cfg = config_get('spec_cfg');
//    	$args->refreshTree = $spec_cfg->automatic_tree_refresh ? 1 : 0;
//    }

    // BUGID 3516
	// For more information about the data accessed in session here, see the comment
	// in the file header of lib/functions/tlTestCaseFilterControl.class.php.
	$form_token = isset($_REQUEST['form_token']) ? $_REQUEST['form_token'] : 0;
	
	$mode = 'edit_mode';
	
	$session_data = isset($_SESSION[$mode]) && isset($_SESSION[$mode][$form_token])
	                ? $_SESSION[$mode][$form_token] : null;
	
	$args->refreshTree = isset($session_data['setting_refresh_tree_on_action']) ?
                         $session_data['setting_refresh_tree_on_action'] : 0;
    
    return $args;
}


/*
  function:

  args:

  returns:

*/
function writeCustomFieldsToDB(&$db,$tprojectID,$tsuiteID,&$hash)
{
    $ENABLED = 1;
    $NO_FILTERS = null;

    $cfield_mgr = new cfield_mgr($db);
    $cf_map = $cfield_mgr->get_linked_cfields_at_design($tprojectID,$ENABLED,
                                                        $NO_FILTERS,'testsuite');
    $cfield_mgr->design_values_to_db($hash,$tsuiteID,$cf_map);
}


/*
  function: deleteTestSuite

  args:

  returns: true -> refresh tree
           false -> do not refresh

*/
function deleteTestSuite(&$smartyObj,&$argsObj,&$tsuiteMgr,&$treeMgr,&$tcaseMgr,$level)
{
  	$feedback_msg = '';
	if($argsObj->bSure)
	{
	 	$tsuite = $tsuiteMgr->get_by_id($argsObj->objectID);
		$tsuiteMgr->delete_deep($argsObj->objectID);
		$tsuiteMgr->deleteKeywords($argsObj->objectID);
		$smartyObj->assign('objectName', $tsuite['name']);
		$doRefreshTree = true;
		$feedback_msg = 'ok';
		$smartyObj->assign('user_feedback',lang_get('testsuite_successfully_deleted'));
	}
	else
	{
	  	$doRefreshTree = false;

		// Get test cases present in this testsuite and all children
		$testcases = $tsuiteMgr->get_testcases_deep($argsObj->testsuiteID);

		$map_msg['warning'] = null;
		$map_msg['link_msg'] = null;
		$map_msg['delete_msg'] = null;

		if(!is_null($testcases))
		{
			$map_msg = build_del_testsuite_warning_msg($treeMgr,$tcaseMgr,$testcases,$argsObj->testsuiteID);
		}

		// prepare to show the delete confirmation page
		$smartyObj->assign('objectID',$argsObj->testsuiteID);
		$smartyObj->assign('objectName', $argsObj->tsuite_name);
		$smartyObj->assign('delete_msg',$map_msg['delete_msg']);
		$smartyObj->assign('warning', $map_msg['warning']);
		$smartyObj->assign('link_msg', $map_msg['link_msg']);
	}
	$smartyObj->assign('page_title', lang_get('delete') . " " . lang_get('container_title_' . $level));
 	$smartyObj->assign('sqlResult',$feedback_msg);

 	return $doRefreshTree;
}

/*
  function: addTestSuite

  args:

  returns: map with messages and status
  
  revision: 20091206 - franciscom - new items are created as last element of tree branch

*/
function addTestSuite(&$tsuiteMgr,&$argsObj,$container,&$hash)
{
    $new_order = null;

    // compute order
    $nt2exclude=array('testplan' => 'exclude_me','requirement_spec'=> 'exclude_me','requirement'=> 'exclude_me');
    $siblings = $tsuiteMgr->tree_manager->get_children($argsObj->containerID,$nt2exclude);
    if( !is_null($siblings) )
    {
    	$dummy = end($siblings);
    	$new_order = $dummy['node_order']+1;
    }
	$ret = $tsuiteMgr->create($argsObj->containerID,$container['container_name'],$container['details'],
	                         $new_order,config_get('check_names_for_duplicates'),config_get('action_on_duplicate_name'));
		                         
    $op['messages']= array('msg' => $ret['msg'], 'user_feedback' => '');
    $op['status']=$ret['status_ok'];
	
	if($ret['status_ok'])
	{
		$op['messages']['user_feedback'] = lang_get('testsuite_created');
		if($op['messages']['msg'] != 'ok')
		{
			$op['messages']['user_feedback'] = $op['messages']['msg'];  
		}

		if(trim($argsObj->assigned_keyword_list) != "")
    	{
    		$tsuiteMgr->addKeywords($ret['id'],explode(",",$argsObj->assigned_keyword_list));
    	}
    	writeCustomFieldsToDB($tsuiteMgr->db,$argsObj->tprojectID,$ret['id'],$hash);
	}
	return $op;
}

/*
  function: moveTestSuiteViewer
            prepares smarty variables to display move testsuite viewer

  args:

  returns: -

*/
function  moveTestSuiteViewer(&$smartyObj,&$tprojectMgr,$argsObj)
{
	$testsuites = $tprojectMgr->gen_combo_test_suites($argsObj->tprojectID,
	                                                  array($argsObj->testsuiteID => 'exclude'));
	// Added the Test Project as the FIRST Container where is possible to copy
	$testsuites = array($argsObj->tprojectID => $argsObj->tprojectName) + $testsuites;

 	// original container (need to comment this better)
	$smartyObj->assign('old_containerID', $argsObj->tprojectID);
	$smartyObj->assign('containers', $testsuites);
	$smartyObj->assign('objectID', $argsObj->testsuiteID);
	$smartyObj->assign('object_name', $argsObj->tsuite_name);
	$smartyObj->assign('top_checked','checked=checked');
 	$smartyObj->assign('bottom_checked','');
}


/*
  function: reorderTestSuiteViewer
            prepares smarty variables to display reorder testsuite viewer

  args:

  returns: -

*/
function  reorderTestSuiteViewer(&$smartyObj,&$treeMgr,$argsObj)
{
	$level = null;
	$oid = is_null($argsObj->testsuiteID) ? $argsObj->containerID : $argsObj->testsuiteID;
	$children = $treeMgr->get_children($oid, array("testplan" => "exclude_me",
                                                 "requirement_spec"  => "exclude_me"));
  	$object_info = $treeMgr->get_node_hierarchy_info($oid);
  	$object_name = $object_info['name'];


	if (!sizeof($children))
		$children = null;

	$smartyObj->assign('arraySelect', $children);
	$smartyObj->assign('objectID', $oid);
	$smartyObj->assign('object_name', $object_name);

	if($oid == $argsObj->tprojectID)
  	{
    	$level = 'testproject';
    	$smartyObj->assign('level', $level);
    	$smartyObj->assign('page_title',lang_get('container_title_' . $level));
  }

  return $level;
}


/*
  function: doTestSuiteReorder


  args:

  returns: -

  rev:
      20080602 - franciscom - fixed typo bug 
      20080223 - franciscom - fixed typo error - BUGID 1408
      removed wrong global coupling
*/
// function doTestSuiteReorder(&$smartyObj,$template_dir,&$tprojectMgr,&$tsuiteMgr,$argsObj)
// {
// 	$nodes_in_order = transform_nodes_order($argsObj->nodes_order,$argsObj->containerID);
// 	$tprojectMgr->tree_manager->change_order_bulk($nodes_in_order);
// 	if($argsObj->containerID == $argsObj->tprojectID)
// 	{
// 		$objMgr = $tprojectMgr;
// 	}
// 	else
// 	{
// 		$objMgr = $tsuiteMgr;
// 	}
// 	$objMgr->show($smartyObj,$template_dir,$argsObj->containerID,,null,'ok');
// }

/*
  function: updateTestSuite

  args:

  returns:

*/
function updateTestSuite(&$tsuiteMgr,&$argsObj,$container,&$hash)
{
	$msg = 'ok';
	$ret = $tsuiteMgr->update($argsObj->testsuiteID,$container['container_name'],$container['details']);
	if($ret['status_ok'])
	{
      $tsuiteMgr->deleteKeywords($argsObj->testsuiteID);
      if(trim($argsObj->assigned_keyword_list) != "")
      {
         $tsuiteMgr->addKeywords($argsObj->testsuiteID,explode(",",$argsObj->assigned_keyword_list));
      }
      writeCustomFieldsToDB($tsuiteMgr->db,$argsObj->tprojectID,$argsObj->testsuiteID,$hash);
  	}
  	else
	{
	    $msg = $ret['msg'];
	}
	return $msg;
}

/*
  function: copyTestSuite

  args:

  returns:

*/
function copyTestSuite(&$smartyObj,$template_dir,&$tsuiteMgr,$argsObj)
{
    $exclude_node_types=array('testplan' => 1, 'requirement' => 1, 'requirement_spec' => 1);
  	
  	$options = array();
	$options['check_duplicate_name'] = config_get('check_names_for_duplicates');
  	$options['action_on_duplicate_name'] = config_get('action_on_duplicate_name');
  	$options['copyKeywords'] = $argsObj->copyKeywords;

  	$op=$tsuiteMgr->copy_to($argsObj->objectID, $argsObj->containerID, $argsObj->userID,$options);
	if( $op['status_ok'] )
	{
	    $tsuiteMgr->tree_manager->change_child_order($argsObj->containerID,$op['id'],
	                                                 $argsObj->target_position,$exclude_node_types);
	}
	
	$guiObj = new stdClass();
	$guiObj->attachments = getAttachmentInfosFrom($tsuiteMgr,$argsObj->objectID);
	$guiObj->id = $argsObj->objectID;
	
	$tsuiteMgr->show($smartyObj,$guiObj,$template_dir,$argsObj->objectID,null,'ok');
}

/*
  function: moveTestSuite

  args:

  returns:

*/
function moveTestSuite(&$smartyObj,$template_dir,&$tprojectMgr,$argsObj)
{
	$exclude_node_types=array('testplan' => 1, 'requirement' => 1, 'requirement_spec' => 1);

	$tprojectMgr->tree_manager->change_parent($argsObj->objectID,$argsObj->containerID);
  	$tprojectMgr->tree_manager->change_child_order($argsObj->containerID,$argsObj->objectID,
                                                   $argsObj->target_position,$exclude_node_types);

	$guiObj = new stdClass();
	$guiObj->id = $argsObj->tprojectID;

  	$tprojectMgr->show($smartyObj,$guiObj,$template_dir,$argsObj->tprojectID,null,'ok');
}


/*
  function: initializeOptionTransfer

  args:

  returns: option transfer configuration

*/
function initializeOptionTransfer(&$tprojectMgr,&$tsuiteMgr,$argsObj,$doAction)
{
    $opt_cfg = opt_transf_empty_cfg();
    $opt_cfg->js_ot_name='ot';
    $opt_cfg->global_lbl='';
    $opt_cfg->from->lbl=lang_get('available_kword');
    $opt_cfg->from->map = $tprojectMgr->get_keywords_map($argsObj->tprojectID);
    $opt_cfg->to->lbl=lang_get('assigned_kword');

    if($doAction=='edit_testsuite')
    {
      $opt_cfg->to->map=$tsuiteMgr->get_keywords_map($argsObj->testsuiteID," ORDER BY keyword ASC ");
    }
    return $opt_cfg;
}


/*
  function: moveTestCasesViewer
            prepares smarty variables to display move testcases viewer

  args:

  returns: -

*/
function moveTestCasesViewer(&$dbHandler,&$smartyObj,&$tprojectMgr,&$treeMgr,$argsObj,$feedback='')
{
	$tables = $tprojectMgr->getDBTables(array('nodes_hierarchy','node_types','tcversions'));
	$testcase_cfg = config_get('testcase_cfg');
	$glue = $testcase_cfg->glue_character;
	
	$containerID = isset($argsObj->testsuiteID) ? $argsObj->testsuiteID : $argsObj->objectID;
	$containerName = $argsObj->tsuite_name;
	if( is_null($containerName) )
	{
		$dummy = $treeMgr->get_node_hierarchy_info($argsObj->objectID);
		$containerName = $dummy['name'];
	}
	
	
  	// 20081225 - franciscom have discovered that exclude selected testsuite branch is not good
  	//            when you want to move lots of testcases from one testsuite to it's children
  	//            testsuites. (in this situation tree drag & drop is not ergonomic).
  	$testsuites = $tprojectMgr->gen_combo_test_suites($argsObj->tprojectID);	                                                  
	$tcasePrefix = $tprojectMgr->getTestCasePrefix($argsObj->tprojectID) . $glue;

	// 20081225 - franciscom
	// While testing with PostGres have found this behaivour:
	// No matter is UPPER CASE has used on field aliases, keys on hash returned by
	// ADODB are lower case.
	// Accessing this keys on Smarty template using UPPER CASE fails.
	// Solution: have changed case on Smarty to lower case.
	//         
	$sql = "SELECT NHA.id AS tcid, NHA.name AS tcname, NHA.node_order AS tcorder," .
	       " MAX(TCV.version) AS tclastversion, TCV.tc_external_id AS tcexternalid" .
	       " FROM {$tables['nodes_hierarchy']} NHA, {$tables['nodes_hierarchy']}  NHB, " .
	       " {$tables['node_types']} NT, {$tables['tcversions']}  TCV " .
	       " WHERE NHB.parent_id=NHA.id " .
	       " AND TCV.id=NHB.id AND NHA.node_type_id = NT.id AND NT.description='testcase'" .
	       " AND NHA.parent_id={$containerID} " .
	       " GROUP BY NHA.id,NHA.name,NHA.node_order,TCV.tc_external_id " .
	       " ORDER BY TCORDER,TCNAME";

  	$children = $dbHandler->get_recordset($sql);
    
 	// check if operation can be done
	$user_feedback = $feedback;
	if(!is_null($children) && (sizeof($children) > 0) && sizeof($testsuites))
	{
	    $op_ok = true;
	}
	else
	{
	    $children = null;
	    $op_ok = false;
	    $user_feedback = lang_get('no_testcases_available');
	}

	$smartyObj->assign('op_ok', $op_ok);
	$smartyObj->assign('user_feedback', $user_feedback);
	$smartyObj->assign('tcprefix', $tcasePrefix);
	$smartyObj->assign('testcases', $children);
	$smartyObj->assign('old_containerID', $argsObj->tprojectID); //<<<<-- check if is needed
	$smartyObj->assign('containers', $testsuites);
	$smartyObj->assign('objectID', $containerID);
	$smartyObj->assign('object_name', $containerName);
	$smartyObj->assign('top_checked','checked=checked');
	$smartyObj->assign('bottom_checked','');
}


/*
  function: copyTestCases
            copy a set of choosen test cases.

  args:

  returns: -

*/
function copyTestCases(&$smartyObj,$template_dir,&$tsuiteMgr,&$tcaseMgr,$argsObj)
{
	$op = array('refreshTree' => false, 'userfeedback' => '');
    if( ($qty=sizeof($argsObj->tcaseSet)) > 0)
    {
		$msg_id = $qty == 1 ? 'one_testcase_copied' : 'testcase_set_copied';
   		$op['userfeedback'] = sprintf(lang_get($msg_id),$qty);

        $check_names_for_duplicates_cfg = config_get('check_names_for_duplicates');
        $action_on_duplicate_name_cfg = config_get('action_on_duplicate_name');

        foreach($argsObj->tcaseSet as $key => $tcaseid)
        {
            $copy_op = $tcaseMgr->copy_to($tcaseid, $argsObj->containerID, $argsObj->userID,
	                                      $argsObj->copyKeywords,$check_names_for_duplicates_cfg,
	                    	              $action_on_duplicate_name_cfg);
        }
        
        $guiObj = new stdClass();
   		$guiObj->attachments = getAttachmentInfosFrom($tsuiteMgr,$argsObj->objectID);
		$guiObj->id = $argsObj->objectID;
		$guiObj->refreshTree = true;
        // $tsuiteMgr->show($smartyObj,$guiObj,$template_dir,$argsObj->objectID);
    	$op['refreshTree'] = true;
    }
    return $op;
}


/*
  function: moveTestCases
            move a set of choosen test cases.

  args:

  returns: -

*/
function moveTestCases(&$smartyObj,$template_dir,&$tsuiteMgr,&$treeMgr,$argsObj)
{
    if(sizeof($argsObj->tcaseSet) > 0)
    {
        $status_ok = $treeMgr->change_parent($argsObj->tcaseSet,$argsObj->containerID);
        $user_feedback= $status_ok ? '' : lang_get('move_testcases_failed');

        // objectID - original container
        $guiObj = new stdClass();
   		$guiObj->attachments = getAttachmentInfosFrom($tsuiteMgr,$argsObj->objectID);
		$guiObj->id = $argsObj->objectID;

        $tsuiteMgr->show($smartyObj,$guiObj,$template_dir,$argsObj->objectID,null,$user_feedback);
    }
}


/**
 * initWebEditors
 *
 */
 function initWebEditors($webEditorKeys,$containerType,$editorCfg)
 {
    switch($containerType)
    {
        case 'testsuite':
            // $cfg=config_get('testsuite_template');
            $itemTemplateKey='testsuite_template';
        break;    
        
        default:
            //$cfg=null;
            $itemTemplateKey=null;
        break;    
    }
    
    
    $htmlNames = $webEditorKeys[$containerType];
    $oWebEditor = array();
    foreach ($htmlNames as $key)
    {
      $oWebEditor[$key] = web_editor($key,$_SESSION['basehref'],$editorCfg);
    }
    return array($oWebEditor,$htmlNames,$itemTemplateKey);
 }
 
 
 
 
 /*
  function: deleteTestSuitesBulk

  args:

  returns: true -> refresh tree
           false -> do not refresh

*/
function deleteTestSuitesBulk(&$smartyObj,&$argsObj,&$tsuiteMgr,&$treeMgr,&$tcaseMgr,$level)
{
}


/**
 * BUGID 3049
 * remove all testcase assignments from a testplan
 * 
 * @param resource $dbHandler database handle
 * @param stdClass $argsObj user input 
 * @param tlTestplan $tplan_mgr testplan
 * @param tlSmarty $smartyObj smarty object
 * @param string $tmpl_dir template  directory
 */
function removeTestcaseAssignments(&$dbHandler, &$argsObj, &$tplan_mgr, &$smartyObj, $tmpl_dir) {
	$gui = new stdClass();
	$gui->tplan_id = $argsObj->tplan_id;
	$tplan = $tplan_mgr->get_by_id($argsObj->tplan_id);
	$gui->tplan_name = $tplan['name'];
	$gui->container_data['name'] = $tplan['name'];
	$gui->tplan_description = $tplan['notes'];
	$gui->mainTitle = lang_get('remove_assigned_testcases');
	$tproject_mgr = new testproject($dbHandler);
	$tproject = $tproject_mgr->get_by_id($tplan['testproject_id']);
	$gui->tproject_name = $tproject['name'];
	$gui->tproject_description = $tproject['notes'];
	$gui->draw_tc_unassign_button = false;
	
	$gui->level = 'testplan';
	$gui->mainTitle = lang_get('remove_assigned_testcases');
	$gui->page_title = lang_get('testplan');
	$gui->refreshTree = false;

	if($argsObj->doAction == 'doUnassignFromPlan') {
		$options = array('output' => 'array');
		$linked_tcversions=$tplan_mgr->get_linked_tcversions($gui->tplan_id,null,$options);
			
		$ids = array();
		foreach ($linked_tcversions as $tc_id => $tc) {
			if (isset($tc['user_id']) && is_numeric($tc['user_id'])) {
				$ids[] = $tc['feature_id'];
			}
		}

		if (count($ids)) {
			$assignment_mgr = new assignment_mgr($dbHandler);
			$assignment_mgr->delete_by_feature_id($ids);
			$gui->result = sprintf(lang_get('unassigned_all_tcs_msg'), $gui->tplan_name);
		} else {
			//no items to unassign
			$gui->result = sprintf(lang_get('nothing_to_unassign_msg'), $gui->tplan_name);
		}

		$smartyObj->assign('gui', $gui);
		$smartyObj->display($tmpl_dir . 'containerView.tpl');
	}

}

?>