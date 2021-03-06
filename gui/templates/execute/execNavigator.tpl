{* TestLink Open Source Project - http://testlink.sourceforge.net/ *}
{* $Id: execNavigator.tpl,v 1.48 2010/06/25 11:57:00 asimon83 Exp $ *}
{* Purpose: smarty template - show test set tree *}
{*
rev :
  20100625 - asimon - removed old ext js constant
  20100610 - asimon - BUGID 3301 - new included template inc_filter_panel.tpl
  20100428 - asimon - BUGID 3301 - removed old filter/settings form/panel and replaced
                      them with new included template inc_tc_filter_panel.tpl
  20100417 - franciscom - BUGID 3380 - filter by execution type 
  20100412 - asimon - BUGID 3379 - changed display method for some filters
  20100409 - eloff - BUGID 3050 - changed filter panels background to grey
  20100222 - asimon - moved platform select box from filters to settings panel
  20100202 - asimon - BUGID 2455, BUGID 3026 - changes on filters
  20100109 - eloff - BUGID 2800 - filter panel is now ext CollapsiblePanel
  20090808 - franciscom - added contribution platform
  20081227 - franciscom - BUGID 1913 - filter by same results on ALL previous builds
  20081220 - franciscom - advanced/simple filters
  20080621 - franciscom - adding ext js treemenu
  20080427 - franciscom - refactoring
  20080224 - franciscom - BUGID 1056
  20070225 - franciscom - fixed auto-bug BUGID 642
  20070212 - franciscom - name changes on html inputs
                          use input_dimensions.conf
*}

{lang_get var="labels"
          s="filter_result,caption_nav_filter_settings,filter_owner,test_plan,filter_on,
             platform,exec_build,btn_apply_filter,build,keyword,filter_tcID,execution_type,
             include_unassigned_testcases,priority,caption_nav_filters,caption_nav_settings"}       

{* ===================================================================== *}

{include file="inc_head.tpl" openHead="yes"}
{include file="inc_ext_js.tpl" bResetEXTCss=1}

{* includes Ext.ux.CollapsiblePanel *}
<script type="text/javascript" src='gui/javascript/ext_extensions.js'></script>
{literal}
<script type="text/javascript">
	treeCfg = {tree_div_id:'tree',root_name:"",root_id:0,root_href:"",
	           loader:"", enableDD:false, dragDropBackEndUrl:'',children:""};
	Ext.onReady(function() {
		Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

		// Use a collapsible panel for filter settings
		// and place a help icon in ther header
		var settingsPanel = new Ext.ux.CollapsiblePanel({
			id: 'tl_exec_filter',
			applyTo: 'settings_panel',
			tools: [{
				id: 'help',
				handler: function(event, toolEl, panel) {
					show_help(help_localized_text);
				}
			}]
		});
		var filtersPanel = new Ext.ux.CollapsiblePanel({
			id: 'tl_exec_settings',
			applyTo: 'filter_panel'
		});
	});
</script>
{/literal}


<script type="text/javascript">
	treeCfg.root_name='{$gui->ajaxTree->root_node->name|escape:'javascript'}';
	treeCfg.root_id={$gui->ajaxTree->root_node->id};
	treeCfg.root_href='{$gui->ajaxTree->root_node->href}';
	treeCfg.children={$gui->ajaxTree->children};
</script>

<script type="text/javascript" src='gui/javascript/execTreeWithMenu.js'></script>

<script language="JavaScript" src="gui/javascript/expandAndCollapseFunctions.js" type="text/javascript"></script>


{* BUGID 3301 - js include file for simpler code, filter refactoring/redesign *}
{include file='inc_filter_panel_js.tpl'}

{* 
 * !!!!! IMPORTANT !!!!!
 * Above included file closes <head> tag and opens <body>, so this is not done here.
 *}

	
{assign var="cfg_section" value=$smarty.template|basename|replace:".tpl":""}
{assign var="build_number" value=$control->settings.setting_build.selected}
{config_load file="input_dimensions.conf" section=$cfg_section}

<h1 class="title">{$labels.test_plan}{$tlCfg->gui_title_separator_1} {$control->args->testplan_name|escape}
{$tlCfg->gui_separator_open}{$labels.build}{$tlCfg->gui_title_separator_1}
{$control->settings.setting_build.items.$build_number|escape}{$tlCfg->gui_separator_close}</h1>


{* BUGID 3301: include file for filter panel *}
{include file='inc_filter_panel.tpl'}


{* ===================================================================== *}
<div id="tree" style="overflow:auto; height:380px;border:1px solid #c3daf9;"></div>

{*if $gui->src_workframe != ''}
<script type="text/javascript">
	parent.workframe.location='{$gui->src_workframe}';
</script>
{/if*}

</body>
</html>
