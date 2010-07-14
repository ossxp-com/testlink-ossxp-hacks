{* 
Testlink Open Source Project - http://testlink.sourceforge.net/
$Id: inc_ext_table.tpl,v 1.3 2010/05/02 09:46:57 franciscom Exp $
Purpose: rendering of Ext Js table

rev :
	 20090710 - Eloff - Added comment to explain magic numbers
   20090709 - Eloff - Initial commit
*}


{*
 IMPORTANT:
 Following functions uses global JS variables created 
 using exttable.class.php

 @url http://extjs.com/deploy/dev/examples/grid/array-grid.html
*}
{literal}
<script type="text/javascript">
/*
 statusRenderer() 
 translate this code to a localized string and applies formatting
*/
function statusRenderer(val)
{
  // This must be refactore using same styles that other features
  // and MUST NOT BE HARDCODED HERE
  //
	var style = "";
	if (val == "p")			style = "color: green; font-weight: bold";
	else if (val == "f")	style = "color: red; font-weight: bold";
	else if (val == "n")	style = "color: gray";
	else					style = "color: blue";

	return "<span style=\""+style+"\">" + status_code_label[val] + "</span>";
}

/*
 statusCompare() 
 handles the sorting order by status. 
 It maps a status code to a number. 
 The statuses are then sorted by comparing those numbers.
 WARNING: Global coupling
          uses variable status_code_order
*/
function statusCompare(val)
{
	var order=0;
	order = status_code_order[val];
	if( order == undefined )
	{
	  alert('Configuration Issue - test case execution status code: ' + val + ' is not configured ');
	  order = -1;
	}
	return order;	
}

function priorityRenderer(val)
{
	return prio_code_label[val];
}

Ext.onReady(function() {
  {/literal}
	{foreach from=$gui->tableSet key=idx item=matrix}
    {assign var=tableID value="table_$idx"}
	   store['{$tableID}'] = {literal}new Ext.data.ArrayStore({fields: fields['{/literal}{$tableID}{literal}']});{/literal}
     store['{$tableID}'].loadData(tableData['{$tableID}']);
	  {literal}grid['{/literal}{$tableID}{literal}'] = new Ext.grid.GridPanel({{/literal}
	  	store: store['{$tableID}'],
	  	viewConfig: {ldelim}
	  		forceFit: true
	  	{rdelim},columns: columnData['{$tableID}']
	  	{$matrix->getGridSettings()}{literal}
	  });
	  {/literal}
  {/foreach}

	{foreach from=$gui->tableSet key=idx item=matrix}
    {assign var=tableID value="table_$idx"}
	  grid['{$tableID}'].render('{$tableID}');
  {/foreach}

});
</script>