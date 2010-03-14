<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 * 
 * Filename $RCSfile: logout.php,v $
 *
 * @version $Revision: 1.15 $
 * @modified $Date: 2008/10/12 08:11:56 $
**/
require_once('config.inc.php');
require_once('common.php');
testlinkInitPage($db);

$userID = $_SESSION['userID'] ?  $_SESSION['userID'] : null;
if ($userID)
{
	$userName = $_SESSION['currentUser']->getDisplayName();
	logAuditEvent(TLS("audit_user_logout",$userName),"LOGOUT",$userID,"users");  
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
?>
