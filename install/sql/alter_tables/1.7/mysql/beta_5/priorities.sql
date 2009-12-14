/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/

ALTER TABLE priorities ADD COLUMN `risk` CHAR(1) NOT NULL DEFAULT '2' AFTER `priority`;
ALTER TABLE priorities ADD COLUMN `importance` CHAR(1) NOT NULL DEFAULT 'M' AFTER `risk`;
ALTER TABLE priorities ADD UNIQUE KEY `tplan_prio_risk_imp`(`testplan_id`,`priority`, `risk`, `importance`);
ALTER TABLE priorities COMMENT = 'Updated to TL 1.7.0 Beta 5';