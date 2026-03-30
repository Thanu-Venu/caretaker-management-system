# SmartCare Payment Processing Cron Job Setup

## Overview
The `process_recurring_payments.php` script handles automated payment processing including:
- Sending payment reminders (7, 3, and 0 days before due date)
- Marking overdue payments
- Auto-cancelling bookings past the grace period

## Setup Instructions

### Windows (Using Task Scheduler)

1. Open Task Scheduler (taskschd.msc)
2. Click "Create Basic Task"
3. Name: "SmartCare Payment Processing"
4. Trigger: Daily at 12:00 AM
5. Action: Start a program
   - Program: `C:\wamp64\bin\php\php8.x.x\php.exe` (adjust to your PHP path)
   - Arguments: `C:\wamp64\www\CMA\app\cron\process_recurring_payments.php`
   - Start in: `C:\wamp64\www\CMA\app\cron`

### Linux/Unix (Using Cron)

1. Open crontab editor:
   ```bash
   crontab -e
   ```

2. Add this line to run daily at midnight:
   ```
   0 0 * * * /usr/bin/php /path/to/CMA/app/cron/process_recurring_payments.php
   ```

3. Save and exit

### Manual Testing

Test the script manually before scheduling:

**Windows:**
```cmd
cd C:\wamp64\www\CMA\app\cron
php process_recurring_payments.php
```

**Linux/Unix:**
```bash
cd /path/to/CMA/app/cron
php process_recurring_payments.php
```

## Logs

Logs are stored in `CMA/logs/payment_cron_YYYY-MM-DD.log`

Check logs regularly to monitor payment processing activity.

## Important Notes

1. Ensure the database migrations have been run:
   - `01_add_payment_fields_to_bookings.sql`
   - `02_create_recurring_payments_table.sql`

2. The script requires CLI access and cannot be run from a browser

3. Make sure the `logs` directory is writable by the web server/cron user

4. Monitor the logs for the first few days after setup to ensure proper operation
