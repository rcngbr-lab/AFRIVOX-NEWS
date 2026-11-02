<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__).'/app/bootstrap.php';
$cutoff=db_is_sqlite()?"datetime('now','-30 days')":'DATE_SUB(NOW(),INTERVAL 30 DAY)';
$login=db()->exec('DELETE FROM login_attempts WHERE attempted_at<'.$cutoff);$mfa=db()->exec('DELETE FROM mfa_attempts WHERE attempted_at<'.$cutoff);$resets=db()->exec('DELETE FROM password_reset_tokens WHERE used_at IS NOT NULL OR expires_at<'.sql_now());
echo 'Deleted old security records: login='.(int)$login.', mfa='.(int)$mfa.', reset_tokens='.(int)$resets.PHP_EOL;
