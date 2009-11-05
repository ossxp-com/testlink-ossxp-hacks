<?php
/** 
* 	TestLink Open Source Project - http://testlink.sourceforge.net/
* 
* 	@version 	$Id: getrequirementnodes.php,v 1.4 2008/09/21 19:35:47 schlundus Exp $
* 	@author 	Francisco Mancardi
* 
*   **** IMPORTANT *****   
*   Created using Ext JS example code
*
* 	Is the tree loader, will be called via AJAX.
*   Ext JS automatically will pass $_REQUEST['node']   
*   Other arguments will be added by TL php code that needs the tree.
*   
*   This tree is used to navigate Test Project, and is used in following feature:
*
*   - Create test suites, test cases on test project
*   - Assign keywords to test cases
*   - Assign requirements to test cases
*
*   rev: 
*        
*/
require_once('../../config.inc.php');
require_once('common.php');
testlinkInitPage($db);


$root_node=isset($_REQUEST['root_node']) ? $_REQUEST['root_node']: null;
$node=isset($_REQUEST['node']) ? $_REQUEST['node'] : $root_node;
$filter_node=isset($_REQUEST['filter_node']) ? $_REQUEST['filter_node'] : null;
$show_children=isset($_REQUEST['show_children']) ? $_REQUEST['show_children'] : 1;
$operation=isset($_REQUEST['operation']) ? $_REQUEST['operation']: 'manage';

// for debug - file_put_contents('d:\request.txt', serialize($_REQUEST));                            
$nodes=display_children($db,$root_node,$node,$filter_node,$show_children,$operation);
echo json_encode($nodes);

/*

*/
function display_children($dbHandler,$root_node,$parent,$filter_node,
                          $show_children=ON,$operation='manage') 
{             
    switch($operation)
    {
        // case 'print':
        //     $js_function=array('testproject' => 'TPROJECT_PTP',
        //                        'testsuite' =>'TPROJECT_PTS', 'testcase' => 'TPROJECT_PTS');
        // break;
        
        case 'manage':
        default:
            $js_function=array('testproject' => 'TPROJECT_REQ_SPEC_MGMT',
                               'requirement_spec' =>'REQ_SPEC_MGMT', 'requirement' => 'REQ_MGMT');
        break;  
    }
    
    $nodes = null;
    $filter_node_type = $show_children ? '' : ",'requirement'";

    $sql = " SELECT NHA.*, NT.description AS node_type " . 
           " FROM nodes_hierarchy NHA, node_types NT " .
           " WHERE NHA.node_type_id=NT.id " .
           " AND parent_id = {$parent} " .
           " AND NT.description NOT IN " .
           " ('testcase','testsuite','testcase_version','testplan'{$filter_node_type}) ";

    if(!is_null($filter_node) && $filter_node > 0 && $parent == $root_node)
    {
       $sql .=" AND NHA.id = {$filter_node} ";  
    }
    $sql .= " ORDER BY NHA.node_order ";    
    
    
    // for debug 
    //file_put_contents('d:\sql_display_node.txt', $sql); 
    $nodeSet = $dbHandler->get_recordset($sql);
    //file_put_contents('d:\nodeSet.txt', serialize($nodeSet)); 
    
    // print_r(array_values($nodeSet));
    // file_put_contents('d:\sql_display_node.txt', serialize(array_values($nodeSet))); 
	if( !is_null($nodeSet) ) 
	{
	    $tproject_mgr = new testproject($dbHandler);
	    foreach($nodeSet as $key => $row)
	    {
	        $path['text'] = htmlspecialchars($row['name']);                                  
	        $path['id'] = $row['id'];                                                           
        
           	 // this attribute/property is used on custom code on drag and drop
	        $path['position'] = $row['node_order'];                                                   
            $path['leaf'] = false;
 	        $path['cls'] = 'folder';
 	        
 	        // Important:
 	        // We can add custom keys, and will be able to access it using
 	        // public property 'attributes' of object of Class Ext.tree.TreeNode 
 	        // 
 	        $path['testlink_node_type']	= $row['node_type'];		                                 
	                                 
	        $tcase_qty = null;
            switch($row['node_type'])
            {
                case 'testproject':
                $path['href'] = "javascript:EP({$path['id']})";
                break;
                
                case 'requirement_spec':
                $path['href'] = "javascript:" . $js_function[$row['node_type']]. "({$path['id']})";
                break;
                
                case 'requirement':
                $path['href'] = "javascript:" . $js_function[$row['node_type']]. "({$path['id']})";
                $path['leaf']	= true;
                break;
            }
            if(!is_null($tcase_qty))
                $path['text'] .= "({$tcase_qty})";   
            
            $nodes[] = $path;                                                                        
	    }	// foreach	
    }
	return $nodes;                                                                             
}                                                                                               