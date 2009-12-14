<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * manage launch of static pages (help, instructions).
 *
 * @package 	TestLink
 * @author 		Martin Havlat
 * @copyright 	2009, TestLink community 
 * @version    	CVS: $Id: staticPage.php,v 1.4.2.1 2009/11/29 00:18:06 havlat Exp $
 * @link 		http://www.teamst.org/index.php
 *
 **/
require('../../config.inc.php');
require('../functions/common.php');
testlinkInitPage($db);

$gui=new stdClass();
$gui->pageTitle = '';
$gui->pageContent = '';
$gui->refreshTree = isset($_REQUEST['refreshTree']) ? 1 : 0;

if (isset($_REQUEST['key'])) {
	$pageKey = htmlspecialchars($_REQUEST['key']);
} else {
	exit ("Error: Invalid page parameter.");
}
// link appropriate definition file and default to en_GB if not present in the current language
$locale = isset($_SESSION['locale']) ? $_SESSION['locale'] : $tlCfg->default_language;
if (file_exists('../../locale/'.$locale.'/texts.php'))
	include('../../locale/'.$locale.'/texts.php');
else
	include('../../locale/en_GB/texts.php');
	
if (isset($TLS_htmltext[$pageKey])) {
	$gui->pageTitle = $TLS_htmltext_title[$pageKey];
	$gui->pageContent =  $TLS_htmltext[$pageKey];
}
else
{
	$gui->pageTitle = "";
	$gui->pageContent = "Please, ask administrator to update localization file (&lt;testlink_root&gt;/locale/$locale/texts.php)"
		." - missing key: ".$pageKey;
}

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display('staticPage.tpl');
?>
