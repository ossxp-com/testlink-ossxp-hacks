{* 
Testlink Open Source Project - http://testlink.sourceforge.net/ 
$Id: inc_show_bug_table.tpl,v 1.6.4.1 2009/05/21 16:43:13 franciscom Exp $

rev: 20090518 - franciscom - BUGID 2505 - aligned code to manage default arguments
     to HEAD where this bug does not exists
*}
{lang_get var="labels" s="build,caption_bugtable,delete_bug,bug_id,del_bug_warning_msg"}
{* -------------------------------------------------------------------------------------- *}
{* Manage missing arguments                                                               *}
{if !isset($tableClassName) }
    {assign var="tableClassName"  value="simple"}
{/if}
{if !isset($tableStyles) }
    {assign var="tableStyles"  value="font-size:12px"}
{/if}
    
{* -------------------------------------------------------------------------------------- *}
<table class="simple" width="100%">
  <tr>
	  <th style="text-align:left">{$labels.build}</th>
	  <th style="text-align:left">{$labels.caption_bugtable}</th>
		{if $can_delete}
	    	<th style="text-align:left">&nbsp;</th>
		{/if}  
  </tr>
  
 	{foreach from=$bugs_map key=bug_id item=bug_elem}
	<tr>
		<td>{$bug_elem.build_name|escape}</td>
		<td>{$bug_elem.link_to_bts}</td>
		{if $can_delete}
		  <td class="clickable_icon"><a href="javascript:deleteBug_onClick({$exec_id},'{$bug_id}',
		               '{$labels.del_bug_warning_msg} ({$labels.bug_id} {$bug_id})');">
		               <img style="border:none" title="{$labels.delete_bug}" alt="{$labels.delete_bug}" 
		                    src="{$smarty.const.TL_THEME_IMG_DIR}/trash.png"/></a></td>
		{/if}
	</tr>
	{/foreach}
</table>		