/* 
$Revision: 1.1.4.1 $
$Date: 2009/05/25 18:39:04 $
$Author: schlundus $
$Name: testlink_1_8_5 $
*/

# already have a unique index for these fields
ALTER TABLE `builds` DROP INDEX `testplan_id`;
ALTER TABLE `priorities` DROP INDEX `testplan_id`;

# remove srs_id from the index and rename it to status
ALTER TABLE `requirements` DROP INDEX `srs_id` , ADD INDEX `status` ( `status` );

# id is already indexed from being a primary key
ALTER TABLE `testprojects` DROP INDEX `id_active` , ADD INDEX `active` ( `active` );