{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
$Id: tcView.tpl,v 1.20 2009/03/08 11:46:24 franciscom Exp $
Purpose: smarty template - view test case in test specification

rev:20090308 - franciscom - added args_can_do
    20090215 - franciscom - BUGID - show info about links to test plans
*}

{include file="inc_head.tpl" openHead='yes'}
<script language="JavaScript" src="gui/javascript/expandAndCollapseFunctions.js" type="text/javascript"></script>

{if $smarty.const.USE_EXT_JS_LIBRARY}
  {include file="inc_ext_js.tpl" css_only=1}
{/if}
</head>
{if !isset($loadOnCancelURL)}
 	{assign var="loadOnCancelURL" value=""}
{/if} 
{assign var="my_style" value=""}
{if $gui->hilite_testcase_name}
    {assign var="my_style" value="background:#059; color:white; margin:0px 0px 4px 0px;padding:3px;"}
{/if}

{assign var=this_template_dir value=$smarty.template|dirname}
{lang_get var='labels' 
          s='no_records_found,other_versions,version,title_test_case,match_count'}

<body onLoad="viewElement(document.getElementById('other_versions'),false)">
<h1 class="title">{$gui->pageTitle}{if $gui->show_match_count} - {$labels.match_count}:{$gui->match_count}{/if}
</h1>
{include file="inc_update.tpl" user_feedback=$user_feedback refresh=$refresh_tree}

<div class="workBack">


{if $gui->tc_current_version}
{section name=idx loop=$gui->tc_current_version}

		{assign var="tcID" value=$gui->tc_current_version[idx][0].testcase_id}

    {* Current active version *}
    {if $testcase_other_versions[idx] neq null}
        {assign var="my_delete_version" value="yes"}
    {else}
        {assign var="my_delete_version" value="no"}
    {/if}
  
    <h2 style="{$my_style}">
	  {if $gui->display_testcase_path}
	      {foreach from=$gui->path_info[$tcID] item=path_part}
	          {$path_part|escape} /
	      {/foreach}
	      {* <br /> *}
	  {/if}
    {if $gui->show_title == 'no' }
	      {$gui->tc_current_version[idx][0].tc_external_id|escape}:{$gui->tc_current_version[idx][0].name|escape}</h2>
    {/if}

		{include file="$this_template_dir/tcView_viewer.tpl" 
		         args_testcase=$gui->tc_current_version[idx][0]
		         args_keywords_map=$keywords_map[idx] 
		         args_reqs=$arrReqs[idx] 
		         args_status_quo=$status_quo[idx]

		         args_can_edit=$can_edit 
		         args_can_move_copy="yes" 
		         args_can_delete_testcase=$can_delete_testcase
		         args_can_delete_version=$my_delete_version
		         args_can_do=$gui->can_do
		         
		         args_show_version="yes" 
		         args_show_title=$gui->show_title
		         
		         args_activate_deactivate_name='activate'
		         args_activate_deactivate='bnt_activate'
		         args_cf=$cf[idx] 
		         args_tcase_cfg=$tcase_cfg
		         args_users=$users

		         args_tproject_name=$gui->tprojectName
		         args_tsuite_name=$gui->parentTestSuiteName
		         
		         args_linked_versions=$gui->linked_versions[idx]
		         }
		
		
		{assign var="bDownloadOnly" value=false}
		{if $can_edit != 'yes'}
			{assign var="bDownloadOnly" value=true}
		{/if}
		{include file="inc_attachments.tpl" 
		         attach_id=$tcID  
		         attach_tableName="nodes_hierarchy"
		         attach_attachmentInfos=$attachments[$tcID]  
		         attach_downloadOnly=$bDownloadOnly
		         attach_loadOnCancelURL=$loadOnCancelURL
		         }
		         
	{* Other Versions *}
    {if $testcase_other_versions[idx] neq null}
        {assign var="vid" value=$gui->tc_current_version[idx][0].id}
        {assign var="div_id" value=vers_$vid}
        {assign var="memstatus_id" value=mem_$div_id}
  
        {include file="inc_show_hide_mgmt.tpl" 
                 show_hide_container_title=$labels.other_versions
                 show_hide_container_id=$div_id
                 show_hide_container_draw=false
                 show_hide_container_class='exec_additional_info'
                 show_hide_container_view_status_id=$memstatus_id}
               
        <div id="vers_{$vid}" class="workBack">
        
  	    {foreach item=my_testcase from=$testcase_other_versions[idx]}

            {assign var="version_num" value=$my_testcase.version}
            {assign var="title" value="$labels.version}
            {assign var="title" value="$title $version_num"}
            
            {assign var="div_id" value=v_$vid}
            {assign var="sep" value="_"}
            {assign var="div_id" value=$div_id$sep$version_num}
            {assign var="memstatus_id" value=mem_$div_id}
           
            {include file="inc_show_hide_mgmt.tpl" 
                     show_hide_container_title=$title
                     show_hide_container_id=$div_id
                     show_hide_container_draw=false
                     show_hide_container_class='exec_additional_info'
                     show_hide_container_view_status_id=$memstatus_id}
 
  	          <div id="{$div_id}" class="workBack">
				      {include file="$this_template_dir/tcView_viewer.tpl" 
                       args_testcase=$my_testcase 
                       args_keywords_map=$keywords_map[idx] 
                       args_reqs=$arrReqs[idx]
                       args_status_quo=$status_quo[idx]
                       			
                       args_can_edit=$can_edit 
                       args_can_move_copy="no" 
                       args_can_delete_testcase='no'
                       args_can_delete_version=$can_delete_version
           		         args_can_do=$gui->can_do

                       args_show_version="no" 
                       args_show_title="no"
                       args_users=$users
                       args_cf=$cf[idx]
                       
           		         args_linked_versions=null
                       }
  	         </div>
  	         <br />
  	         
		    {/foreach}
		    </div>
  
      	{* ---------------------------------------------------------------- *}
      	{* Force the div of every old version to show closed as first state *}
      	{literal}
      	<script type="text/javascript">
      	{/literal}
 	  	      viewElement(document.getElementById('vers_{$vid}'),false);
    	  		{foreach item=my_testcase from=$testcase_other_versions[idx]}
  	  	      viewElement(document.getElementById('v_{$vid}_{$my_testcase.version}'),false);
			      {/foreach}
      	{literal}
      	</script>
      	{/literal}
      	{* ---------------------------------------------------------------- *}
    {/if}
    <br>
{/section}
{else}
	{$labels.no_records_found}
{/if}

</div>
</body>
</html>
