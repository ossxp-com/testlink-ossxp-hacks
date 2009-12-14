<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Login page with configuratin checking and authorization
 *
 * @package 	TestLink
 * @author 		Martin Havlat
 * @copyright 	2006, TestLink community 
 * @version    	CVS: $Id: login.php,v 1.47.2.1 2009/11/28 23:16:17 havlat Exp $
 * @filesource	http://testlink.cvs.sourceforge.net/viewvc/testlink/testlink/login.php?view=markup
 * @link 		http://www.teamst.org/index.php
 * 
 * @internal 
 * rev: 20081231 - franciscom - minor refactoring
 *      20081015 - franciscom - access to config parameters following development standard
 **/
require_once('lib/functions/configCheck.php');
checkConfiguration();
require_once('config.inc.php');
require_once('common.php');
require_once('doAuthorize.php');

$op = doDBConnect($db);
//@TODO: schlundus, this kind of code should be contained within doDBConnect!
if (!$op['status'])
{
	$smarty = new TLSmarty();
	$smarty->assign('title', lang_get('fatal_page_title'));
	$smarty->assign('content', $op['dbms_msg']);
	$smarty->display('workAreaSimple.tpl'); 
	tLog('Connection fail page shown.','ERROR'); 
	exit();
}

$args = init_args();
$gui = init_gui($db,$args);

if(!is_null($args->login))
{
	doSessionStart();
	unset($_SESSION['basehref']);
	setPaths();
	
	if(doAuthorize($db,$args->login,$args->pwd,$msg) < tl::OK)
	{
		if (!$msg)
		{
			$gui->note = lang_get('bad_user_passwd');
		}
		else
		{
			$gui->note = $msg;
		}	
	}
	else
	{
		logAuditEvent(TLS("audit_login_succeeded",$args->login,
		                  $_SERVER['REMOTE_ADDR']),"LOGIN",$_SESSION['currentUser']->dbID,"users");
		redirect($_SESSION['basehref']."index.php".($args->preqURI ? "?reqURI=".urlencode($args->preqURI) :""));
		exit();
	}
}

$logPeriodToDelete = config_get('removeEventsOlderThan');
$g_tlLogger->deleteEventsFor(null, strtotime("-{$logPeriodToDelete} days UTC"));

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display('login.tpl');

/**
 * Initialize input parameters
 */
function init_args()
{
    $args = new stdClass();
    $_REQUEST = strings_stripSlashes($_REQUEST);
    
    $args->note = isset($_REQUEST['note']) ? htmlspecialchars($_REQUEST['note']) : null;
    $args->login = isset($_REQUEST['tl_login']) ? trim($_REQUEST['tl_login']) : null;
    $args->pwd = isset($_REQUEST['tl_password']) ? $_REQUEST['tl_password'] : null;

    $args->reqURI = isset($_REQUEST['req']) ? urlencode($_REQUEST['req']) : null;
    $args->preqURI = (isset($_REQUEST['reqURI']) && strlen($_REQUEST['reqURI'])) ? urlencode($_REQUEST['reqURI']) : null;
  
    return $args;
}

function init_gui(&$db,$args)
{
	$gui = new stdClass();
	
	$authCfg = config_get('authentication');
	$gui->securityNotes = getSecurityNotes($db);
	$gui->external_password_mgmt = ('LDAP' == $authCfg['method']) ? 1 : 0;
	$gui->login_disabled = ($gui->external_password_mgmt && !checkForLDAPExtension()) ? 1:0;
	$gui->user_self_signup = config_get('user_self_signup');

	switch($args->note)
    {
    	case 'expired':
    		if(!isset($_SESSION))
    		{
    			session_start();
    		}
    		session_unset();
    		session_destroy();
    		$gui->note = lang_get('session_expired');
    		$gui->reqURI = null;
    		break;
    		
    	case 'first':
    		$gui->note = lang_get('your_first_login');
    		$gui->reqURI = null;
    		break;
    		
    	case 'lost':
    		$gui->note = lang_get('passwd_lost');
    		$gui->reqURI = null;
    		break;
    		
    	default:
    		$gui->note = lang_get('please_login');
    		break;
    }
	$gui->reqURI = $args->reqURI ? $args->reqURI : $args->preqURI;
    
	return $gui;
}
?>
