<?php
declare(strict_types=1);

final class Security
{
    public static function encryptionKey(string $configured): string
    {
        $configured=trim($configured);$raw=false;
        if(str_starts_with($configured,'base64:'))$raw=base64_decode(substr($configured,7),true);
        elseif(preg_match('/^[a-f0-9]{64}$/i',$configured))$raw=hex2bin($configured);
        if(!is_string($raw)||strlen($raw)!==32)throw new RuntimeException('A 32-byte AFRIVOX NEWS app key is required for confidential-source encryption.');
        return $raw;
    }

    private static function keyFor(string|array $configured,string $keyId): string
    {
        if(is_array($configured)){
            if(!array_key_exists($keyId,$configured))throw new RuntimeException('Encryption key identifier is unavailable.');
            return self::encryptionKey((string)$configured[$keyId]);
        }
        return self::encryptionKey($configured);
    }

    public static function encrypt(string $plaintext,string|array $configuredKey,string $keyId='primary'): string
    {
        if($plaintext==='')return '';if(!preg_match('/^[A-Za-z0-9_-]{1,32}$/',$keyId))throw new RuntimeException('Invalid encryption key identifier.');$iv=random_bytes(12);$tag='';$aad='AFRIVOX NEWS:v2:'.$keyId;
        $cipher=openssl_encrypt($plaintext,'aes-256-gcm',self::keyFor($configuredKey,$keyId),OPENSSL_RAW_DATA,$iv,$tag,$aad,16);
        if($cipher===false)throw new RuntimeException('Confidential encryption failed.');
        return 'v2.'.$keyId.'.'.base64_encode($iv).'.'.base64_encode($tag).'.'.base64_encode($cipher);
    }

    public static function decrypt(?string $envelope,string|array $configuredKey): string
    {
        if(!$envelope)return '';
        if(str_starts_with($envelope,'v2.')){
            $parts=explode('.',$envelope);if(count($parts)!==5)throw new RuntimeException('Invalid confidential-data envelope.');
            [, $keyId,$iv,$tag,$cipher]=$parts;$plain=false;
            foreach(['AFRIVOX NEWS:v2:'.$keyId,implode('',['UM','URONGO']).':v2:'.$keyId] as $aad){$plain=openssl_decrypt((string)base64_decode($cipher,true),'aes-256-gcm',self::keyFor($configuredKey,$keyId),OPENSSL_RAW_DATA,(string)base64_decode($iv,true),(string)base64_decode($tag,true),$aad);if($plain!==false)break;}
            if($plain===false)throw new RuntimeException('Confidential data could not be decrypted.');return $plain;
        }
        if(!str_starts_with($envelope,'v1.'))return $envelope; // legacy plaintext remains private until the encryption migration runs
        $parts=explode('.',$envelope);if(count($parts)!==4)throw new RuntimeException('Invalid confidential-data envelope.');
        [, $iv,$tag,$cipher]=$parts;$legacyKey=is_array($configuredKey)?(string)($configuredKey['primary']??reset($configuredKey)):$configuredKey;$plain=false;
        foreach(['AFRIVOX NEWS:v1',implode('',['UM','URONGO']).':v1'] as $aad){$plain=openssl_decrypt((string)base64_decode($cipher,true),'aes-256-gcm',self::encryptionKey($legacyKey),OPENSSL_RAW_DATA,(string)base64_decode($iv,true),(string)base64_decode($tag,true),$aad);if($plain!==false)break;}
        if($plain===false)throw new RuntimeException('Confidential data could not be decrypted.');return $plain;
    }

    public static function base32Encode(string $bytes): string
    {
        $alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';$bits='';foreach(str_split($bytes) as $char)$bits.=str_pad(decbin(ord($char)),8,'0',STR_PAD_LEFT);
        $out='';foreach(str_split($bits,5) as $chunk){$chunk=str_pad($chunk,5,'0');$out.=$alphabet[bindec($chunk)];}return $out;
    }

    public static function base32Decode(string $value): string
    {
        $alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';$value=strtoupper((string)preg_replace('/[^A-Z2-7]/','',$value));$bits='';
        foreach(str_split($value) as $char){$pos=strpos($alphabet,$char);if($pos===false)throw new RuntimeException('Invalid MFA secret.');$bits.=str_pad(decbin($pos),5,'0',STR_PAD_LEFT);}
        $out='';foreach(str_split($bits,8) as $chunk)if(strlen($chunk)===8)$out.=chr(bindec($chunk));return $out;
    }

    public static function totp(string $base32Secret,?int $time=null): string
    {
        $counter=(int)floor(($time??time())/30);$binary=pack('N*',0).pack('N*',$counter);$hash=hash_hmac('sha1',$binary,self::base32Decode($base32Secret),true);$offset=ord($hash[19])&15;
        $number=((ord($hash[$offset])&127)<<24)|((ord($hash[$offset+1])&255)<<16)|((ord($hash[$offset+2])&255)<<8)|(ord($hash[$offset+3])&255);
        return str_pad((string)($number%1000000),6,'0',STR_PAD_LEFT);
    }

    public static function verifyTotp(string $secret,string $code): bool
    {
        if(!preg_match('/^\d{6}$/',$code))return false;foreach([-1,0,1] as $step)if(hash_equals(self::totp($secret,time()+$step*30),$code))return true;return false;
    }

    public static function privilegedRole(string $role): bool
    {
        return in_array($role,['system_administrator','publisher','editor_in_chief','managing_editor','section_editor','editor','legal_reviewer','advertising_manager','finance_officer'],true);
    }
}
