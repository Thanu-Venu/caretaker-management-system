-- Migration: Add PayHere gateway tracking columns to payments
-- Date: 2026-04-03

USE smartcare;

ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `payhere_order_id` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `payhere_payment_id` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `payhere_status_code` INT NULL,
  ADD COLUMN IF NOT EXISTS `payhere_status_message` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `payhere_md5sig` VARCHAR(255) NULL;

CREATE INDEX IF NOT EXISTS `idx_payments_payhere_order` ON `payments` (`payhere_order_id`);
CREATE INDEX IF NOT EXISTS `idx_payments_payhere_payment` ON `payments` (`payhere_payment_id`);
