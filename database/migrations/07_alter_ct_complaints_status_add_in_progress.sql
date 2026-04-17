-- ct_complaints.status: Open / In Progress / Resolved / Closed
-- Safe upgrade path from older ENUMs (e.g. Pending / Rejected) via VARCHAR bridge:
ALTER TABLE `ct_complaints` MODIFY COLUMN `status` VARCHAR(32) NOT NULL DEFAULT 'Open';
UPDATE `ct_complaints` SET `status` = CASE
  WHEN `status` = 'Pending' THEN 'Open'
  WHEN `status` = 'Rejected' THEN 'Closed'
  ELSE `status`
END;
ALTER TABLE `ct_complaints`
  MODIFY COLUMN `status` ENUM('Open','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Open';
