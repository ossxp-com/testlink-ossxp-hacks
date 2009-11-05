<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * Filename $RCSfile: tlsmarty.inc.php,v $
 *
 * @version $Revision: 1.6 $
 * @modified $Date: 2009/03/05 07:32:37 $ $Author: franciscom $
 *
 * @author Martin Havlat
 *
 * SCOPE:
 * TLSmarty class implementation used in all templates
 *
 * Revisions:
 * 20090304 - franciscom - removed some MAGIC NUMBERS 
 * 20081027 - havlatm - moved to include Smarty library
 * 20080424 - havlatm - added $tlCfg
 * ----------------------------------------------------------------------------------- */

require_once( TL_ABS_PATH . 'third_party'. DIRECTORY_SEPARATOR . 'smarty'.  
	            DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'Smarty.class.php');


class TLSmarty extends Smarty
{
    function TLSmarty()
    {
        global $tlCfg;
        global $g_attachments;
        global $g_spec_cfg;
        global $g_bugInterfaceOn;
        global $g_interface_bugs;
        global $g_locales;
        global $g_locales_html_select_date_field_order;
        global $g_locales_date_format;
        global $g_locales_timestamp_format;
        
        
        $this->Smarty();
        $this->template_dir = TL_ABS_PATH . 'gui/templates/';
        $this->compile_dir = TL_TEMP_PATH;
        $this->config_dir = TL_ABS_PATH . 'gui/templates/';
        
        $testproject_coloring=$tlCfg->gui->testproject_coloring;
        $testprojectColor = $tlCfg->gui->background_color ; //TL_BACKGROUND_DEFAULT;
        if (isset($_SESSION['testprojectColor']))
        {
            $testprojectColor =  $_SESSION['testprojectColor'];
            if (!strlen($testprojectColor))
            {
                $testprojectColor = $tlCfg->gui->background_color;
            }    
        }
        $this->assign('testprojectColor', $testprojectColor);
        
        $my_locale = isset($_SESSION['locale']) ? $_SESSION['locale'] : TL_DEFAULT_LOCALE;
        $basehref = isset($_SESSION['basehref']) ? $_SESSION['basehref'] : TL_BASE_HREF;
        
        if ($tlCfg->smarty_debug)
        {
            $this->debugging = true;
            tLog("Smarty debug window = ON");
        }
        
        // -------------------------------------------------------------------------------------
        // Must be initialized to avoid log on TestLink Event Viewer due to undefined variable.
        // This means that optional/missing parameters on include can not be used.
        //
        // Good refactoring must be done in future, to create group of this variable
        // with clear names that must be a hint for developers, to understand where this
        // variables are used.
        
        // inc_head.tpl
        $this->assign('SP_html_help_file',null);
        $this->assign('menuUrl',null);
        $this->assign('args',null);
        $this->assign('pageTitle',null);
        
        $this->assign('css_only',null);
        $this->assign('body_onload',null);
        
        // inc_attachments.tpl
        $this->assign('attach_tableStyles',"font-size:12px");
        $this->assign('attach_tableClassName',"simple");
        $this->assign('attach_inheritStyle',0);
        $this->assign('attach_show_upload_btn',1);
        $this->assign('attach_show_title',1);
        $this->assign('attach_downloadOnly',false);
        
        // inc_help.tpl
        $this->assign('inc_help_alt',null);
        $this->assign('inc_help_title',null);
        $this->assign('inc_help_style',null);
        
        $this->assign('tplan_name',null);
        $this->assign('name',null);
        // -----------------------------------------------------------------------------
        
        $this->assign('basehref', $basehref);
        $this->assign('css', $basehref . TL_TESTLINK_CSS);
        $this->assign('locale', $my_locale);
          
          
        // -----------------------------------------------------------------------------
        // load configuration
        $this->assign('session',isset($_SESSION) ? $_SESSION : null);
        
        // load configuration
        $this->assign('tlCfg',$tlCfg);
        $this->assign('gsmarty_gui',$tlCfg->gui);
        $this->assign('gsmarty_spec_cfg',$g_spec_cfg);
        $this->assign('gsmarty_attachments',$g_attachments);
        
        $this->assign('pageCharset',$tlCfg->charset);
        $this->assign('tlVersion',TL_VERSION);
        
        // $this->assign('gsmarty_tc_status',$tlCfg->results['status_code']);
        // $this->assign('gsmarty_tc_status_css',$tlCfg->results['code_status']);
        // $this->assign('gsmarty_tc_status_for_ui',$tlCfg->results['status_label_for_exec_ui']);
        // $this->assign('gsmarty_tc_status_verbose_labels',$tlCfg->results['status_label']);
        
        $this->assign('g_bugInterfaceOn', $g_bugInterfaceOn);
        $this->assign('gsmarty_interface_bugs',$g_interface_bugs);
        $this->assign('testproject_coloring',null);
        
        	
        // -----------------------------------------------------------------------------
        // define a select structure for {html_options ...}
        $this->assign('gsmarty_option_yes_no', array(0 => lang_get('No'), 1 => lang_get('Yes')));
        $this->assign('gsmarty_option_priority', array(HIGH => lang_get('high_priority'), 
                                                       MEDIUM => lang_get('medium_priority'), 
                                                       LOW => lang_get('low_priority')));

        $this->assign('gsmarty_option_importance', array(HIGH => lang_get('high_importance'), 
                                                         MEDIUM => lang_get('medium_importance'), 
                                                         LOW => lang_get('low_importance')));
           
        
        // this allows unclosed <head> tag to add more information and link; see inc_head.tpl
        $this->assign('openHead', 'no');
        
        // there are some variables which should not be assigned for template
        // but must be initialized
        // inc_head.tpl
        $this->assign('jsValidate', null);
        $this->assign('jsTree', null);
        $this->assign('editorType', null);
        	
        	
        // user feedback variables (used in inc_update.tpl)
        $this->assign('user_feedback', null);
        $this->assign('feedback_type', ''); // Possibile values: soft
        $this->assign('action', 'updated'); //todo: simplify (remove) - use user_feedback
        $this->assign('sqlResult', null); //todo: simplify (remove) - use user_feedback
        
        $this->assign('refresh', 'no');
        $this->assign('result', null);
        
        $this->assign('optLocale',$g_locales);
        
        $this->assign('gsmarty_href_keywordsView',
        ' "lib/keywords/keywordsView.php" ' .
        ' target="mainframe" class="bold" ' .
        ' title="' . lang_get('menu_manage_keywords') . '"');
        
        $this->assign('gsmarty_html_select_date_field_order',
                      $g_locales_html_select_date_field_order[$my_locale]);
        $this->assign('gsmarty_date_format',$g_locales_date_format[$my_locale]);
        $this->assign('gsmarty_timestamp_format',$g_locales_timestamp_format[$my_locale]);
        
        // -----------------------------------------------------------------------------
        // Images
        $sort_img = TL_THEME_IMG_DIR . "/sort_hint.png";
        $api_info_img = TL_THEME_IMG_DIR . "/brick.png";
        
        $this->assign("sort_img",$sort_img);
        $this->assign("checked_img",TL_THEME_IMG_DIR . "/apply_f2_16.png");
        $this->assign("delete_img",TL_THEME_IMG_DIR . "/trash.png");
        
        $msg = lang_get('show_hide_api_info');
        $toggle_api_info_img="<img title=\"{$msg}\" alt=\"{$msg}\" " .
        " onclick=\"showHideByClass('span','api_info');event.stopPropagation();\" " .
        " src=\"{$api_info_img}\" align=\"left\" />";
        $this->assign("toggle_api_info_img",$toggle_api_info_img);
        
        
        // Some useful values for Sort Table Engine
        switch (TL_SORT_TABLE_ENGINE)
        {
            case 'kryogenix.org':
                $sort_table_by_column=lang_get('sort_table_by_column');
                $sortHintIcon="<img title=\"{$sort_table_by_column}\" " .
                " alt=\"{$sort_table_by_column}\" " .
                " src=\"{$sort_img}\" align=\"left\" />";
                
                $this->assign("sortHintIcon",$sortHintIcon);
                $this->assign("noSortableColumnClass","sorttable_nosort");
            break;
            
            default:
                $this->assign("sortHintIcon",'');
                $this->assign("noSortableColumnClass",'');
            break;
        }
        
        // Register functions
        $this->register_function("lang_get", "lang_get_smarty");
        $this->register_function("localize_date", "localize_date_smarty");
        $this->register_function("localize_timestamp", "localize_timestamp_smarty");
        $this->register_function("localize_tc_status","translate_tc_status_smarty");
        
        $this->register_modifier("basename","basename");
        $this->register_modifier("dirname","dirname");
    
    } // end of function TLSmarty()

} // end of class TLSmarty
?>