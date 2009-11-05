<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Filename $RCSfile: reqExport.php,v $
 *
 * @version $Revision: 1.5 $
 * @modified $Date: 2008/12/16 20:11:53 $ by $Author: schlundus $
 *
 * This page this allows users to export requirements.
 *
**/
require_once("../../config.inc.php");
require_once("csv.inc.php");
require_once("xml.inc.php");
require_once("common.php");
require_once("requirements.inc.php");
require_once('requirement_spec_mgr.class.php');
testlinkInitPage($db,false,false,"checkRights");

$template_dir = "requirements/";
$default_template = str_replace('.php','.tpl',basename($_SERVER['SCRIPT_NAME']));

$bExport = isset($_REQUEST['export']) ? $_REQUEST['export'] : null;
$exportType = isset($_REQUEST['exportType']) ? $_REQUEST['exportType'] : null;
$req_spec_id = isset($_REQUEST['req_spec_id']) ? $_REQUEST['req_spec_id'] : null;
$export_filename = isset($_REQUEST['export_filename']) ? $_REQUEST['export_filename'] : "requirements.xml";

$req_spec_mgr = new requirement_spec_mgr($db);
$req_spec = $req_spec_mgr->get_by_id($req_spec_id);
$export_types = $req_spec_mgr->get_export_file_types();

if ($bExport)
{
	$requirements_map = $req_spec_mgr->get_requirements($req_spec_id);

	$pfn = null;
	switch($exportType)
	{
		case 'csv':
			$pfn = "exportReqDataToCSV";
			$fileName = 'reqs.csv';
			break;
		case 'XML':
			$pfn = "exportReqDataToXML";
			$fileName = 'reqs.xml';
			break;
	}
	if ($pfn)
	{
		$fileName = is_null($export_filename) ? $fileName : $export_filename;
		$content = $pfn($requirements_map);
		downloadContentsToFile($content,$fileName);
		exit();
	}
}

$smarty = new TLSmarty();
$smarty->assign('req_spec_id', $req_spec_id);
$smarty->assign('req_spec', $req_spec);
$smarty->assign('exportTypes',$export_types);
$smarty->assign('export_filename',$export_filename);
$smarty->display($template_dir . $default_template);

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'mgt_view_req');
}
?>