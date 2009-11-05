-- 
-- $File:$
-- $Revision: 1.2.2.1 $
-- $Date: 2009/05/25 18:39:04 $
-- $Author: schlundus $
-- $Name: branch_testlink_1_8 $
-- 
BEGIN TRANSACTION
SET QUOTED_IDENTIFIER ON
SET ARITHABORT ON
SET NUMERIC_ROUNDABORT OFF
SET CONCAT_NULL_YIELDS_NULL ON
SET ANSI_NULLS ON
SET ANSI_PADDING ON
SET ANSI_WARNINGS ON
COMMIT
BEGIN TRANSACTION
EXECUTE sp_rename N'dbo.tcversions.[open]', N'Tmp_is_open', 'COLUMN'
EXECUTE sp_rename N'dbo.tcversions.Tmp_is_open', N'is_open', 'COLUMN'
COMMIT
