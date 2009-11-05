/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: branch_testlink_1_8 $
*/

UPDATE priorities
SET risk=SUBSTRING(risk_importance FROM 1 FOR 1),
    importance=SUBSTRING(risk_importance FROM 2 FOR 1);

ALTER TABLE priorities DROP COLUMN risk_importance;

INSERT INTO db_version VALUES('1.7.0 Beta 5', now());