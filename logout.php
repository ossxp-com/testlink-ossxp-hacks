<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 * 
 * Filename $RCSfile: logout.php,v $
 *
 * @version $Revision: 1.18 $
 * @modified $Date: 2009/08/11 19:48:50 $
**/
require_once('config.inc.php');
require_once('common.php');
testlinkInitPage($db);
$args = init_args();
if ($args->userID)
{
	logAuditEvent(TLS("audit_user_logout",$args->userName),"LOGOUT",$args->userID,"users");  
}
session_unset();
session_destroy();

// cosign v2 or v3
if (strtolower($tlCfg->authentication['method']) == "cosign" ||
    strtolower($tlCfg->authentication['method']) == "cosign3")
{
	$authCfg = config_get('authentication');
	$logout_url = $authCfg['logout_url'];

	if (@$_SERVER['COSIGN_SERVICE'] || @$_SERVER['REDIRECT_COSIGN_SERVICE'])
	{
		$cookie_name = @$_SERVER["COSIGN_SERVICE"] ? @$_SERVER["COSIGN_SERVICE"] : @$_SERVER["REDIRECT_COSIGN_SERVICE"];
		setcookie( $cookie_name, "null", time()-1, '/', "", 0 );
		setcookie( $cookie_name, "null", time()-1 );
	}
	redirect($logout_url);
	exit();
}

redirect("login.php");
exit();

function init_args()
{
	$args = new stdClass();
	
	$args->userID = isset($_SESSION['userID']) ?  $_SESSION['userID'] : null;
	$args->userName = $args->userID ? $_SESSION['currentUser']->getDisplayName() : "";
	
	return $args;
}
?>
