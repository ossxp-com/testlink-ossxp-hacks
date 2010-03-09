/* 
$Revision: 1.3.8.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5b $

20051013 - fm - added Test Plan info in build name
Migration from 1.5.x to 1.6 POST RC1 - 20050925 - fm

*/
UPDATE  build SET name=CONCAT("BUILD ",build, " - Test Plan ID:", projid) 
WHERE (name='undefined' or name IS NULL or name='');
ALTER TABLE `build` COMMENT = 'Updated to TL 1.6 POST RC1';
