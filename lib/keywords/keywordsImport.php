<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Scope: Import keywords page
 *
 * Filename $RCSfile: keywordsImport.php,v $
 * @version $Revision: 1.7 $
 * @modified $Date: 2008/12/13 23:47:01 $ by $Author: schlundus $
 */
require_once('../../config.inc.php');
require_once('keyword.class.php');
require_once('common.php');
require_once('csv.inc.php');
require_once('xml.inc.php');
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$fInfo = isset($_FILES['uploadedFile']) ? $_FILES['uploadedFile'] : null;
$source = isset($fInfo['tmp_name']) ? $fInfo['tmp_name'] : null;
$bUpload = isset($_REQUEST['UploadFile']) ? 1 : 0;

$importType = isset($_POST['importType']) ? $_POST['importType'] : null;
$location = isset($_POST['location']) ? strings_stripSlashes($_POST['location']) : null; 

$testproject_id = $_SESSION['testprojectID'];
$tproject_name = $_SESSION['testprojectName'];
$dest = TL_TEMP_PATH . session_id()."-importkeywords.".$importType;

$msg = getFileUploadErrorMessage($fInfo);
if(!$msg && $bUpload)
{
	if(($source != 'none') && ($source != ''))
	{ 
		if (move_uploaded_file($source, $dest))
		{
			$pfn = null;
			switch($importType)
			{
				case 'iSerializationToCSV':
					$pfn = "importKeywordsFromCSV";
					break;
				case 'iSerializationToXML':
					$pfn = "importKeywordsFromXMLFile";
					break;
			}
			if($pfn)
			{
				$tproject = new testproject($db);
				$result = $tproject->$pfn($testproject_id,$dest);
				@unlink($dest);
				if ($result != tl::OK)
					$msg = lang_get('wrong_keywords_file'); 
				else
				{
					header("Location: keywordsView.php");
					exit();		
				}
			}
		}
	} 
	else
		$msg = lang_get('please_choose_keywords_file');
}

$tlKeyword = new tlKeyword();
$importTypes = $tlKeyword->getSupportedSerializationInterfaces();
$formatStrings = $tlKeyword->getSupportedSerializationFormatDescriptions();
			
$smarty = new TLSmarty();
$smarty->assign('import_type_selected',$importType);
$smarty->assign('msg',$msg);  
$smarty->assign('keywordFormatStrings',$formatStrings);
$smarty->assign('importTypes',$importTypes);
$smarty->assign('tproject_name', $tproject_name);
$smarty->assign('tproject_id', $testproject_id);
$smarty->assign('importLimit',TL_IMPORT_LIMIT);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'mgt_modify_key') && $user->hasRight($db,'mgt_modify_key');
}
?>
	
