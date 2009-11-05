<?php
/**
* TestLink Open Source Project - http://testlink.sourceforge.net/
* This script is distributed under the GNU General Public License 2 or later.
*
* Filename $RCSfile: userInfo.php,v $
*
* @version $Revision: 1.26 $
* @modified $Date: 2008/11/20 21:10:45 $
*
* Displays the users' information and allows users to change
* their passwords and user info.
*/
require_once('../../config.inc.php');
require_once('users.inc.php');
require_once('../../lib/api/APIKey.php');
testlinkInitPage($db);

$templateCfg = templateConfiguration();
$args = init_args();

$user = new tlUser($args->userID);
$user->readFromDB($db);

$op = new stdClass();
$op->auditMsg = null;
$op->user_feedback = null;
$op->status = tl::OK;

$doUpdate = 0;
switch($args->doAction)
{
    case 'editUser':
		$doUpdate = 1;
		foreach($args->user as $key => $value)
		{
			$user->$key = $value;
		}
		$op->status = tl::OK;
		$op->auditMsg = "audit_user_saved";
		$op->user_feedback = lang_get('result_user_changed');
    	break;

    case 'changePassword':
	    $op = changePassword($args,$user);
	    $doUpdate = ($op->status >= tl::OK) ? 1 : 0;
	    break;

    case 'genApiKey':
	    $op = generateApiKey($args,$user);
	    break;
}

if($doUpdate)
{
	$op->status = $user->writeToDB($db);
	if ($op->status >= tl::OK)
	{
		logAuditEvent(TLS($op->auditMsg,$user->login),"SAVE",$user->dbID,"users");
		$_SESSION['currentUser'] = $user;
		setUserSession($db,$user->login, $args->userID, $user->globalRoleID, $user->emailAddress, $user->locale);
	}
}

$loginHistory = new stdClass();
$loginHistory->failed = $g_tlLogger->getAuditEventsFor($args->userID,"users","LOGIN_FAILED",10);
$loginHistory->ok = $g_tlLogger->getAuditEventsFor($args->userID,"users","LOGIN",10);

if ($op->status != tl::OK)
	$op->user_feedback = getUserErrorMessage($op->status);

$user->readFromDB($db);

// set a string if not generated key yet
if (null == $user->userApiKey)
	$user->userApiKey = TLS('none');

$smarty = new TLSmarty();
$smarty->assign('external_password_mgmt',tlUser::isPasswordMgtExternal());
$smarty->assign('user',$user);
$smarty->assign('api_ui_show',$user);
$smarty->assign('mgt_view_events',$user->hasRight($db,"mgt_view_events"));
$smarty->assign('loginHistory', $loginHistory);
$smarty->assign('user_feedback', $op->user_feedback);
$smarty->assign('update_title_bar',0);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

/*
  function:

  args :

  returns:

*/
function init_args()
{
    $_REQUEST = strings_stripSlashes($_REQUEST);

    $args = new stdClass();
    $args->id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    $args->user = new stdClass();
    $key2loop = array('firstName','lastName','emailAddress','locale');
	foreach($key2loop as $key)
    {
       $args->user->$key = isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : null;
    }

    $args->oldpassword = isset($_REQUEST['oldpassword']) ? $_REQUEST['oldpassword'] : null;
    $args->newpassword = isset($_REQUEST['newpassword']) ? $_REQUEST['newpassword'] : null;
    $args->doAction = null;

    $key2loop = array('editUser','genApiKey','changePassword');
    foreach($key2loop as $key)
    {
       if(isset($_REQUEST[$key]))
       {
           $args->doAction = $key;
           break;
       }
    }
    $args->userID = isset($_SESSION['currentUser']) ? $_SESSION['currentUser']->dbID : 0;

    return $args;
}

/*
  function: changePassword

  args:

  returns: object with properties:
           status
           user_feedback: string message for on screen feedback
           auditMsg: to be written by logAudid

*/
function changePassword(&$argsObj,&$userMgr)
{
	$op = new stdClass();
    $op->status = $userMgr->comparePassword($argsObj->oldpassword);
    $op->user_feedback = '';
    $op->auditMsg = '';
	if ($op->status == tl::OK)
	{
		$userMgr->setPassword($argsObj->newpassword);
		$op->user_feedback = lang_get('result_password_changed');
		$op->auditMsg = "audit_user_pwd_saved";
	}
    return $op;
}

/*
  function: generateApiKey

  args :

  returns:

*/
function generateApiKey(&$argsObj,&$user)
{
	$op = new stdClass();
    $op->status = tl::OK;
    $op->user_feedback = null;

    if ($user)
    {
	    $APIKey = new APIKey();
	    $api_key = $APIKey->addKeyForUser($argsObj->userID);
		if (strlen($api_key))
		{
			logAuditEvent(TLS("audit_user_apikey_set",$user->login),"CREATE",$user->login,"users");
			$op->user_feedback = lang_get('result_apikey_create_ok');
		}
    }
    return $op;
}
?>