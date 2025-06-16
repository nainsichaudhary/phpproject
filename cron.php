<?php
date_default_timezone_set('Asia/Kolkata');

file_put_contents(__DIR__ . '/tmp/cron_log.txt', "Cron ran at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

require_once 'functions.php';

file_put_contents(__DIR__ . '/tmp/xkcd_mail_log.txt', "[" . date('Y-m-d H:i:s') . "] Function triggered\n", FILE_APPEND);

sendXKCDUpdatesToSubscribers();