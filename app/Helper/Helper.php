<?php
namespace App\Helper;
use Jenssegers\Agent\Agent;

class Helper
{
    static function clientInfo(){
        $agent = new Agent();
        $devicename    = $agent->device();
        $browser = $agent->browser();
        $version = $agent->version($browser);
        $ip      = getenv('HTTP_CLIENT_IP') ?:
        getenv('HTTP_X_FORWARDED_FOR') ?:
        getenv('HTTP_X_FORWARDED') ?:
        getenv('HTTP_FORWARDED_FOR') ?:
        getenv('HTTP_FORWARDED') ?:
        getenv('REMOTE_ADDR');

        if($agent->isDesktop())
            $device = 'Desktop';
        if($agent->isPhone())
            $device = 'Phone';
        if($agent->isMobile())
            $device = 'Mobile';
        if($agent->isTablet())
            $device = 'Tablet';

        $cdevice = $device.' '.$devicename;
        $cbrowser  = $browser.' '.$version;
        $plateform = $agent->platform();

        $data = array(
            'device'    => $cdevice,
            'browser'   => $cbrowser,
            'plateform' => $plateform,
            'ip'        => $ip
        );
        return json_encode($data);
    }
    //encryption
    static function CryptoJSAesEncrypt($passphrase, $plain_text){

        $salt = openssl_random_pseudo_bytes(256);
        $iv = openssl_random_pseudo_bytes(16);
        $iterations = 999;
        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

        $encrypted_data = openssl_encrypt($plain_text, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

        $data = array("ciphertext" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "salt" => bin2hex($salt));
        return $data;
    }
    //decryption
   static  function CryptoJSAesDecrypt($passphrase, $encrypted){

         $salt = $encrypted->salt;
         $iv = $encrypted->iv;
         $iterations = 10000;
         $key = hash_pbkdf2("sha512", $passphrase, hex2bin($salt), $iterations, 64);

         $decrypted_data = openssl_decrypt($encrypted->cipherText, 'aes-256-cbc', hex2bin($key), 0, hex2bin($iv));

       return $decrypted_data;
    }
}
?>
