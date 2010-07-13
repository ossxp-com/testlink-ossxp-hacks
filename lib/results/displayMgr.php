<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later.
 *  
 * @filesource $RCSfile: displayMgr.php,v $
 * @version $Revision: 1.23 $
 * @modified $Date: 2009/06/10 19:36:00 $ by $Author: franciscom $
 * @author	Kevin Levy
 * 
 * Revision:
 * 	20090213 - havlatm - added flushHttpHeader function instead of particular headers
 * 						support for OpenOffice
 * 	20080928 - franciscom - minor refactoring
 * 	20071207 - havlatm - added MSWord, magic numbers -> use global const.
 */

require_once('info.inc.php'); // has the sendMail() method
require_once('../../cfg/reports.cfg.php');

function generateHtmlEmail($template_file, &$smarty, $buildName = null)
{
	$html_report = $smarty->fetch($template_file);
	$emailIsHtml = true;
 	$send_cc_to_myself = false;
	$subjectOfMail =  $_SESSION['testplanName'] . ": " . $template_file . " " . $buildName;
  
	$emailFrom = $_SESSION['currentUser']->emailAddress;
	$emailTo = $emailFrom;
	if ($emailTo == "")
  		$message = lang_get("error_sendreport_no_email_credentials");
  	else
  		$message = sendMail($emailFrom, $emailTo, $subjectOfMail, $html_report, $send_cc_to_myself, $emailIsHtml);

	return	$message;
}


function displayReport($template_file, &$smarty, $doc_format, $buildName = null)
{

	switch($doc_format)
	{
		case FORMAT_HTML:
		case FORMAT_ODT:
		case FORMAT_ODS:
		case FORMAT_XLS:
		case FORMAT_MSWORD:
		case FORMAT_PDF:
	  		flushHttpHeader($doc_format, $doc_kind = 0);
    		break;  

	    case FORMAT_MAIL_HTML:
		  	$message = generateHtmlEmail($template_file, $smarty, $buildName);
		  		
			$smarty = new TLSmarty();
			$smarty->assign('message', $message);
			$smarty->assign('title', $_SESSION['testplanName']);
		  	$template_file = "emailSent.tpl";
      		break;
	} 

	$smarty->display($template_file);
}


/**
 * Generate HTML header and send it to browser
 * @param string $format identifier of document format; value must be in $tlCfg->reports_formats
 * @param integer $doc_kind Magic number of document kind; see consts.inc.php for list 
 * 		(for example: DOC_TEST_PLAN)
 * @author havlatm
 */
function flushHttpHeader($format, $doc_kind = 0)
{
	$file_extensions = config_get('reports_file_extension');
	$reports_applications = config_get('reports_applications');

	switch($doc_kind)
	{
		case DOC_TEST_SPEC: $kind_acronym = '_test_spec'; break;
		case DOC_TEST_PLAN: $kind_acronym = '_test_plan'; break;
		case DOC_TEST_REPORT: $kind_acronym = '_test_report'; break;
		case DOC_REQ_SPEC: $kind_acronym = '_req_spec'; break;
		default: $kind_acronym = '';
	}
	
	if ($format == FORMAT_MAIL_HTML)
		tLog('flushHttpHeader> Invalid format: '.$format, 'ERROR');

	$filename = $_SESSION['testprojectPrefix'] . $kind_acronym . '-' . date('Y-m-d') . '.' . $file_extensions[$format];
	tLog('Flush HTTP header for '.$format); 


    header("Content-Description: TestLink - Generated Document");
    if ($format != FORMAT_HTML)
		header("Content-Disposition: attachment; filename=$filename");
   	header("Content-type: {$reports_applications[$format]}; name='Testlink_$format'");
	flush();
}

?>
