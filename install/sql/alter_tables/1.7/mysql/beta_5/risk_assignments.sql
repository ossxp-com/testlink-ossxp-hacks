/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE risk_assignments MODIFY COLUMN `risk` CHAR(1) NOT NULL DEFAULT '2';
ALTER TABLE risk_assignments COMMENT = 'Updated to TL 1.7.0 Beta 5';