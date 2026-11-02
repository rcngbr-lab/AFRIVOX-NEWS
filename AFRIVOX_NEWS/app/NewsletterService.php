<?php
declare(strict_types=1);

final class NewsletterService
{
    /** @return array{state:string,confirmation_token:?string,unsubscribe_token:?string} */
    public static function requestSubscription(string $email, string $locale='rw'): array
    {
        $email=mb_strtolower(trim($email));
        if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190) {
            throw new InvalidArgumentException('invalid_email');
        }
        if (!in_array($locale,['rw','en','fr'],true)) $locale='rw';

        $existing=query_one('SELECT id,status FROM subscribers WHERE email=?',[$email]);
        if (($existing['status']??'')==='suppressed') {
            return ['state'=>'accepted','confirmation_token'=>null,'unsubscribe_token'=>null];
        }
        if (($existing['status']??'')==='confirmed') {
            return ['state'=>'already_confirmed','confirmation_token'=>null,'unsubscribe_token'=>null];
        }

        $confirmation=bin2hex(random_bytes(32));
        $unsubscribe=bin2hex(random_bytes(32));
        $confirmationHash=hash('sha256',$confirmation);
        $unsubscribeHash=hash('sha256',$unsubscribe);
        $expires=date('Y-m-d H:i:s',time()+86400);
        $now=date('Y-m-d H:i:s');
        $pdo=db();
        $pdo->beginTransaction();
        try {
            if ($existing) {
                $update=$pdo->prepare("UPDATE subscribers SET consent_at=?,status='pending',verified_at=NULL,unsubscribed_at=NULL,locale=?,verification_token_hash=?,verification_expires_at=?,unsubscribe_token_hash=?,updated_at=? WHERE id=? AND status<>'suppressed'");
                $update->execute([$now,$locale,$confirmationHash,$expires,$unsubscribeHash,$now,(int)$existing['id']]);
            } else {
                $insert=$pdo->prepare("INSERT INTO subscribers(email,verification_token_hash,verification_expires_at,unsubscribe_token_hash,consent_at,status,locale,updated_at) VALUES (?,?,?,?,?,'pending',?,?)");
                $insert->execute([$email,$confirmationHash,$expires,$unsubscribeHash,$now,$locale,$now]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }

        return ['state'=>'pending','confirmation_token'=>$confirmation,'unsubscribe_token'=>$unsubscribe];
    }

    public static function dispatchConfirmation(string $email, string $locale, string $confirmationToken, string $unsubscribeToken): bool
    {
        $dispatcher=cfg('newsletter_confirmation_dispatcher');
        if (!is_callable($dispatcher)) return false;
        $confirmUrl=absolute_url('verify-newsletter.php?token='.rawurlencode($confirmationToken));
        $unsubscribeUrl=absolute_url('unsubscribe-newsletter.php?token='.rawurlencode($unsubscribeToken));
        $dispatcher($email,$confirmUrl,$unsubscribeUrl,$locale);
        return true;
    }

    public static function confirm(string $token): bool
    {
        if (!preg_match('/^[a-f0-9]{64}$/',$token)) return false;
        $hash=hash('sha256',$token);
        $now=date('Y-m-d H:i:s');
        $statement=db()->prepare("UPDATE subscribers SET status='confirmed',verified_at=?,verification_token_hash=NULL,verification_expires_at=NULL,updated_at=? WHERE verification_token_hash=? AND status='pending' AND verification_expires_at>?");
        $statement->execute([$now,$now,$hash,$now]);
        return $statement->rowCount()===1;
    }

    public static function unsubscribe(string $token): bool
    {
        if (!preg_match('/^[a-f0-9]{64}$/',$token)) return false;
        $hash=hash('sha256',$token);
        $now=date('Y-m-d H:i:s');
        $statement=db()->prepare("UPDATE subscribers SET status='unsubscribed',unsubscribed_at=?,verification_token_hash=NULL,verification_expires_at=NULL,unsubscribe_token_hash=NULL,updated_at=? WHERE unsubscribe_token_hash=? AND status IN ('pending','confirmed')");
        $statement->execute([$now,$now,$hash]);
        return $statement->rowCount()===1;
    }
}
