/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE requirements ADD COLUMN `node_order` INT(10) UNSIGNED DEFAULT '0' AFTER `type`;
ALTER TABLE requirements COMMENT = 'Updated to TL 1.7.0 RC 2';