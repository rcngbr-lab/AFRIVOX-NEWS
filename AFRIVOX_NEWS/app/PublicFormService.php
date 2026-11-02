<?php
declare(strict_types=1);

/**
 * Database-backed throttling for anonymous public forms.
 *
 * The database stores only a keyed digest of the request identity; raw IP
 * addresses and user-agent strings are deliberately not retained.
 */
final class PublicFormService
{
    public static function check(string $formKey, int $limit, int $windowSeconds): void
    {
        if (!preg_match('/^[a-z0-9_-]{2,40}$/', $formKey) || $limit < 1 || $windowSeconds < 60) {
            throw new InvalidArgumentException('Invalid public form throttle policy.');
        }

        $identity=self::identityHash();
        $threshold=date('Y-m-d H:i:s', time()-$windowSeconds);
        $pdo=db();
        $pdo->beginTransaction();
        try {
            $delete=$pdo->prepare('DELETE FROM public_form_attempts WHERE attempted_at < ?');
            $delete->execute([date('Y-m-d H:i:s', time()-604800)]);

            $windowSql='SELECT id FROM public_form_attempts WHERE form_key=? AND identity_hash=? AND attempted_at>=? ORDER BY attempted_at';
            if (!db_is_sqlite()) $windowSql.=' FOR UPDATE';
            $window=$pdo->prepare($windowSql);
            $window->execute([$formKey,$identity,$threshold]);
            if (count($window->fetchAll(PDO::FETCH_COLUMN)) >= $limit) {
                $pdo->rollBack();
                throw new DomainException('rate_limited');
            }

            $insert=$pdo->prepare('INSERT INTO public_form_attempts(form_key,identity_hash) VALUES (?,?)');
            $insert->execute([$formKey,$identity]);
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    private static function identityHash(): string
    {
        $remote=(string)($_SERVER['REMOTE_ADDR']??'unknown');
        $agent=mb_substr((string)($_SERVER['HTTP_USER_AGENT']??'unknown'),0,300);
        $configured=(string)cfg('audit_key','');
        if ($configured==='') {
            $keys=encryption_keys();
            $configured=(string)($keys[active_encryption_key_id()]??'');
        }
        if ($configured==='') throw new RuntimeException('Public form security key is not configured.');
        $key=Security::encryptionKey($configured);
        return hash_hmac('sha256',$remote."\n".$agent,$key);
    }
}
