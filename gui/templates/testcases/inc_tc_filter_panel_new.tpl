{*
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * $Id: inc_tc_filter_panel_new.tpl,v 1.1 2010/05/23 17:42:45 franciscom Exp $
 * 
 * Shows the filter panel. Included by some other templates.
 * At the moment: planTCNavigator, execNavigator, planAddTCNavigator, tcTree.
 * Inspired by idea in discussion regarding BUGID 3301.
 *
 * Naming conventions for variables are based on the names 
 * used in plan/planTCNavigator.tpl.
 * That template was also the base for most of the html code used in here.
 *
 * @author Andreas Simon
 * @internal revision
 *  20100501 - franciscom - BUGID 3410: Smarty 3.0 compatibility
 *}


{lang_get var=labels s='caption_nav_settings, caption_nav_filters, platform, test_plan,
                        exec_build,build,filter_tcID,filter_on,filter_result,
                        btn_update_menu,btn_apply_filter,keyword,keywords_filter_help,
                        filter_owner,TestPlan,test_plan,caption_nav_filters,
                        platform, include_unassigned_testcases, btn_unassign_all_tcs,
                        execution_type, do_auto_update, testsuite, 
                        btn_bulk_update_to_latest_version, priority'} 


{* Assigning/initializing of all used variables is done here.
   I did not use foreach or some construct like that here because this should be more performant.
   It is more code, but doing this here in one place at the top keeps the 
   template code below simple, clean and readable. *}

{assign var="panelMisc" value=$gui->controlPanel}
{assign var="panelSettings" value=$gui->controlPanel->settings}
{assign var="panelFilters" value=$gui->controlPanel->filters}
{assign var="strOption" value=$gui->controlPanel->strOption}
{assign var="displaySetting" value=$gui->controlPanel->displaySetting}
{assign var="displayFilter" value=$gui->controlPanel->displayFilter}



{if isset($gui->assigneeFilterItemQty)}
    {assign var="assigneeFilterItemQty" value=$gui->assigneeFilterItemQty}
{else}
	{assign var="assigneeFilterItemQty" value=0}
{/if}

{if isset($gui->statusFilterItemQty)}
    {assign var="statusFilterItemQty" value=$gui->statusFilterItemQty}
{else}
	{assign var="statusFilterItemQty" value=0}
{/if}

{if isset($gui->keywordsMap)}
    {assign var="keywordsMap" value=$gui->keywordsMap}
{else}
	{assign var="keywordsMap" value=0}
{/if}

{if isset($gui->optBuild)}
    {assign var="optBuild" value=$gui->optBuild}
{else}
	{assign var="optBuild" value=0}
{/if}

{if isset($gui->keywordID)}
    {assign var="keywordID" value=$gui->keywordID}
{else}
	{assign var="keywordID" value=0}
{/if}

{if isset($gui->tPlanID)}
    {assign var="tPlanID" value=$gui->tPlanID}
{else}
	{assign var="tPlanID" value=0}
{/if}

{if isset($gui->keywordsFilterType)}
	{assign var="keywordsFilterType" value=$gui->keywordsFilterType}
{else}
	{assign var="keywordsFilterType" value=""}
{/if}

{if isset($gui->advancedFilterMode)}
	{assign var="advancedFilterMode" value=$gui->advancedFilterMode}
{else}
	{assign var="advancedFilterMode" value=0}
{/if}

{if isset($gui->toggleFilterModeLabel)}
	{assign var="toggleFilterModeLabel" value=$gui->toggleFilterModeLabel}
{else}
	{assign var="toggleFilterModeLabel" value=""}
{/if}

{if isset($gui->mapTPlans)}
	{assign var="mapTPlans" value=$gui->mapTPlans}
{else}
	{assign var="mapTPlans" value=""}
{/if}

{if isset($gui->optPlatform)}
	{assign var="optPlatform" value=$gui->optPlatform}
{else}
	{assign var="optPlatform" value=""}
{/if}


{if isset($gui->buildCount)}
	{assign var="buildCount" value=$gui->buildCount}
{else}
	{assign var="buildCount" value=0}
{/if}

{if isset($gui->chooseFilterModeEnabled)}
	{assign var="chooseFilterModeEnabled" value=$gui->chooseFilterModeEnabled}
{else}
	{assign var="chooseFilterModeEnabled" value=""}
{/if}

{if isset($gui->drawBulkUpdateButton)}
	{assign var="drawBulkUpdateButton" value=$gui->drawBulkUpdateButton}
{else}
	{assign var="drawBulkUpdateButton" value=0}
{/if}

{if isset($gui->drawTCUnassignButton)}
	{assign var="drawTCUnassignButton" value=$gui->drawTCUnassignButton}
{else}
	{assign var="drawTCUnassignButton" value=0}
{/if}

{if isset($gui->execType)}
	{assign var="execType" value=$gui->execType}
{else}
	{assign var="execType" value=0}
{/if}

{if isset($gui->execTypeMap)}
	{assign var="execTypeMap" value=$gui->execTypeMap}
{else}
	{assign var="execTypeMap" value=0}
{/if}

{if isset($gui->tcSpecRefreshOnAction)}
	{assign var="tcSpecRefreshOnAction" value=$gui->tcSpecRefreshOnAction}
{else}
	{assign var="tcSpecRefreshOnAction" value=0}
{/if}

{if isset($gui->tsuitesCombo)}
	{assign var="tsuitesCombo" value=$gui->tsuitesCombo}
{else}
	{assign var="tsuitesCombo" value=0}
{/if}

{if isset($gui->tsuiteChoice)}
	{assign var="tsuiteChoice" value=$gui->tsuiteChoice}
{else}
	{assign var="tsuiteChoice" value=0}
{/if}

{if isset($gui->optFilterBuild)}
	{assign var="optFilterBuild" value=$gui->optFilterBuild}
{else}
	{assign var="optFilterBuild" value=0}
{/if}

{if isset($gui->optFilterMethodSelected)}
	{assign var="optFilterMethodSelected" value=$gui->optFilterMethodSelected}
{else}
	{assign var="optFilterMethodSelected" value=0}
{/if}

{if isset($gui->filterMethods)}
	{assign var="filterMethods" value=$gui->filterMethods}
{else}
	{assign var="filterMethods" value=0}
{/if}

{if isset($gui->filterMethodSpecificBuild)}
	{assign var="filterMethodSpecificBuild" value=$gui->filterMethodSpecificBuild}
{else}
	{assign var="filterMethodSpecificBuild" value=0}
{/if}

{if isset($gui->optResult)}
	{assign var="optResult" value=$gui->optResult}
{else}
	{assign var="optResult" value=0}
{/if}

{if isset($gui->optResultSelected)}
	{assign var="optResultSelected" value=$gui->optResultSelected}
{else}
	{assign var="optResultSelected" value=0}
{/if}

{if isset($gui->includeUnassigned)}
	{assign var="includeUnassigned" value=$gui->includeUnassigned}
{else}
	{assign var="includeUnassigned" value=0}
{/if}

{if isset($gui->filterAssignedTo)}
	{assign var="filterAssignedTo" value=$gui->filterAssignedTo}
{else}
	{assign var="filterAssignedTo" value=0}
{/if}

{if isset($gui->assigned_to_user)}
	{assign var="assignedToUser" value=$gui->assigned_to_user}
{else}
	{assign var="assignedToUser" value=0}
{/if}

{if isset($gui->disable_filter_assigned_to)}
	{assign var="disableFilterAssignedTo" value=$gui->disable_filter_assigned_to}
{else}
	{assign var="disableFilterAssignedTo" value=0}
{/if}

{if isset($gui->urgencyImportanceSelectable)}
	{assign var="urgencyImportanceSelectable" value=$gui->urgencyImportanceSelectable}
{else}
	{assign var="urgencyImportanceSelectable" value=0}
{/if}

{if isset($gui->urgencyImportance)}
	{assign var="urgencyImportance" value=$gui->urgencyImportance}
{else}
	{assign var="urgencyImportance" value=0}
{/if}

{if isset($gui->design_time_cfields)}
	{assign var="designTimeCFields" value=$gui->design_time_cfields}
{else}
	{assign var="designTimeCFields" value=""}
{/if}

{if isset($gui->feature)}
	{assign var="feature" value=$gui->feature}
{else}
	{assign var="feature" value=0}
{/if}




<form method="get" id="tc_filter_panel_form">

{if $drawTCUnassignButton}
	<input type="button" name="unassign_all_tcs" value="{$labels.btn_unassign_all_tcs}" 
		onclick="javascript:PL({$tPlanID});" />
{/if}

{* hidden feature input (mainly for testcase edit when refreshing frame) *}
{if $feature}
<input type="hidden" id="feature" name="feature" value="{$feature}" />
{/if}

{include file="inc_help.tpl" helptopic="hlp_executeFilter" show_help_icon=false}

{if $showSettings == 'yes'}
	
	<div id="settings_panel">
		<div class="x-panel-header x-unselectable">
			{$labels.caption_nav_settings}
		</div>
	
		<div id="tplan_settings" class="x-panel-body exec_additional_info" "style="padding-top: 3px;">
			<input type='hidden' id="tpn_view_settings"  name="tpn_view_status"  value="0" />
			
			<table class="smallGrey" style="width:98%;">
			
			{if $displaySetting->testPlans}
				<tr>
					<th>{$labels.test_plan}</th>
					<td>
						<select name="panelSettingsTestPlan" id="panelSettingsTestPlan" onchange="this.form.submit()">
						{html_options options=$panelSettings.testPlans.items 
						              selected=$panelSettings.testPlans.selected}
						</select>
					</td>
				</tr>
			{/if}
	
			{if $displaySetting->platforms}
				<tr>
					<th>{$labels.platform}</th>
					<td>
						<select name="panelSettingsPlatform" id="panelSettingsPlatform" onchange="this.form.submit()">
						{html_options options=$panelSettings.platforms.items 
						              selected=$panelSettings.platforms.selected}
						</select>
					</td>
				</tr>
			{/if}
			
			{if $displaySetting->builds}
				<tr>
					<th>{$labels.exec_build}</th>
					<td>
						<select name="panelSettingsBuild" id="panelSettingsBuild" onchange="this.form.submit()">
						{html_options options=$panelSettings.builds.items 
						              selected=$panelSettings.builds.selected}
						</select>
					</td>
				</tr>
			{/if}
			
			<tr>
	   			<td>{$labels.do_auto_update}</td>
	  			<td>
	  			   <input type="hidden" id="hidden_panelSettingsRefreshTreeOnAction"   
	  			                        name="hidden_panelSettingsRefreshTreeOnAction" />
	  			
	  			   <input type="checkbox" 
	  			           id="panelSettingsRefreshTreeOnAction"   name="panelSettingsRefreshTreeOnAction"
	  			           value="1" {$panelSettings->refreshTreeOnActionChecked}
	  			           style="font-size: 90%;" onclick="this.form.submit()"/>
	  			</td>
	  		</tr>
			
			</table>
		</div> {* tplan_settings *}
	</div> {* settings_panel *}
	
{/if} {* show settings *}

{if $showFilters == 'yes'}
	
	<div id="filter_panel">
		<div class="x-panel-header x-unselectable">
			{$labels.caption_nav_filters}
		</div>
	
	<div id="filter_settings" class="x-panel-body exec_additional_info" style="padding-top: 3px;">

		<input type="hidden" id="called_by_me" name="called_by_me" value="1" />
		<input type="hidden" id="called_url" name="called_url" value="" />
		<input type='hidden' id="panelFiltersAdvancedFilterMode"  name="filtePanelAdvancedFilterMode"  
		                     value="{$advancedFilterMode}" />
	
		<table class="smallGrey" style="width:98%;">
			
	    {if $displayFilter.testPlans && $executionMode == 'no'}
			<tr>
				<td>{$labels.test_plan}</td>
				<td>
					<select name="panelFiltersTestPlan" onchange="this.form.submit()">
				    {html_options options=$panelFilters.testPlans.items 
				                  selected=$panelFilters.testPlans.selected}
					</select>
				</td>
			</tr>
		  {/if}
			
		{if $displayFilter.testSuites}
			<tr>
	    		<td>{$labels.testsuite}</td>
	    		<td>
	    			<select name="panelFiltersTestSuite" style="width:auto">
	    				{html_options options=$panelFilters.testSuites.items 
	    				              selected=$panelFilters.testSuites.selected}
	    			</select>
	    		</td>
	    	</tr>
    	{/if}
			
		{if $displayFilter.keywords}
			<tr style="{$panelFilters->keywords.displayStyle}">
				<td>{$labels.keyword}</td>
				<td><select name="panelFiltersKeyword[]" title="{$labels.keywords_filter_help}"
				            multiple="multiple" size={$panelFilters->keywords.size}>
				    {html_options options=$panelFilters.keywords.items 
				                  selected=$panelFilters.keywords.selected}
					</select>
				
	      {html_radios name='panelFiltersKeywordFilterType' 
	                   options=$panelFilters->keywordFilterTypes->options
	                   selected=$panelFilters->keywordFilterTypes->selected}
				</td>
			</tr>
		{/if}
		
			{if $displayFilter.platforms && $executionMode = 'no'}
			  <tr>
			  	<th>{$labels.platform}</th>
			  	<td><select name="panelFiltersPlatform">
			  		{html_options options=$panelFilters.platforms.items 
			  		              selected=$panelFilters.platforms.selected}
			  		</select>
			  	</td>
			  </tr>
			{/if}
			
			{if $urgencyImportanceSelectable}
				<tr>
					<th width="75">{$labels.priority}</th>
					<td>
						<select name="urgencyImportance">
						<option value="">{$strOptionAny}</option>
						{html_options options=$gsmarty_option_importance selected=$urgencyImportance}
						</select>
					</td>
				</tr>
			{/if}
			
			{* $session['testprojectOptions']->automationEnabled && $execTypeMap *}
			{if $displayFilter.execTypes}
				<tr>
					<td>{$labels.execution_type}</td>
		  			<td>
				    <select name="filterPanelExecType">
	    	  	  {html_options options=$panelFilters.execTypes.items
	    	  	                selected=$panelFilters.execTypes.selected}
		    	  </select>
					</td>	
				</tr>
			{/if}
			
			{* $panelItems.testers.items != '' *}
			{if $displayFilter.testers}
			<tr>
				<td>{$labels.filter_owner}</td>
				<td>
				
				{if $disableFilterAssignedTo && $assignedToUser}
					{$assignedToUser}
				{else}
					  {if $advancedFilterMode}
					  <select name="filterPanelAssignedTo[]" id="filterPanelAssignedTo" 
					  		multiple="multiple" size={$panelItems.testers.size}
					  		{html_options options=$panelItems.testers.items 
					  		              selected=$panelItems.testers.items.selected}
						</select>						
					  {else}
					  <select name="filterPanelAssignedTo" id="filterPanelAssignedTo" 
							      onchange="javascript: triggerAssignedBox('filterPanelAssignedTo','filterPanelIncludeUnassigned',
											                                       '{$strOption.any}', '{$strOption.none}',
																						                 '{$strOption.somebody}');">
					  		{html_options options=$panelItems.testers.items 
					  		              selected=$panelItems.testers.items.selected}
						</select>
						<br/>		
						<input type="checkbox" name="filterPanelIncludeUnassigned" id="filterPanelIncludeUnassigned" 
			  		           value="1" {if $includeUnassigned} checked="checked" {/if} />
						{$labels.include_unassigned_testcases}
						{/if}
				{/if}
				
	 			</td>
			</tr>
	    	{/if}
	
	
	{* custom fields are placed here *}
	
	{if $designTimeCFields}
		<tr><td>&nbsp;</td></tr> {* empty row for a little separation *}
		{$designTimeCFields}
	{/if}
	
	
	
	{* result filtering parts *}
	{if $buildCount neq 0}
		
		<tr><td>&nbsp;</td></tr> {* empty row for a little separation *}
	
		{if $optResult}
	   		<tr>
				<th>{$labels.filter_result}</th>
				<td>
				  {if $advancedFilterMode}
				  	<select name="filter_status[]" multiple="multiple" size={$statusFilterItemQty}>
				  {else}
				  	<select name="filter_status">
				  {/if}
				  	{html_options options=$optResult selected=$optResultSelected}
				  	</select>
				</td>
			</tr>
		{/if}
		
			<tr>
				<th>{$labels.filter_on}</th>
				<td>
				  	<select name="filter_method" id="filter_method"
				  		      onchange="javascript: triggerBuildChooser('deactivatable',
				  		                                                'filter_method',
						                                                {$filterMethodSpecificBuild});">
					  	{html_options options=$filterMethods selected=$optFilterMethodSelected}
				  	</select>
				</td>
			</tr>
			
			<tr id="deactivatable">
				<th>{$labels.build}</th>
				<td><select id="filter_build_id" name="filter_build_id">
					{html_options options=$optFilterBuild.items selected=$optFilterBuild.selected}
					</select>
				</td>
			</tr>
			
	{/if}

	
		</table>
			
			<div>
				<input type="submit" value="{$labels.btn_apply_filter}" 
				       id="doUpdateTree" name="doUpdateTree" style="font-size: 90%;" />
	
				{if $chooseFilterModeEnabled}
				<input type="submit" id="toggleFilterMode"  name="toggleFilterMode" 
				     value="{$toggleFilterModeLabel}"  
				     onclick="toggleInput('advancedFilterMode');"
				     style="font-size: 90%;"  />
	      		{/if}
			</div>
	
		{if $drawBulkUpdateButton}
	    	<input type="button" value="{$labels.btn_bulk_update_to_latest_version}" 
	    	       name="doBulkUpdateToLatest" 
	    	       onclick="update2latest({$tPlanID})" />
		{/if}
	
	</form>
	
	</div> {* filter_settings *}
	
	</div> {* filter_panel *}
	
{/if} {* show filters *}
