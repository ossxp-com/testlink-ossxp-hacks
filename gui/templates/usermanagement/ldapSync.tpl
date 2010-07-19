{*
Testlink Open Source Project - http://testlink.sourceforge.net/

Purpose: smarty template - sync users from ldap

20100719 - jiangxin - initialized.
*}
{include file="inc_head.tpl" openHead="yes"}

{literal}
<script type="text/javascript">
function toggleAllSelection(el) {
  var boxes = el.getElementsBySelector('input[type=checkbox]');
  var all_checked = true;
  for (i = 0; i < boxes.length; i++) { if (boxes[i].checked == false) { all_checked = false; } }
  for (i = 0; i < boxes.length; i++) {
    if (all_checked) {
      boxes[i].checked = false;
    } else if (boxes[i].checked == false) {
      boxes[i].checked = true;
    }
  }
}
</script>
{/literal}

</head>


{lang_get var="labels"
          s="title_user_mgmt,th_login,th_first_name,th_last_name,th_email,no_permissions_for_action,
             menu_ldap_sync_users,btn_ldap_sync_users,btn_apply_filter,th_toggle_all"}

<body>

{if $grants->user_mgmt == "yes"}

	<h1 class="title">{$labels.title_user_mgmt} - {$labels.menu_ldap_sync_users}</h1>
	{***** TABS *****}
  {include file="usermanagement/tabsmenu.tpl"}

	{***** existing users form *****}
	<div class="workBack">
		<form method="post" action="lib/usermanagement/ldapSync.php" name="ldapsync" id="ldapsync">
    <input type="hidden" name="show" value="{$show|escape}">
	  {include file="inc_update.tpl" result=$result item="user" action="$action" user_feedback=$user_feedback}
		<div>
			<input name="doFilterApply" id="ldap_filter_apply" type="submit" value="{$labels.btn_apply_filter}:">
			<input name="ldap_filter" id="ldap_filter" type="text" size="50" value="{$ldap_filter}">
		</div>
    <br>
		<input name="doLdapSync" id="do_ldap_sync" type="submit" value="{$labels.btn_ldap_sync_users}">
		<table class="simple" width="95%">
			<tr>
				<th style="width:3em;"><a href="#" onclick="toggleAllSelection(Element.up(this, &quot;form&quot;)); return false;" title="{$labels.th_toggle_all}">{$labels.th_toggle_all}</a></th>
				<th>{$labels.th_login}</th>
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
    <div>{$summary}</div>
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
