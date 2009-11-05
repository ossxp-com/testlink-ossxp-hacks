<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Filename $RCSfile: eventinfo.php,v $
 *
 * @version $Revision: 1.5 $
 * @modified $Date: 2008/12/15 20:22:41 $ by $Author: schlundus $
**/
require_once("../../config.inc.php");
require_once("common.php");
testlinkInitPage($db,false,false,"checkRights");
$templateCfg = templateConfiguration();

$user = null;
$eventID = isset($_POST['id']) ? intval($_POST['id']) : null;
if ($eventID)
{
	$event = new tlEvent($eventID);
	if ($event->readFromDB($db,tlEvent::TLOBJ_O_GET_DETAIL_TRANSACTION) >= tl::OK)
	{
		$user = new tlUser($event->userID);
		if ($user->readFromDB($db) < tl::OK)
			$user = null;
	}
	else
		$event = null;
}

$smarty = new TLSmarty();
$smarty->assign("event",$event);
$smarty->assign("user",$user);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

function checkRights(&$db,&$user,&$action)
{
	if (!$user->hasRight($db,"mgt_view_events"))
		return false;
	return true;
}
?>