<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later. 
 *  
 * @filesource $RCSfile: reqView.php,v $
 * @version $Revision: 1.13 $
 * @modified $Date: 2009/01/12 21:53:43 $ by $Author: schlundus $
 * @author Martin Havlat
 * 
 * Screen to view content of requirement.
 *
 * rev: 20080512 - franciscom - added showReqSpecTitle
 */
require_once('../../config.inc.php');
require_once('common.php');
require_once('attachments.inc.php');
require_once('requirements.inc.php');
require_once('users.inc.php');
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$args = init_args();
$gui = initialize_gui($db,$args);

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


/*
  function: 

  args:
  
  returns: 

*/
function init_args()
{
    $args = new stdClass();
    $_REQUEST = strings_stripSlashes($_REQUEST);
    $args->req_id = isset($_REQUEST['requirement_id']) ? intval($_REQUEST['requirement_id']) : null;
    $args->showReqSpecTitle = isset($_REQUEST['showReqSpecTitle']) ? intval($_REQUEST['showReqSpecTitle']) : 0;
    $args->tproject_id = isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0;
    $args->tproject_name = isset($_SESSION['testprojectName']) ? $_SESSION['testprojectName'] : null;
    
    return $args;
}

/*
function: initialize_gui

args :

returns: 

*/
function initialize_gui(&$dbHandler,$argsObj)
{
    $tproject_mgr = new testproject($dbHandler);
    $req_mgr = new requirement_mgr($dbHandler);
 
    $gui = new stdClass();
    $gui->grants = new stdClass();
    $gui->grants->req_mgmt = has_rights($db,"mgt_modify_req");
    
    $gui->tcasePrefix = $tproject_mgr->getTestCasePrefix($argsObj->tproject_id);
    $gui->glueChar = config_get('testcase_cfg')->glue_character;
    $gui->pieceSep = config_get('gui_title_separator_1');
    
    $gui->req_id = $argsObj->req_id;
    $gui->req = $req_mgr->get_by_id($gui->req_id);
    $gui->main_descr = lang_get('req') . TITLE_SEP . $gui->req['title'];
    
    $gui->showReqSpecTitle = $argsObj->showReqSpecTitle ;
    if($gui->showReqSpecTitle)
    {
        $gui->parent_descr = lang_get('req_spec') . TITLE_SEP . $gui->req['req_spec_title'];
    }
  
    $gui->req['coverage'] = $req_mgr->get_coverage($gui->req_id);
    $gui->cfields = $req_mgr->html_table_of_custom_field_values($gui->req_id,$argsObj->tproject_id);
    $gui->attachments = getAttachmentInfosFrom($req_mgr,$gui->req_id);
    $gui->reqStatus = init_labels(config_get('req_status'));

    return $gui;
}
  


 /*
   function: checkRights

   args:
   
   returns: 

 */
function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'mgt_view_req');
}
?>