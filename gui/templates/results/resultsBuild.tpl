{* TestLink Open Source Project - http://testlink.sourceforge.net/ *}
{* $Id: resultsBuild.tpl,v 1.2 2008/05/06 06:26:11 franciscom Exp $ *}
{* Purpose: smarty template - show Test Results of one build *}
{include file="inc_head.tpl"}
<body>

<h1 class="title">{$tpName|escape} {lang_get s='title_met_of_build'} {$buildName|escape}</h1>
<div class="workBack">
{include file="inc_res_by_comp.tpl"}
{include file="inc_res_by_ts.tpl"} 
{include file="inc_res_by_keyw.tpl"}

{lang_get s="generated_by_TestLink_on"} {$smarty.now|date_format:$gsmarty_timestamp_format}
</div>

</body>
</html>
