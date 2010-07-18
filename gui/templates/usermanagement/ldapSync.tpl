{*
Testlink Open Source Project - http://testlink.sourceforge.net/
$Id: usersView.tpl,v 1.23 2010/05/02 15:26:59 franciscom Exp $

Purpose: smarty template - users overview

20100426 - asimon - removed forgotten comment end sign (template syntax error)
20100419 - franciscom - BUGID 3355: A user can not be deleted from the list
20100326 - franciscom - BUGID 3324
*}
{include file="inc_head.tpl" openHead="yes"}
{include file="inc_del_onclick.tpl"}

{assign var="userActionMgr" value="lib/usermanagement/usersEdit.php"}
{assign var="createUserAction" value="$userActionMgr?doAction=create"}
{assign var="editUserAction" value="$userActionMgr?doAction=edit&amp;user_id="}
{assign var="ldapSyncAction" value="lib/usermanagement/ldapSync.php"}

{lang_get s='warning_disable_user' var="warning_msg"}
{lang_get s='disable' var="del_msgbox_title"}

<script type="text/javascript">
	var del_action=fRoot+"lib/usermanagement/usersView.php?operation=disable&user=";
</script>

{literal}
<script type="text/javascript">
function toggleRowByClass(oid,className,displayValue)
{
  var trTags = document.getElementsByTagName("tr");
  var cbox = document.getElementById(oid);
  
  for( idx=0; idx < trTags.length; idx++ ) 
  {
    if( trTags[idx].className == className ) 
    {
      if( displayValue == undefined )
      {
        if( cbox.checked )
        {
          trTags[idx].style.display = 'none';
        }
        else
        {
          trTags[idx].style.display = 'table-row';
        }
      } 
      else
      {
        trTags[idx].style.display = displayValue;
      }
    }
  }

}
</script>
{/literal}

</head>


{lang_get var="labels"
          s="title_user_mgmt,th_login,title_user_mgmt,th_login,th_first_name,th_last_name,th_email,
             order_by_role_descr,order_by_role_dir,
             disable,alt_edit_user,Yes,No,alt_delete_user,no_permissions_for_action,btn_create,
             show_inactive_users,hide_inactive_users,alt_disable_user,order_by_login,order_by_login_dir,alt_active_user,
             title_ldap_sync_users,btn_ldap_sync_users,title_ldap_filter,btn_apply_filter,th_toggle_all"}

<body>

{if $grants->user_mgmt == "yes"}

	<h1 class="title">{$labels.title_user_mgmt} - {$labels.title_ldap_sync_users}</h1>
	{***** TABS *****}
  {include file="usermanagement/tabsmenu.tpl"}

	{***** existing users form *****}
	<div class="workBack">
		<form method="post" action="lib/usermanagement/ldapSync.php" name="ldapsync" id="ldapsync">
		<input type="hidden" id="operation" name="operation" value="" />
		<input type="hidden" id="order_by_login_dir" name="order_by_login_dir" value="{$order_by_login_dir}" />
		<input type="hidden" id="user_order_by" name="user_order_by" value="{$user_order_by}" />

	  {include file="inc_update.tpl" result=$result item="user" action="$action" user_feedback=$user_feedback}
		<div>
			{$labels.title_ldap_filter}: 
			<input name="ldap_filter" id="ldap_filter" type="text" value="{$ldap_filter}">
			<input name="doFilterApply" id="ldap_filter_apply" type="submit" value="{$labels.btn_apply_filter}">
		</div>
		<input name="doLdapSync" id="do_ldap_sync" type="submit" value="{$labels.btn_ldap_sync_users}">
		<table class="simple" width="95%">
			<tr>
				<th style="width:3em;">{$labels.th_toggle_all}</th>
				<th {if $user_order_by == 'order_by_login'}style="background-color: #c8dce8;color: black;"{/if}>
				    {$labels.th_login}
				    <img src="{$smarty.const.TL_THEME_IMG_DIR}/order_{$order_by_login_dir}.gif"
				         title="{$labels.order_by_login} {lang_get s=$order_by_login_dir}"
						     alt="{$labels.order_by_role_descr} {lang_get s=$order_by_role_dir}"
				         onclick="usersview.operation.value='order_by_login';
				                  usersview.user_order_by.value='order_by_login';
				                  usersview.submit();" />
				</th>

				<th>{$labels.th_first_name}</th>
				<th>{$labels.th_last_name}</th>
				<th>{$labels.th_email}</th>
			</tr>

      {foreach from=$users key=uid item=user}
				<tr>
				<td align="center"><input type="checkbox" name="ids[]" value="{$uid|escape}"></td>
				<td>{$uid|escape}</td>
				<td>{$user.firstName|escape}</td>
				<td>{$user.lastName|escape}</td>
				<td>{$user.emailAddress|escape}</td>
			</tr>
			{/foreach}
		</table>
		<input name="doLdapSync" id="do_ldap_sync" type="submit" value="{$labels.btn_ldap_sync_users}">
		</form>
	</div>

	{*  BUGID 0000103: Localization is changed but not strings *}
	{if $update_title_bar == 1}
	{literal}
	<script type="text/javascript">
		parent.titlebar.location.reload();
	</script>
	{/literal}
	{/if}
	{if $reload == 1}
	{literal}
	<script type="text/javascript">
		top.location.reload();
	</script>
	{/literal}
	{/if}
{else}
	{$labels.no_permissions_for_action}<br />
	<a href="{$base_href}" alt="Home">Home</a>
{/if}
</body>
</html>
