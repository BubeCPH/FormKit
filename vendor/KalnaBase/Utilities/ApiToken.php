<?php
/**
 * Description of Token
 *
 * @author:     Claus Hjort Bube <chb@kalna.dk>
 * @org_author: 
 * @created:    30-10-2013
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       Token
 * @version:    0.1
 * @desc:       class for 
 * 
 * @param
 * - foo are required
 * - bar are optional
 * 
 * @example
 * $m = new email ( "hello there",                           // foo
 *                  "how are you?"                           // bar
 *                );
 * 
 * $m->method();
 */
require_once UTILPATH . 'Cipher.php';
class ApiToken {

    private $controlValue = "hello there";
    private $internalKey = "7aaKMGWjJGPxPfMx";
    private $privateKey = "klSoio2DzzD+J5Mbc+A0123456";
    private $encryptionKey = "IDE+FKWl2mFHMtctSEIF873tj1PluruMMX9oAMzlJpM=";
    private $iv;
    private $iv_size;

    // the constructor!
    public function __construct() {

        # create a random IV to use with CBC encoding
        $this->iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND);
    }

    public function getControlKey($timestamp) {
        return hash_hmac('sha256', $this->controlValue . $timestamp, $this->internalKey);
    }

    public function getgetIv() {
        $this->iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND);
        return $this->iv_size.'#'.$this->iv;
    }

    // the method
    public function getToken($userId) {
        $timestamp = time();
        $data = $this->getControlKey($timestamp) . $timestamp . $userId;

        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential 
        # only suitable for encoded input that never ends with value 00h
        # (because of default zero padding)
        //$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $data, MCRYPT_MODE_CBC, $this->iv);

        # prepend the IV for it to be available for decryption
        //$ciphertext = $this->iv . $ciphertext;

        # encode the resulting cipher text so it can be represented by a string
        //$ciphertext_base64 = base64_encode($ciphertext);

        //return $ciphertext_base64 . "\n";
//        return $ciphertext_base64 . "\n" . $this->getControlKey($timestamp) . "\n" . $timestamp . "\n";
        //return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $data, MCRYPT_MODE_CFB, $this->iv);
        
        $cipher = new Cipher($this->encryptionKey);
        return $cipher->encrypt($data);
    }

    public function getTokenValues($token) {
//        $ciphertext_dec = base64_decode($token);
//
//        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
//        $iv_dec = substr($ciphertext_dec, 0, $this->iv_size);
//
//        # retrieves the cipher text (everything except the $iv_size in the front)
//        $ciphertext_dec = substr($ciphertext_dec, $this->iv_size);
//
//        # may remove 00h valued characters from end of plain text
//        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        $cipher = new Cipher($this->encryptionKey);
        $plaintext_dec = $cipher->decrypt($token);
        $controlKey = substr($plaintext_dec, 0, 64);
        $timestamp = substr($plaintext_dec, 64, 10);
        $userId = substr($plaintext_dec, 74);

        if ($this->getControlKey($timestamp) == $controlKey) {
            return array('timestamp' => $timestamp, 'userId' => $userId);
        } else {
            return array('timestamp' => $timestamp, 'userId' => -999, 'controlKey' => $this->getControlKey($timestamp), 'urlControlKey' => $controlKey, 'ciphertext_dec' => $ciphertext_dec, 'plaintext_dec' => $plaintext_dec);
        }
    }

}

?>
