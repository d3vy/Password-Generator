<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Security\Core\Encoder\{
    Argon2iPasswordEncoder, BCryptPasswordEncoder, Pbkdf2PasswordEncoder, SodiumPasswordEncoder
};
use Symfony\Component\Security\Core\Exception\LogicException;

const DEFAULT_LENGTH    = 16;
const DEFAULT_COST      = 13;

$length     = $_GET['length'] ?? DEFAULT_LENGTH;
$encodeCost = $_GET['cost'] ?? DEFAULT_COST;

$plainPassword = substr(rtrim(strtr(base64_encode(random_bytes($length * 2)), '+/', '-_'), '='), 0, $length);

try {
    $argon2iPassword = (new Argon2iPasswordEncoder)->encodePassword($plainPassword, null);
} catch(\LogicException $ex) {
    $argon2iPassword = '(not supported)';
}

try {
    $sodiumPassword = (new SodiumPasswordEncoder)->encodePassword($plainPassword, null);
} catch(LogicException $ex) {
    $sodiumPassword = '(not supported)';
}

echo (new Pug)->render('output.pug', [
    'defaults'          => [
        'length'    => DEFAULT_LENGTH,
        'cost'      => DEFAULT_COST
    ],
    'plainPassword'     => $plainPassword,
    'md5Password'       => md5($plainPassword),
    'sha1Password'      => sha1($plainPassword),
    'bcryptPassword'    => (new BCryptPasswordEncoder($encodeCost))->encodePassword($plainPassword, null),
    'pbkdf2Password'    => (new Pbkdf2PasswordEncoder)->encodePassword($plainPassword, null),
    'argon2iPassword'   => $argon2iPassword,
    'sodiumPassword'    => $sodiumPassword
]);