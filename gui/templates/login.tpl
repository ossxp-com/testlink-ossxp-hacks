{* 
TestLink Open Source Project - http://testlink.sourceforge.net/ 
$Id: login.tpl,v 1.25.2.1 2009/11/28 23:12:14 havlat Exp $
Purpose: smarty template - login page 
-------------------------------------------------------------------------------------- *}
{lang_get var='labels' s='login_name,password,btn_login,new_user_q,lost_password_q'}
{config_load file="input_dimensions.conf" section="login"}
{include file="inc_head.tpl" title="TestLink - Login" openHead='yes'}

{include file="inc_ext_js.tpl"}

<script language="JavaScript" src="{$basehref}gui/niftycube/niftycube.js" 
		type="text/javascript"></script>
{literal}
<script type="text/javascript">
window.onload=function()
{
	Nifty("div#login_div","big");
	Nifty("div.messages","normal");

	if( typeof display_login_block != 'undefined')
	{
		display_login_block();
	}

	if( typeof display_demo_users_block != 'undefined')
	{
		display_demo_users_block();
	}

	if( typeof display_footer_block != 'undefined')
	{
		display_footer_block();
	}
 
	// set focus on login text box
	focusInputField('login');
}
</script>
{/literal}

</head>
<body>
{include file="inc_login_title.tpl"}

<div id="login_div" style="text-align:left; margin:20%; margin-top:5px; padding:5px; border:1px;">

	<script type="text/javascript">
		function display_login_block()
		{ldelim}
			var p1 = new Ext.Panel({ldelim}
			                       title: '<center>{$login_title}</center>',
			                       collapsible:false,
			                       collapsed: false,
			                       draggable: false,
			                       contentEl: 'login_content',
			                       baseCls: 'x-tl-panel',
			                       bodyStyle: "background:#c8dce8;padding:3px;",
			                       renderTo: 'menu_login_block',
			                       width:'100%'
			                       {rdelim});
		{rdelim}

		{if $demo_login_contents}
		function display_demo_users_block()
		{ldelim}
			var p2 = new Ext.Panel({ldelim}
			                       title: '<center>{$demo_login_title}</center>',
			                       collapsible:false,
			                       collapsed: false,
			                       draggable: false,
			                       contentEl: 'demo_users_content',
			                       baseCls: 'x-tl-panel',
			                       bodyStyle: "background:#c8dce8;padding:3px;",
			                       renderTo: 'menu_demo_users_block',
			                       width:'100%'
			                       {rdelim});
		{rdelim}

		function display_footer_block()
		{ldelim}
			var p4 = new Ext.Panel({ldelim}
			                       collapsible:false,
			                       collapsed: false,
			                       draggable: false,
			                       contentEl: 'footer_content',
			                       baseCls: 'x-tl-panel',
			                       bodyStyle: "background:#c8dce8;padding:3px;",
			                       renderTo: 'menu_footer_block',
			                       width:'100%'
			                       {rdelim});
		{rdelim}
	</script>

	<div class="vertical_menu" style="float: left; width: 80%; margin:10px">
		<br />
		<div id="menu_login_block"></div><br />
		<div id="menu_demo_users_block"></div><br />
		<div id="menu_footer_block"></div><br />

		<div id='login_content'>
			<div style="text-align:center;color:red;">{$gui->note}</div>
			{if !$login_form_contents}
				<form method="post" name="login_form" action="login.php">
					{if $gui->login_disabled eq 0}
						<input type="hidden" name="reqURI" value="{$gui->reqURI|escape:'url'}"/>
						<p class="label">{$labels.login_name}<br />
						<input type="text" name="tl_login" id="login" size="{#LOGIN_SIZE#}" maxlength="{#LOGIN_MAXLEN#}" />
					</p>
						<p class="label">{$labels.password}<br />
						<input type="password" name="tl_password" size="{#PASSWD_SIZE#}" maxlength="{#PASSWD_SIZE#}" />
					</p>
					<input type="submit" name="login_submit" value="{$labels.btn_login}" />
					{/if}
				</form>
			{else}
				<div>
					{$login_form_contents}
				</div>
			{/if}

			{if $gui->user_self_signup}
				<div align="center"><a href="firstLogin.php">{$labels.new_user_q}</a></div><br />
			{/if}

			{* the configured authentication method don't allow users to reset his/her password *}
			{if $gui->external_password_mgmt eq 0}
				<div align="center"><a href="lostPassword.php">{$labels.lost_password_q}</a></div>
			{/if}
		</div>

		<div id='demo_users_content'>
			{$demo_login_contents}
		</div>

		<div id='footer_content'>
			{include file="inc_copyrightnotice.tpl"}
		</div>
	</div>
	
	{if $gui->securityNotes}
    	{include file="inc_msg_from_array.tpl" array_of_msg=$gui->securityNotes arg_css_class="messages"}
	{/if}
	
	{if $tlCfg->login_info != ""}
		<div>{$tlCfg->login_info}</div>
	{/if}

</div>
</body>
</html>
