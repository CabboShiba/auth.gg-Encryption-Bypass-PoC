<?php
/*
authgg C# Encryption PoC

Developed by https://github.com/wnelson03, founder of https://keyauth.cc

auth.gg's encryption is implemented poorly, since the owner Outbuilt steals code https://archive.is/b8WZd

as such, all auth.gg programs can be bypassed; regardless of whether they are utilizing obfuscation

Tutorial video: https://youtu.be/LtiPOj6DuAg?t=36
backup video links (in case of wrongful removal): https://files.catbox.moe/8nm18s.mp4
*/
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	die("Invalid request");
}

$type = decrypt($_POST['type']);

$aid = decrypt($_POST['aid']);

$length = intval(substr($aid, 0, 2));

$token = substr($_POST['token'], 0, -$length);
$token = decrypt($token);

$timestamp = substr($_POST['timestamp'], 0, -$length);
$timestamp = decrypt($timestamp);

$username = decrypt($_POST['username']);

$hwid = decrypt($_POST['hwid']);

function decrypt($string)
{
    $plaintext = $string;
    $password = base64_decode($_POST['api_key']);
    $method = 'aes-256-cbc';
    $password = substr(hash('sha256', $password, true), 0, 32);
    $iv = base64_decode($_POST['api_id']);
    $decrypted = openssl_decrypt(base64_decode($plaintext), $method, $password, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}
function encrypt($string)
{
	$plaintext = $string;
    $password = base64_decode($_POST['api_key']);
    $method = 'aes-256-cbc';
    $password = substr(hash('sha256', $password, true), 0, 32);
    $iv = base64_decode($_POST['api_id']);
    $encrypted = base64_encode(openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv));
    return $encrypted;
}
function randnum($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters); $randomString = '';
    for ($i = 0; $i < $length; $i++)  $randomString .= $characters[rand(0, $charactersLength - 1)];
    return $randomString;
}

switch($type) {
    case 'start':
        $bytes = openssl_random_pseudo_bytes(32);
        $randHash = md5($bytes);
        $resp = "{$token}|{$timestamp}|success|Enabled|Enabled|{$randHash}|1.0||Disabled|Enabled|{$randHash}|Enabled|Disabled|" . randnum(3);
        die(encrypt($resp));
    case 'login':
        $hwid = "S-1-5-21-".randnum(9)."-".randnum(10)."-".randnum(10)."-".randnum(4)."";
        $ip = long2ip(rand(0, 4294967295));

        $expiry = new DateTime(date("Y-m-d h:i:s"));
        $expiry->add(DateInterval::createFromDateString('5 days'));
        $expiry = $expiry->format('Y-m-d h:i:s');

        $lastlogin = date('Y-m-d h:i:s',strtotime("-1 days"));

        $regiterdate = date('Y-m-d h:i:s',strtotime("-2 days"));

        $resp = "{$token}|{$timestamp}|success|" . randnum(7) . "|{$username}|{$username}|{$username}|{$hwid}||1|{$ip}|{$expiry}|{$lastlogin}|{$regiterdate}||https://i.imgur.com/xn4APqWs.gif";
        die(encrypt($resp));
    case 'log':
        die();
    default:
        $resp = "{$token}|{$timestamp}|success";
        die(encrypt($resp));
}
