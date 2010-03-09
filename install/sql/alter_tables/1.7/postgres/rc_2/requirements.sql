/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5b $
*/
ALTER TABLE requirements ADD COLUMN node_order BIGINT DEFAULT 0;
COMMENT ON TABLE requirements IS 'Updated to TL 1.7.0 RC 2';