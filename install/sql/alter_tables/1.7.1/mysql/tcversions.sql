/* 
$Revision: 1.2.2.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: branch_testlink_1_8 $
*/
ALTER TABLE `tcversions` CHANGE COLUMN `open` `is_open` TINYINT(1) NOT NULL DEFAULT 1;