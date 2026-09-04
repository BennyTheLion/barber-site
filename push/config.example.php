<?php
// push/config.example.php - copy this to push/config.php and fill in real
// VAPID keys. push/config.php is gitignored (same pattern as
// config/config.php) because the private key must stay secret and each
// deployment (local / production) should have its own keypair - keys are
// never shared across environments since each DB only stores subscriptions
// created against its own public key.
//
// Generate a keypair with:
//   php -r "require 'vendor/autoload.php'; $k = Minishlink\WebPush\VAPID::createVapidKeys(); echo 'PUBLIC='.$k['publicKey'].PHP_EOL.'PRIVATE='.$k['privateKey'].PHP_EOL;"
//
// Do NOT regenerate the keys once real customers/admins have subscribed -
// every existing subscription becomes invalid and everyone has to opt back in.

define('PUSH_VAPID_PUBLIC_KEY', 'REPLACE_WITH_GENERATED_PUBLIC_KEY');
define('PUSH_VAPID_PRIVATE_KEY', 'REPLACE_WITH_GENERATED_PRIVATE_KEY');

// Contact address the push services may use to reach the site owner about
// this VAPID identity (required by the Web Push protocol).
define('PUSH_VAPID_SUBJECT', 'mailto:' . SMTP_USERNAME);

// XAMPP's PHP build on Windows doesn't point openssl at a config file by
// default, which makes the EC key operations VAPID signing needs fail with
// "Unable to create the key". Only set this when that local file exists, so
// production hosting (which doesn't need it) is unaffected.
$__pushOpensslCnf = 'C:/xampp/php/extras/openssl/openssl.cnf';
if (empty(getenv('OPENSSL_CONF')) && file_exists($__pushOpensslCnf)) {
    putenv('OPENSSL_CONF=' . $__pushOpensslCnf);
}
unset($__pushOpensslCnf);
