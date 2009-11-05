<?php
/**
* TestLink Open Source Project - http://testlink.sourceforge.net/
* This script is distributed under the GNU General Public License 2 or later.
*
* Filename $RCSfile: usersAssign.php,v $
*
* @version $Revision: 1.18.2.2 $
* @modified $Date: 2009/04/27 18:28:48 $ $Author: schlundus $
*
* Allows assigning users roles to testplans or testprojects
*/
require_once('../../config.inc.php');
require_once('users.inc.php');
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$feature = isset($_REQUEST['feature']) ? $_REQUEST['feature'] : null;
$featureID = isset($_REQUEST['featureID']) ? intval($_REQUEST['featureID']) : 0;
$map_userid_roleid = isset($_REQUEST['userRole']) ? $_REQUEST['userRole'] : null;
$bUpdate = isset($_REQUEST['do_update']) ? 1 : 0;

$testprojectID = isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0;
$testprojectName = isset($_SESSION['testprojectName']) ? $_SESSION['testprojectName'] : null;
$tpID = isset($_SESSION['testPlanId']) ? $_SESSION['testPlanId'] : 0;
$currentUser = $_SESSION['currentUser'];
$userID = $currentUser->dbID;

$user_feedback = '';
$no_features = '';
$roles_updated = '';
$bTestproject = false;
$bTestPlan = false;
$mgr = null;
$highlight = initialize_tabsmenu();

if ($feature == "testproject")
{
	$highlight->assign_users_tproject = 1;
	$roles_updated = lang_get("test_project_user_roles_updated");
	$no_features = lang_get("no_test_projects");
	$bTestproject = true;
	$mgr = new testproject($db);
}
else if ($feature == "testplan")
{
  	$highlight->assign_users_tplan = 1;
	$roles_updated = lang_get("test_plan_user_roles_updated");
	$no_features = lang_get("no_test_plans");
	$bTestPlan = true;
	$mgr = new testplan($db);
}

if ($featureID && $bUpdate && $mgr)
{
	checkRightsForUpdate($db,$currentUser,$testprojectID,$feature,$featureID);
	$mgr->deleteUserRoles($featureID);
	foreach($map_userid_roleid as $user_id => $role_id)
	{
		if ($role_id)
			$mgr->addUserRole($user_id,$featureID,$role_id);
	}
	$user_feedback = $roles_updated;
}
$can_manage_users = has_rights($db,"mgt_users");
$users = tlUser::getAll($db,null,null,null,tlUser::TLOBJ_O_GET_DETAIL_MINIMUM);

$userFeatureRoles = null;
$features = null;
if ($bTestproject)
{
	$gui_cfg = config_get('gui');
	$order_by = $gui_cfg->tprojects_combo_order_by;
	$features = $mgr->get_accessible_for_user($userID,'array_of_map',$order_by);
	// If have no a test project ID, try to figure out which test project to show
	// Try with session info, if failed go to first test project available.
	if (!$featureID)
	{
		if ($testprojectID)
			$featureID = $testprojectID;
		else if (sizeof($features))
			$featureID = $features[0]['id'];
	}
	
	foreach($users as &$user)
	{
		$user->readTestProjectRoles($db,$featureID);
	}
	$userFeatureRoles = get_tproject_effective_role($db,$featureID,null,$users);
}
else if($bTestPlan)
{
	$activeFeatures = getAllActiveTestPlans($db,$testprojectID,$_SESSION['filter_tp_by_product']);
	$features = array();
	if ($can_manage_users)
		$features = $activeFeatures;
	else
	{
		for($i = 0;$i < sizeof($activeFeatures);$i++)
		{
			$f = $activeFeatures[$i];
			if (has_rights($db,"testplan_planning",null,$f['id']))
				$features[] = $f;
		}
	}
	//if nothing special was selected, use the one in the session or the first
	if (!$featureID)
	{
		if (sizeof($features))
		{
			if ($tpID)
			{
				for($i = 0;$i < sizeof($features);$i++)
				{
					if ($tpID == $features[$i]['id'])
						$featureID = $tpID;
				}
			}
			if (!$featureID)
				$featureID = $features[0]['id'];
		}
	}
	foreach($users as &$user)
	{
		$user->readTestProjectRoles($db,$testprojectID);
		$user->readTestPlanRoles($db,$featureID);
	}
	$userFeatureRoles = get_tplan_effective_role($db,$featureID,$testprojectID,null,$users);
}
if(is_null($features))
	$user_feedback = $no_features;

$smarty = new TLSmarty();
$smarty->assign('highlight',$highlight);
$smarty->assign('user_feedback',$user_feedback);
$smarty->assign('grants',getGrantsForUserMgmt($db,$currentUser));
$smarty->assign('tproject_name',$testprojectName);
$smarty->assign('optRights', tlRole::getAll($db,null,null,null,tlRole::TLOBJ_O_GET_DETAIL_MINIMUM));
$smarty->assign('userData',$users);
$smarty->assign('userFeatureRoles',$userFeatureRoles);
$smarty->assign('featureID',$featureID);
$smarty->assign('feature',$feature);
$smarty->assign('features',$features);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


function checkRights(&$db,&$user)
{
	$result = false;
	if ($user->hasRight($db,"role_management")
		|| ($user->hasRight($db,"testplan_user_role_assignment") || $user->hasRight($db,"user_role_assignment",null,-1))
	  )
	  	$result = true;
	return $result;
}
function checkRightsForUpdate($db,$user,$testprojectID,$feature,$featureID)
{
	if ($feature == "testplan")
	{
		if (!$user->hasRight($db,"testplan_user_role_assignment",$testprojectID,$featureID))
			exit();
	}
	if ($feature == "testproject")
	{
		if (!$user->hasRight($db,"user_role_assignment",$featureID,-1))
			exit();
	}
}
?>