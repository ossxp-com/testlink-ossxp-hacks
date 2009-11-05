<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * Filename $RCSfile: testcaseCommands.class.php,v $
 *
 * @version $Revision: 1.4 $
 * @modified $Date: 2009/03/08 11:46:35 $  by $Author: franciscom $
 * testcases commands
 *
 *
 *
**/
class testcaseCommands
{
  private $db;
  private $tcaseMgr;
  private $templateCfg;

	function __construct(&$db)
	{
	    $this->db=$db;
	    $this->tcaseMgr = new testcase($db);
	}

	function setTemplateCfg($cfg)
	{
	    $this->templateCfg=$cfg;
	}


  /*
    function: edit

    args:
    
    returns: 

  */
	function edit(&$argsObj,&$otCfg,$oWebEditorKeys)
	{
	  $tpl_cfg=config_get('tpl');
	  
    $guiObj = new stdClass();
    $otCfg->to->map = $this->tcaseMgr->get_keywords_map($argsObj->tcase_id," ORDER BY keyword ASC ");

    keywords_opt_transf_cfg($otCfg, $argsObj->assigned_keywords_list);

  	$tc_data = $this->tcaseMgr->get_by_id($argsObj->tcase_id,$argsObj->tcversion_id);

    
  	foreach ($oWebEditorKeys as $key => $value)
   	{
  	  	$guiObj->$key = $tc_data[0][$key];
  	}

		$guiObj->cfields = $this->tcaseMgr->html_table_of_custom_field_inputs($argsObj->tcase_id);
    $guiObj->tc=$tc_data[0];
    $guiObj->opt_cfg=$otCfg;
    $guiObj->template=$tpl_cfg['tcEdit'];
    return $guiObj;
  }


  /*
    function: doUpdate

    args:
    
    returns: 

  */
	function doUpdate(&$argsObj,$request)
	{
      $smartyObj = new TLSmarty();
      $guiObj=new stdClass();
      $viewer_args=array();
      
   	  $guiObj->refresh_tree=$argsObj->do_refresh?"yes":"no";

		  // to get the name before the user operation
		  $tc_old = $this->tcaseMgr->get_by_id($argsObj->tcase_id,$argsObj->tcversion_id);

      $ret=$this->tcaseMgr->update($argsObj->tcase_id, $argsObj->tcversion_id, $argsObj->name, 
		                               $argsObj->summary, $argsObj->steps, $argsObj->expected_results,
		                               $argsObj->user_id, $argsObj->assigned_keywords_list,
		                               TC_DEFAULT_ORDER, $argsObj->exec_type, $argsObj->importance);

      $smartyObj->assign('attachments',null);
		  if($ret['status_ok'])
		  {
		    $refresh_tree='yes';
		    $msg = '';
  			$ENABLED = 1;
	  		$NO_FILTERS = null;
		  	$cf_map=$this->tcaseMgr->cfield_mgr->get_linked_cfields_at_design($argsObj->testproject_id,
			                                                                     $ENABLED,$NO_FILTERS,'testcase') ;
			  $this->tcaseMgr->cfield_mgr->design_values_to_db($request,$argsObj->tcase_id);
         
        $attachments[$argsObj->tcase_id] = getAttachmentInfosFrom($this->tcaseMgr,$argsObj->tcase_id);
        $smartyObj->assign('attachments',$attachments);
		  }
		  else
		  {
		      $refresh_tree='no';
		     	$msg = $ret['msg'];
		  }
	
	    $viewer_args['refresh_tree'] = $refresh_tree;
 	    $viewer_args['user_feedback'] = $msg;

      $smartyObj->assign('has_been_executed',$argsObj->has_been_executed);
      $smartyObj->assign('execution_types',$this->tcaseMgr->get_execution_types());
      
	    $this->tcaseMgr->show($smartyObj,$this->templateCfg->template_dir,
	                          $argsObj->tcase_id,$argsObj->tcversion_id,$viewer_args);
 
      return $guiObj;
  }  


  /**
   * doAdd2testplan
   *
   */
	function doAdd2testplan(&$argsObj,$request)
	{
      $smartyObj = new TLSmarty();
      $smartyObj->assign('attachments',null);
      $guiObj=new stdClass();
      $viewer_args=array();
      $tplan_mgr = new testplan($this->db);
      
   	  $guiObj->refresh_tree=$argsObj->do_refresh?"yes":"no";
      $item2link[$argsObj->tcase_id]=$argsObj->tcversion_id;
      
      if( isset($request['add2tplanid']) )
      {
          foreach($request['add2tplanid'] as $tplan_id => $value)
          {
              $tplan_mgr->link_tcversions($tplan_id,$item2link);  
          }
          
	        $this->tcaseMgr->show($smartyObj,$this->templateCfg->template_dir,
	                              $argsObj->tcase_id,$argsObj->tcversion_id,$viewer_args);
          
      }
      return $guiObj;
  }

  /**
   * add2testplan - is really needed???? 20090308 - franciscom - TO DO
   *
   */
	function add2testplan(&$argsObj,$request)
	{
      // $smartyObj = new TLSmarty();
      // $guiObj=new stdClass();
      // $viewer_args=array();
      // $tplan_mgr = new testplan($this->db);
      // 
   	  // $guiObj->refresh_tree=$argsObj->do_refresh?"yes":"no";
      // 
      // $item2link[$argsObj->tcase_id]=$argsObj->tcversion_id;
      // foreach($request['add2tplanid'] as $tplan_id => $value)
      // {
      //     $tplan_mgr->link_tcversions($tplan_id,$item2link);  
      // }
	    // $this->tcaseMgr->show($smartyObj,$this->templateCfg->template_dir,
	    //                       $argsObj->tcase_id,$argsObj->tcversion_id,$viewer_args);
      // 
      // return $guiObj;
  }



} // end class  
?>