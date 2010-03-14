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

$cosign_loggedin = 0;
// cosign v2 or cosign v3
if (strtolower($tlCfg->authentication['method']) == 'cosign' ||
    strtolower($tlCfg->authentication['method']) == 'cosign3')
{
	if (@$_SERVER["REMOTE_USER"] || @$_SERVER["REDIRECT_REMOTE_USER"])
	{
		$args->login = @$_SERVER["REMOTE_USER"] ? @$_SERVER["REMOTE_USER"] : @$_SERVER["REDIRECT_REMOTE_USER"];
		$cosign_loggedin = 1;
	}
}

if(!is_null($args->login) || $cosign_loggedin || $args->demo)
{
	doSessionStart();
	unset($_SESSION['basehref']);
	setPaths();

	if($args->demo)
	{
		if (array_key_exists($args->demo, $tlCfg->authentication['demo_users']))
		{
			$args->login = $args->demo;
			$cosign_loggedin = 1;
		}
		else
		{
			$args->demo = "";
			$gui->note = lang_get('bad_user_passwd');
		}
	}
	if(doAuthorize($db,$args->login,$args->pwd,$msg,$cosign_loggedin) < tl::OK)
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

// cosign v2
if (strtolower($tlCfg->authentication['method']) == 'cosign' &&
    ($args->sso || !@$tlCfg->authentication['demo_users']))
{
	// Redirect to cosign login page.
	sso_redirect(2);
}
// cosign v3
elseif (strtolower($tlCfg->authentication['method']) == 'cosign3' &&
        ($args->sso || !@$tlCfg->authentication['demo_users']))
{
	// Redirect to cosign login page.
	sso_redirect(3);
}

$demo_login_contents = "";
$login_form_contents = "";
if (@$tlCfg->authentication['demo_users'])
{
	if (strtolower($tlCfg->authentication['method']) == 'cosign' ||
	    strtolower($tlCfg->authentication['method']) == 'cosign3')
	{
		$login_form_contents = "<p><div align=\"center\"> ".
                  "<a href=\"" .
		              $_SERVER["PHP_SELF"] .
		              "?sso\" class=\"urlbtn\">" .
		              lang_get('sso_login_url_text') .
	                "</a></div><p>";
	}

  $demo_login_contents =  "<p>" .
                      lang_get('demo_login_text') .
                      "<p>";
	foreach ($tlCfg->authentication['demo_users'] as $role => $text)
	{
		$demo_login_contents .= "<a href=\"" .
		                    $_SERVER["PHP_SELF"] .
	                      "?demo=$role\" class=\"rlbtn\">" .
		                    "&#187;". $text .
	                      "</a>, ";
	}
	$demo_login_contents = "<div align=\"center\" width=\"80%\">" .
                     rtrim($demo_login_contents," ,") . "<div>" ;
}


$logPeriodToDelete = config_get('removeEventsOlderThan');
$g_tlLogger->deleteEventsFor(null, strtotime("-{$logPeriodToDelete} days UTC"));

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->assign('login_title', lang_get('please_login'));
$smarty->assign('demo_login_title', lang_get('demo_login_title'));
$smarty->assign('description_title', config_get('login_page_msg_title')? config_get('login_page_msg_title'): lang_get('alt_notes'));
$smarty->assign('description_contents', config_get('login_page_msg'));
$smarty->assign('demo_login_contents', $demo_login_contents);
$smarty->assign('login_form_contents', $login_form_contents);
$smarty->display('login.tpl');

/**
 * Single Sign-on redirect
 */
function sso_redirect($protocol=2)
{
	global $tlCfg;

	$cosign_login_url    = $tlCfg->authentication['login_url'] ?
												 $tlCfg->authentication['login_url'] :
												 "https://weblogin.foo.bar/cgi-bin/login";
	$cosign_service_name = $tlCfg->authentication['sso_service_name'] ?
												 $tlCfg->authentication['sso_service_name'] :
												 "testlink";
	$cookie_name = "cosign-" . $cosign_service_name;
	$service_url  = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	if ($protocol == 3)
	{
		$dest_url = $cosign_login_url . "?" . $cookie_name . "&" .  $service_url;
	}
	else
	{
		$sample_string =
		"0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

		$cookie_data = '';
		for ($i=0;$i<125;$i++) {
						$cookie_data .= $sample_string[mt_rand(0,61)];
		}
		setcookie( $cookie_name, $cookie_data );
		$dest_url = $cosign_login_url . "?" . $cookie_name . "=" . $cookie_data . ";&" .  $service_url;
	}
	header( "Location: $dest_url" );
	exit;
}

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
  
    $args->sso = isset($_REQUEST['sso']) ? 1 : 0;
    $args->demo = isset($_REQUEST['demo']) ? $_REQUEST['demo'] : '';

    return $args;
}

function init_gui(&$db,$args)
{
	$gui = new stdClass();
	
	$authCfg = config_get('authentication');
	$gui->securityNotes = getSecurityNotes($db);
	$gui->external_password_mgmt = strtolower($authCfg['method']) != 'md5' ? 1 : 0;
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
    		$gui->note = '';
    		break;
    }
	$gui->reqURI = $args->reqURI ? $args->reqURI : $args->preqURI;
    
	return $gui;
}
?>
