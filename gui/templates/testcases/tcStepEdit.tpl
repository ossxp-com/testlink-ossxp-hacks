{*
TestLink Open Source Project - http://testlink.sourceforge.net/ 
$Id: tcStepEdit.tpl,v 1.27 2010/07/01 14:21:54 mx-julian Exp $ 
Purpose: create/edit test case step

rev:
	20100621 - eloff - BUGID 3241 - Implement vertical layout
  20100529 - franciscom - BUGID 3493 - using escape:'url'
	20100403 - franciscom - added create step button while editing existent step
	                        BUGID 3359 - copy test case step
	20100327 - franciscom - improvements on goback logic
	20100125 - franciscom - fixed bug on checks on existence of step number
	20100124 - eloff - BUGID 3088 - Check valid session before submit
	20100123 - franciscom - checks on existence of step number
	20100111 - eloff - BUGID 2036 - Check modified content before exit

*}

{assign var="cfg_section" value=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}


{assign var="module" value='lib/testcases/'}
{assign var="tcase_id" value=$gui->tcase_id}
{assign var="tcversion_id" value=$gui->tcversion_id}

{* Used on several operations to implement goback *}
{* BUGID 3493 - added show_mode*}
{assign var="showMode" value=$gui->show_mode} 

{assign var="tcViewAction" value="lib/testcases/archiveData.php?tcase_id=$tcase_id&show_mode=$showMode"}
{assign var="goBackAction" value="$basehref$tcViewAction"}
{assign var="goBackActionURLencoded" value="$goBackAction|escape:'url'"}
{assign var="url_args" value="tcEdit.php?doAction=editStep&testcase_id=$tcase_id&tcversion_id=$tcversion_id"}
{assign var="url_args" value="$url_args&goback_url=$goBackActionURLencoded&step_id="}
{assign var="hrefEditStep"  value="$basehref$module$url_args"}

{lang_get var="labels"
          s="warning_step_number_already_exists,warning,warning_step_number,
             expected_results,step_actions,step_number_verbose,btn_cancel,btn_create_step,
             btn_copy_step,btn_save,cancel,warning_unsaved,step_number,execution_type_short_descr"}

{include file="inc_head.tpl" openHead='yes' jsValidate="yes" editorType=$gui->editorType}
{include file="inc_del_onclick.tpl"}

<script type="text/javascript" src="gui/javascript/ext_extensions.js" language="javascript"></script>
<script type="text/javascript">
var warning_step_number = "{$labels.warning_step_number}";
var alert_box_title = "{$labels.warning}";
var warning_step_number_already_exists = "{$labels.warning_step_number_already_exists}";

{literal}
function validateForm(the_form,step_set,step_number_on_edit)
{
	var value = '';
	var status_ok = true;
	var feedback = '';
	var value_found_on_set=false;
	var value_step_mistmatch=false;
	value = parseInt(the_form.step_number.value);

	if( isNaN(value) || value <= 0)
	{
		alert_message(alert_box_title,warning_step_number);
		selectField(the_form,'step_number');
		return false;
	}

  // check is step number is free/available
  // alert('#1# - step_set:' + step_set + ' - step_set.length:' + step_set.length);
  // alert('#2# - step_numver.value:' + value + ' - step_number_on_edit:' + step_number_on_edit);
  if( step_set.length > 0 )
  {
    value_found_on_set = (step_set.indexOf(value) >= 0);
    value_step_mistmatch = (value != step_number_on_edit);
    // alert('#3# - value_found_on_set:' + value_found_on_set + ' - value_step_mistmatch:' + value_step_mistmatch);

    if(value_found_on_set && value_step_mistmatch)
    {
      feedback = warning_step_number_already_exists.replace('%s',value);
 	    alert_message(alert_box_title,feedback);
		  selectField(the_form,'step_number');
		  return false;
		}
  }
	show_modified_warning=false;
	return Ext.ux.requireSessionAndSubmit(the_form);
}
{/literal}
</script>
{if $tlCfg->gui->checkNotSaved}
<script type="text/javascript">
var unload_msg = "{$labels.warning_unsaved}";
var tc_editor = "{$tlCfg->gui->text_editor.all.type}";
</script>
<script src="gui/javascript/checkmodified.js" type="text/javascript"></script>
{/if}
</head>

<body onLoad="focusInputField('step')">
<h1 class="title">{$gui->main_descr}</h1> 

<div class="workBack" style="width:98.6%;">

{if $gui->user_feedback != ''}
	<div>
		<p class="info">{$gui->user_feedback}</p>
	</div>
{/if}

{if $gui->has_been_executed}
    {lang_get s='warning_editing_executed_step' var="warning_edit_msg"}
    <div class="messages" align="center">{$warning_edit_msg}</div>
{/if}

{*
DEBUG: $gui->operation: {$gui->operation} <br>
DEBUG: $gui->action: {$gui->action} <br>
*}

<form method="post" action="lib/testcases/tcEdit.php" name="tcStepEdit"
      onSubmit="return validateForm(this,'{$gui->step_set}',{$gui->step_number});">

	<input type="hidden" name="testcase_id" value="{$gui->tcase_id}" />
	<input type="hidden" name="tcversion_id" value="{$gui->tcversion_id}" />
	<input type="hidden" name="doAction" value="" />
 	<input type="hidden" name="show_mode" value="{$gui->show_mode}" />
	<input type="hidden" name="step_id" value="{$gui->step_id}" />
	<input type="hidden" name="step_number" value="{$gui->step_number}" />
	<input type="hidden" name="goback_url" value="{$goBackAction}" />
	<div class="groupBtn">
		<input id="do_update_step" type="submit" name="do_update_step" 
		       onclick="doAction.value='{$gui->operation}'" value="{$labels.btn_save}" />

    {if $gui->operation == 'doUpdateStep'}
		  <input id="do_create_step" type="submit" name="do_create_step" 
		         onclick="doAction.value='createStep'" value="{$labels.btn_create_step}" />

		  <input id="do_copy_step" type="submit" name="do_copy_step" 
		         onclick="doAction.value='doCopyStep'" value="{$labels.btn_copy_step}" />
    {/if}

  	<input type="button" name="cancel" value="{$labels.btn_cancel}"
    	     {if $gui->goback_url != ''}  onclick="location='{$gui->goback_url}'"
    	     {else}  onclick="javascript:history.back();" {/if} />
	</div>	

  <table class="simple" style="width:99%;">
	{if $gui->steps_results_layout == "horizontal"}
  	<tr>
  		<th width="{$gui->tableColspan}">{$labels.step_number}</th>
  		{* Julian: added width to show columns step details and expected
  		 * results at approximately same size (step details get 45%
  		 * expected results get the rest)
  		 *}
		<th width="45%">{$labels.actions}</th>
  		<th>{$labels.expected_results}</th>
      {if $session['testprojectOptions']->automationEnabled}
  		  <th width="25">{$labels.execution_type_short_descr}</th>
  		{/if}  
  	</tr>
  
  {if $gui->tcaseSteps != ''}
   	{foreach from=$gui->tcaseSteps item=step_info}
  	  <tr>
      {if $step_info.step_number == $gui->step_number}
		    <td style="text-align:left;">{$gui->step_number}</td>
  		  <td>{$steps}</td>
  		  <td>{$expected_results}</td>
		    {if $session['testprojectOptions']->automationEnabled}
		    <td>
		    	<select name="exec_type" onchange="content_modified = true">
        	  	{html_options options=$gui->execution_types selected=$gui->step_exec_type}
	        </select>
      	</td>
      	{/if}
      {else}
        <td style="text-align:left;"><a href="{$hrefEditStep}{$step_info.id}">{$step_info.step_number}</a></td>
  	  	<td ><a href="{$hrefEditStep}{$step_info.id}">{$step_info.actions}</a></td>
  	  	<td ><a href="{$hrefEditStep}{$step_info.id}">{$step_info.expected_results}</a></td>
        {if $session['testprojectOptions']->automationEnabled}
  	  	  <td><a href="{$hrefEditStep}{$step_info.id}">{$gui->execution_types[$step_info.execution_type]}</a></td>
  	  	{/if}  
      {/if}
  	  </tr>
    {/foreach}
  {/if}
  {else} {* Vertical layout *}
	{foreach from=$gui->tcaseSteps item=step_info}
	<tr>
		<th width="20">{$args_labels.step_number} {$step_info.step_number}</th>
		<th>{$labels.step_actions}</th>
		{if $session['testprojectOptions']->automationEnabled}
		{if $step_info.step_number == $gui->step_number}
		<th width="200">{$labels.execution_type_short_descr}:
			<select name="exec_type" onchange="content_modified = true">
				{html_options options=$gui->execution_types selected=$gui->step_exec_type}
	        </select>
		</th>
		{else}
			<th>{$labels.execution_type_short_descr}:
				{$gui->execution_types[$step_info.execution_type]}</th>
		{/if}
		{else}
		<th>&nbsp;</th>
		{/if} {* automation *}
		{if $edit_enabled}
		<th>&nbsp;</th>
		{/if}
	</tr>
	<tr>
		<td>&nbsp;</td>
		{if $step_info.step_number == $gui->step_number}
		<td colspan="2">{$steps}</td>
		{else}
		<td colspan="2"><a href="{$hrefEditStep}{$step_info.id}">{$step_info.actions}</a></td>
		{/if}
	</tr>
	<tr>
		<th style="background: transparent; border: none"></th>
		<th colspan="2">{$labels.expected_results}</th>
	</tr>
	<tr>
		<td>&nbsp;</td>
		{if $step_info.step_number == $gui->step_number}
		<td colspan="2">{$expected_results}</td>
		{else}
		<td colspan="2" style="padding: 0.5em 0.5em 2em 0.5em"><a href="{$hrefEditStep}{$step_info.id}">{$step_info.expected_results}</a></td>
		{/if}
	</tr>
	{/foreach}
  {/if}
  {if $gui->action == 'createStep' || $gui->action == 'doCreateStep'}
  	<tr>
		  <td style="text-align:left;">{$gui->step_number}</td>
  		<td>{$steps}</td>
  		<td>{$expected_results}</td>
		    {if $session['testprojectOptions']->automationEnabled}
		    <td>
		    	<select name="exec_type" onchange="content_modified = true">
        	  	{html_options options=$gui->execution_types selected=$gui->step_exec_type}
	        </select>
      	</td>
      	{/if}
  	</tr>
  {/if}
  </table>	
  <p>
	<div class="groupBtn">
		<input id="do_update_step" type="submit" name="do_update_step" 
		       onclick="doAction.value='{$gui->operation}'" value="{$labels.btn_save}" />

    {if $gui->operation == 'doUpdateStep'}
		  <input id="do_create_step" type="submit" name="do_create_step" 
		         onclick="doAction.value='createStep'" value="{$labels.btn_create_step}" />

		  <input id="do_copy_step" type="submit" name="do_copy_step" 
		         onclick="doAction.value='doCopyStep'" value="{$labels.btn_copy_step}" />
    {/if}

  	<input type="button" name="cancel" value="{$labels.btn_cancel}"
    	     {if $gui->goback_url != ''}  onclick="location='{$gui->goback_url}'"
    	     {else}  onclick="javascript:history.back();" {/if} />
	</div>	
</form>

<script type="text/javascript" defer="1">
   	document.forms[0].step_number.focus();
</script>

</div>
</body>
</html>
