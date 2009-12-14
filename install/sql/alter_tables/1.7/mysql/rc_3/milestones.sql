/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE milestones CHANGE COLUMN `date` `target_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE milestones COMMENT = 'Updated to TL 1.7.0 RC 3';