/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE builds ADD COLUMN active TINYINT NOT NULL DEFAULT 1 AFTER notes;
ALTER TABLE builds ADD COLUMN open TINYINT NOT NULL DEFAULT 1 AFTER active;
ALTER TABLE builds COMMENT = 'Updated to TL 1.7.0 Beta 3';
