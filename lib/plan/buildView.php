<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Filename $RCSfile: buildView.php,v $
 *
 * @version $Revision: 1.12 $
 * @modified $Date: 2009/02/07 19:44:03 $ $Author: schlundus $
 *
 * rev :
 *       20070122 - franciscom - use build_mgr methods
 *       20070121 - franciscom - active and open management
 *
*/
require('../../config.inc.php');
require_once("common.php");
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$tplan_mgr = new testplan($db);
$build_mgr = new build_mgr($db);

$tplan_id = isset($_SESSION['testPlanId']) ? $_SESSION['testPlanId'] : 0;
$tplan_name = $_SESSION['testPlanName'];

$the_builds = $tplan_mgr->get_builds($tplan_id);

$smarty = new TLSmarty();
$smarty->assign('user_feedback',null); // disable notice
$smarty->assign('tplan_name', $tplan_name);
$smarty->assign('tplan_id', $tplan_id);
$smarty->assign('the_builds', $the_builds);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'testplan_create_build');
}
?>
