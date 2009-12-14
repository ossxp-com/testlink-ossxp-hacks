/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/
ALTER TABLE requirements MODIFY req_doc_id varchar(32) default NULL;
ALTER TABLE requirements DROP INDEX req_doc_id; 
ALTER TABLE requirements ADD UNIQUE KEY `req_doc_id` (`srs_id`,`req_doc_id`);
ALTER TABLE requirements COMMENT = 'Updated to TL 1.7.0 Beta 4';
