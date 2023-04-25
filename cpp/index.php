<?php
/*
authgg C++ Encryption PoC

Developed by https://github.com/wnelson03, founder of https://keyauth.cc

auth.gg's encryption is implemented poorly, since the owner Outbuilt steals code https://archive.is/b8WZd

as such, all auth.gg programs can be bypassed; regardless of whether they are utilizing obfuscation

Tutorial video: https://files.catbox.moe/ju42i7.mp4
backup video links (in case of wrongful removal): https://web.archive.org/web/20230424213606/https://files.catbox.moe/ju42i7.mp4
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
$type = $_POST['a'];

// key and iv same each request because its outbuilt what you expect
$enc = $_POST['e'];
$enc_array = explode(":",$enc);
$key = $enc_array[0];
$iv = $enc_array[1];

if($type == "start")
{
// initialization
$method = 'aes-256-cfb';

// credits https://stackoverflow.com/a/13212994/13109708
$randString = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);

$ye = base64_encode( openssl_encrypt ("Enabled|Enabled|UPDATEME|1.0|{$randString}|Disabled|Enabled|{$randString}|Enabled", $method, $key, true, $iv));
die($ye .= "|");
}
else if($type == "login")
{

$method = 'aes-256-cfb';

// login
$aid = $_POST['b'];
$aid = base64_decode($aid);
$aid = openssl_decrypt ($aid, $method, $key, OPENSSL_RAW_DATA, $iv);

$apikey = $_POST['d'];
$apikey = base64_decode($apikey);
$apikey = openssl_decrypt ($apikey, $method, $key, OPENSSL_RAW_DATA, $iv);

// credits https://stackoverflow.com/a/10268607/13109708
$ip = long2ip(rand(0, 4294967295));

$text = "success";
$success = $text .= $apikey .= $aid .= $ip;

$hwid = md5(rand());

// credits https://stackoverflow.com/a/13212994/13109708
$email = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);

// credits https://stackoverflow.com/a/20174323/13109708
$date = new DateTime(date("Y-m-d h:i:s"));
$date->add(DateInterval::createFromDateString('5 days'));
$date = $date->format('Y-m-d h:i:s');

$ye = base64_encode( openssl_encrypt ($success .= "|{$hwid}|{$email}@gmail.com|0|{$ip}|{$date}|", $method, $key, true, $iv));
die($ye);
}
else 
{
die();
{
}

}
}
