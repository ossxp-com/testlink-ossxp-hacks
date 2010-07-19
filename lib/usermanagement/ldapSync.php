<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Sync user accounts with LDAP
 *
 * Author		Jiang Xin
 * Copyright 	2010, http://www.ossxp.com/
 *
 * @internal Revisions:
 *  20100718 - jiangxin - initialized
 *                          
 */
require_once("../../config.inc.php");
require_once("users.inc.php");
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();
$args = init_args();
$grants = getGrantsForUserMgmt($db,$args->currentUser);

// Fetch all ldap users as an array.
$ldap_users = get_ldap_users($args->ldap_filter);

$sqlResult = null;
$user_feedback = '';

// User click doLdapSync button.
if ( $args->doLdapSync && ! empty($args->ids) )
{
	$count_success = 0;
	$count_failed = 0;
	foreach ($args->ids as $uid) {
		$user = new tlUser();
		$user->login = $uid;
		$login_exists = ($user->readFromDB($db,tlUser::USER_O_SEARCH_BYLOGIN) >= tl::OK);
		if (!$login_exists && isset($ldap_users[$uid]))
		{
			$user->firstName    = $ldap_users[$uid]["firstName"];
			$user->lastName     = $ldap_users[$uid]["lastName"];
			$user->emailAddress = $ldap_users[$uid]["emailAddress"];
			$user->globalRoleID = config_get('default_roleid');
			$user->locale = config_get('default_language');
			$user->isActive = 1;
			$login_exists = 1;
		}
		else {
			$count_failed++;
			continue;
		}
		$status = $user->writeToDB($db);
		if($status < tl::OK) {
			$count_failed++;
		} else {
			$count_success++;
		}
	}
	$user_feedback = sprintf( lang_get('ldap_sync_result'), $count_success, $count_failed );
}

// Get all testlink users include new sync users from ldap.
$tl_users = get_tl_users();

// Users still in LDAP, but not in testlink.
$ldap_new_users = get_new_ldap_users( $tl_users, $ldap_users);

$count_total = count($ldap_new_users);
$count_show = $args->show && $args->show < $count_total ? $args->show : $count_total;
$ldap_new_users = array_slice( $ldap_new_users, 0, $count_show );
$summary = sprintf( lang_get(ldap_user_summary), $count_show, $count_total,
			"<a href='" . $_SERVER['PHP_SELF'] . "?show=20&ldap_filter="  . urlencode($args->ldap_filter) . "'>20</a>, ".
			"<a href='" . $_SERVER['PHP_SELF'] . "?show=50&ldap_filter="  . urlencode($args->ldap_filter) . "'>50</a>, ".
			"<a href='" . $_SERVER['PHP_SELF'] . "?show=100&ldap_filter=" . urlencode($args->ldap_filter) . "'>100</a>"
			);

$highlight = initialize_tabsmenu();
$highlight->ldap_sync_users = 1;

$smarty = new TLSmarty();
$smarty->assign('highlight',$highlight);
$smarty->assign('users',$ldap_new_users);
$smarty->assign('ldap_filter',$args->ldap_filter);
$smarty->assign('user_feedback',$user_feedback);
$smarty->assign('result',$sqlResult);
$smarty->assign('grants',$grants);
$smarty->assign('show',$args->show);
$smarty->assign('ldap_user_sync_capability',ldap_user_sync_capability());
$smarty->assign('summary', $summary);

$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


function get_ldap_users( $filter="", $max=0 )
{
	return ldap_fetch_all_accounts($filter, $max);
}

function get_tl_users()
{
	global $db;
	$users = array();
	foreach ( getAllUsersRoles($db) as $user )
	{
		$users[$user->login] = array( 'lastName'	=>	$user->lastName,
									  'firstName'	=>	$user->firstName,
									  'emailAddress'=>	$user->emailAddress);

	}
	return $users;
}

function get_new_ldap_users( &$tl_users, &$ldap_users, $max=0 )
{
	if (! $tl_users)
		$tl_users = get_tl_users();
	if (! $ldap_users)
		$ldap_users = get_ldap_users();

	$users = array_diff_key($ldap_users, $tl_users);

	if ($max != 0)
		return array_slice($users, 0, $max);
	else
		return $users;
}

/*
  function: init_args()
            get info from request and session

  args:

  returns: object

*/
function init_args()
{
	// input from GET['HelloString3'], 
	// type: string,  
	// minLen: 1, 
	// maxLen: 15,
	// regular expression: null
	// checkFunction: applys checks via checkFooOrBar() to ensure its either 'foo' or 'bar' 
	// normalization: done via  normFunction() which replaces ',' with '.' 
	// "HelloString3" => array("GET",tlInputParameter::STRING_N,1,15,'checkFooOrBar','normFunction'),
	$iParams = array("ldap_filter" => array(tlInputParameter::STRING_N,0,90),
			         "ids" => array(tlInputParameter::ARRAY_STRING_N),
			         "show" => array(tlInputParameter::STRING_N),
			         "doLdapSync" => array(tlInputParameter::STRING_N),
			         "doFilterApply" => array(tlInputParameter::STRING_N));

	$pParams = R_PARAMS($iParams);

	$args = new stdClass();
	$args->ldap_filter = isset($pParams["ldap_filter"]) ? $pParams["ldap_filter"] : "&(uid=*)(mail=*)(!(objectClass=gosaUserTemplate))";
    $args->ids = $pParams["ids"];
    $args->doLdapSync = empty($pParams["doLdapSync"]) ? false : true;
    $args->doFilterApply = empty($pParams["doFilterApply"]) ? false : true;
    $args->show = isset($pParams["show"]) ? $pParams["show"] : 20;
    $args->currentUser = $_SESSION['currentUser'];
    $args->currentUserID = $_SESSION['currentUser']->dbID;
    $args->basehref =  $_SESSION['basehref'];
   
    return $args;
}

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'mgt_users');
}

function ldap_user_sync_capability()
{
	$authCfg = config_get('authentication');
	return strtolower($authCfg['method']) != 'md5';
}

// vim: noet ts=4 sw=4
?>
