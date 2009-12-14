/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE requirements ALTER COLUMN req_doc_id TYPE varchar(32);
DROP INDEX requirements_req_doc_id; 
CREATE UNIQUE INDEX requirements_req_doc_id ON requirements ("srs_id","req_doc_id");
COMMENT ON TABLE requirements IS 'Updated to TL 1.7.0 Beta 4';