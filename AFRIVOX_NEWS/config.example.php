<?php
return [
  'site_name' => 'AFRIVOX NEWS',
  'site_tagline' => 'Africa Has a Voice.',
  // Add only verified official profiles. Empty values render as disabled icons.
  'social_profiles' => [
    'facebook' => '',
    'x' => '',
    'youtube' => '',
    'linkedin' => '',
  ],
  'base_path' => '', // e.g. /news when installed in a subfolder
  'app_url' => 'https://example.rw', // public canonical base URL, without a trailing slash
  'timezone' => 'Africa/Kigali',
  'db_dsn' => 'mysql:host=localhost;port=3306;dbname=afrivox_news;charset=utf8mb4',
  'db_user' => 'CHANGE_ME',
  'db_password' => 'CHANGE_ME',
  'session_name' => 'afrivox_news_session',
  'environment' => 'production',
  // Key ring supports rotation. Keep real values in a secret manager, never in this file.
  'app_keys' => ['primary' => getenv('AFRIVOX_APP_KEY') ?: getenv('UM'.'URONGO_APP_KEY') ?: ''],
  'active_app_key_id' => 'primary',
  // Separate HMAC key for the append-only audit chain.
  'audit_key' => getenv('AFRIVOX_AUDIT_KEY') ?: getenv('UM'.'URONGO_AUDIT_KEY') ?: '',
  'mfa_issuer' => 'AFRIVOX NEWS',
  // Keep originals outside the public document root.
  'private_media_path' => dirname(__DIR__).'/AFRIVOX_NEWS_PRIVATE_MEDIA',
  // Production approval must fail closed until a scanner integration marks files clean.
  'malware_scanner_required' => true,
  // Private config may provide a reviewed callable: fn(string $email,string $resetUrl): void.
  // Keep null until an approved mail provider and authenticated sending domain exist.
  'password_reset_dispatcher' => null,
  // Reviewed callable signature:
  // fn(string $email,string $confirmUrl,string $unsubscribeUrl,string $locale): void.
  // Pending subscribers never receive a newsletter until this sends a confirmation
  // and the recipient completes double opt-in. Keep null until mail is authorized.
  'newsletter_confirmation_dispatcher' => null,
];
