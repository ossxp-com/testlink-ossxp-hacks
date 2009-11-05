{* 
 Testlink Open Source Project - http://testlink.sourceforge.net/ 
 $Id: metricsDashboard.tpl,v 1.6 2008/09/28 10:01:50 franciscom Exp $     
 Purpose: smarty template - main page / site map                 
                                                                 
 rev :                                                   
*}
{lang_get var="labels"
          s="generated_by_TestLink_on,testproject,test_plan,th_total_tc,th_active_tc,th_executed_tc,
             th_executed_vs_active,th_executed_vs_total"}
{include file="inc_head.tpl"}

<body>
<div class="workBack">
<h1 class="title">{$labels.testproject} {$smarty.const.TITLE_SEP} {$gui->tproject_name|escape}</h1>

<table class="mainTable-x" style="width: 100%">
  <tr>
    <th>{$labels.test_plan}</th>
   	<th>{$labels.th_total_tc}</th>
   	<th>{$labels.th_active_tc}</th>
   	<th>{$labels.th_executed_tc}</th>
   	<th>{$labels.th_executed_vs_active}</th>
   	<th>{$labels.th_executed_vs_total}</th>
  </tr>
  {foreach item=metric from=$gui->tplan_metrics}
  <tr>
    <td>{$metric.tplan_name|escape}</td>
    <td style="text-align:right;">{$metric.total}</td>
    <td style="text-align:right;">{$metric.active}</td>
    <td style="text-align:right;">{$metric.executed}</td>
    <td style="text-align:right;">{if $metric.executed_vs_active gt 0}
                                      {$metric.executed_vs_active}
                                  {else} - {/if} </td>
    <td style="text-align:right;">{if $metric.executed_vs_total gt 0}
                                      {$metric.executed_vs_total}
                                  {else} - {/if} </td>
  </tr> 
  {/foreach}

</table>
<br />
{$labels.generated_by_TestLink_on} {$smarty.now|date_format:$gsmarty_timestamp_format}
</div> 
</body>
</html>
