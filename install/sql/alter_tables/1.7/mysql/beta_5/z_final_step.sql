/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/

UPDATE priorities
SET risk=SUBSTRING(risk_importance,1,1),
    importance=SUBSTRING(risk_importance,2,1);

ALTER TABLE priorities DROP COLUMN risk_importance;

INSERT INTO db_version VALUES('1.7.0 Beta 5', CURRENT_TIMESTAMP());