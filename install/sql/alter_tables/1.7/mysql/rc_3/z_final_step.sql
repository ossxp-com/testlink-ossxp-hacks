/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: branch_testlink_1_8 $
*/
UPDATE rights SET description = 'testplan_user_role_assignment' WHERE id=5;
DELETE FROM rights WHERE id=19;
INSERT INTO db_version VALUES('1.7.0 RC 3', CURRENT_TIMESTAMP());