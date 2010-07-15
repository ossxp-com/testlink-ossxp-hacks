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
 * @version    	CVS: $Id: login.php,v 1.56 2010/02/02 12:52:36 franciscom Exp $
 * @filesource	http://testlink.cvs.sourceforge.net/viewvc/testlink/testlink/login.php?view=markup
 * @link 		http://www.teamst.org/index.php
 * 
 * @internal Revisions
 * 20100202 - franciscom - BUGID 0003129: After login failure blank page is displayed
 * 20100127 - eloff - Send localized login form strings with response to ajaxcheck
 * 20100124 - eloff - Added login functionality via ajax
 **/

require_once('lib/functions/configCheck.php');
checkConfiguration();
require_once('config.inc.php');
require_once('common.php');
require_once('doAuthorize.php');

$templateCfg = templateConfiguration();
$doRender = false; // BUGID 0003129

$op = doDBConnect($db);
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
$login_switch = $cosign_loggedin ? 'doLogin' : $args->action;
switch($login_switch) 
{
	case 'doLogin':
	case 'ajaxlogin':
		 doSessionStart();
		 unset($_SESSION['basehref']);
		 setPaths();
		 $op = doAuthorize($db,$args->login,$args->pwd,$cosign_loggedin);
		 
		 if( $op['status'] < tl::OK)
		 {
		 	$gui->note = is_null($op['msg']) ? lang_get('bad_user_passwd') : $op['msg'];
		 	if ($args->action == 'ajaxlogin') 
		 	{
		 		echo json_encode(array('success' => false,'reason' => $gui->note));
		 	}
		 	else
		 	{
		 		$doRender = true;
		 	}
		 }
		 else
		 {
		 	$args->currentUser = $_SESSION['currentUser'];
		 	logAuditEvent(TLS("audit_login_succeeded",$args->login,
		 	                  $_SERVER['REMOTE_ADDR']),"LOGIN",$args->currentUser->dbID,"users");
		 	if ($args->action == 'ajaxlogin') {
		 		echo json_encode(array('success' => true));
		 	} else {
		 		redirect($_SESSION['basehref']."index.php".($args->preqURI ? "?reqURI=".urlencode($args->preqURI) :""));
		 	}
		 }
		 break;
	
	case 'ajaxcheck':
		 doSessionStart();
		 unset($_SESSION['basehref']);
		 setPaths();
		 $validSession = checkSessionValid($db, false);
	     
		 // Send a json reply, include localized strings for use in js to display a login form.
		 echo json_encode(array('validSession' => $validSession,
		 	                    'username_label' => lang_get('login_name'),
		 	                    'password_label' => lang_get('password'),
		 	                    'login_label' => lang_get('btn_login')));
		 break;
	
	case 'loginform':
		 $doRender = true;
		 break;
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

// BUGID 0003129
if( $doRender )
{
	$logPeriodToDelete = config_get('removeEventsOlderThan');
	$g_tlLogger->deleteEventsFor(null, strtotime("-{$logPeriodToDelete} days UTC"));
	
	$smarty = new TLSmarty();
	$smarty->assign('gui', $gui);
	$smarty->assign('login_title', lang_get('please_login'));
	$smarty->assign('demo_login_title', lang_get('demo_login_title'));
	$smarty->assign('demo_login_contents', $demo_login_contents);
	$smarty->assign('login_form_contents', $login_form_contents);
	$smarty->display($templateCfg->default_template);
}

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
	$iParams = array("note" => array(tlInputParameter::STRING_N,0,255),
		             "tl_login" => array(tlInputParameter::STRING_N,0,30),
		             "tl_password" => array(tlInputParameter::STRING_N,0,32),
		             "req" => array(tlInputParameter::STRING_N,0,4000),
		             "reqURI" => array(tlInputParameter::STRING_N,0,4000),
		             "action" => array(tlInputParameter::STRING_N,0, 10),
		             "sso" => array(tlInputParameter::STRING_N,0, 10),
		             "demo" => array(tlInputParameter::STRING_N,0, 32),
	);
	$pParams = R_PARAMS($iParams);

    $args = new stdClass();
    $args->note = $pParams['note'];
    $args->login = $pParams['tl_login'];
    $args->pwd = $pParams['tl_password'];
    $args->reqURI = urlencode($pParams['req']);
    $args->preqURI = urlencode($pParams['reqURI']);
    $args->demo = urlencode($pParams['demo']);
    $args->sso = isset($pParams['sso']);

	if ($pParams['action'] == 'ajaxcheck' || $pParams['action'] == 'ajaxlogin') {
		$args->action = $pParams['action'];
	} else if (!is_null($args->login)) {
		$args->action = 'doLogin';
	} else {
		$args->action = 'loginform';
	}

    return $args;
}

/**
 * 
 *
 */
function init_gui(&$db,$args)
{
	$gui = new stdClass();
	
	$authCfg = config_get('authentication');
	$gui->securityNotes = getSecurityNotes($db);
	$gui->external_password_mgmt = strtolower($authCfg['method']) != 'md5' ? 1 : 0;
	$gui->login_disabled = ($gui->external_password_mgmt && !checkForLDAPExtension()) ? 1 : 0;
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
