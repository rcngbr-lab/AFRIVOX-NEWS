<?php
declare(strict_types=1);

final class PasswordResetService
{
    public static function issue(int $userId): string
    {
        $token=bin2hex(random_bytes(32));$hash=hash('sha256',$token);$expires=date('Y-m-d H:i:s',time()+1800);
        db()->beginTransaction();try{db()->prepare('DELETE FROM password_reset_tokens WHERE user_id=? OR expires_at<'.sql_now().' OR used_at IS NOT NULL')->execute([$userId]);db()->prepare('INSERT INTO password_reset_tokens(user_id,token_hash,expires_at) VALUES (?,?,?)')->execute([$userId,$hash,$expires]);db()->commit();}catch(Throwable $e){if(db()->inTransaction())db()->rollBack();throw $e;}
        audit_log('password_reset_issued','user',$userId);return $token;
    }

    public static function consume(string $token,string $newPassword): bool
    {
        if(!preg_match('/^[a-f0-9]{64}$/',$token)||strlen($newPassword)<14||strlen($newPassword)>1024)return false;$hash=hash('sha256',$token);$row=query_one('SELECT id,user_id FROM password_reset_tokens WHERE token_hash=? AND used_at IS NULL AND expires_at>'.sql_now(),[$hash]);if(!$row)return false;
        db()->beginTransaction();try{$s=db()->prepare('UPDATE password_reset_tokens SET used_at='.sql_now().' WHERE id=? AND used_at IS NULL AND expires_at>'.sql_now());$s->execute([(int)$row['id']]);if($s->rowCount()!==1)throw new RuntimeException('Reset token was already consumed.');db()->prepare('UPDATE users SET password_hash=?,force_password_change=0,password_changed_at='.sql_now().',session_version=session_version+1 WHERE id=?')->execute([password_hash($newPassword,PASSWORD_DEFAULT),(int)$row['user_id']]);db()->prepare('DELETE FROM mfa_attempts WHERE user_id=?')->execute([(int)$row['user_id']]);db()->commit();}catch(Throwable $e){if(db()->inTransaction())db()->rollBack();return false;}
        audit_log('password_reset_completed','user',(int)$row['user_id']);return true;
    }
}
