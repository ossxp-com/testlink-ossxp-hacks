/* 
$Revision: 1.2.2.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE `builds` CHANGE COLUMN `open` `is_open` TINYINT(1) NOT NULL DEFAULT 1;