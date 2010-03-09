/* 
$Revision: 1.2.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5b $
*/
ALTER TABLE builds ADD COLUMN active INT2 NOT NULL DEFAULT 1;
ALTER TABLE builds ADD COLUMN open INT2 NOT NULL DEFAULT 1;
COMMENT ON TABLE builds IS 'Updated to TL 1.7.0 Beta 3';

