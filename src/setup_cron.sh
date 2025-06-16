#!/bin/bash

# Absolute path to PHP executable
PHP_PATH=$(which php)

# Absolute path to cron.php script
CRON_SCRIPT="$(pwd)/cron.php"

# The cron job entry: run daily at 9 AM
CRON_JOB="0 9 * * * $PHP_PATH $CRON_SCRIPT"

# Check if the cron job is already installed, add it if not
(crontab -l 2>/dev/null | grep -Fv "$CRON_SCRIPT"; echo "$CRON_JOB") | crontab -