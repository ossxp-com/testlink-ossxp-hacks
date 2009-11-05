/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: branch_testlink_1_8 $
*/
ALTER TABLE risk_assignments ALTER COLUMN risk TYPE CHAR(1);
ALTER TABLE risk_assignments ALTER COLUMN risk SET NOT NULL;
ALTER TABLE risk_assignments ALTER COLUMN risk SET DEFAULT '2';
COMMENT ON TABLE risk_assignments IS 'Updated to TL 1.7.0 Beta 5';