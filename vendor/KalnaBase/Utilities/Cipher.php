<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cipher
 *
 * @author:     Claus Hjort Bube <chb@kalna.dk>
 * @org_author: 
 * @created:    20-11-2013
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       Cipher
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
class Cipher {

    private $securekey, $iv;

    function __construct($textkey) {
        $this->securekey = hash('sha256', $textkey, TRUE);
        $this->iv = mcrypt_create_iv($this->getIvSize());
    }

    function getIvSize() {
        return mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    }

    function encrypt($input, $urlencode = TRUE) {
        $output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
        if ($urlencode) {
            $output = urlencode($output);
        }
        return $output;
    }

    function decrypt($input, $urldecode = FALSE) {
        if ($urldecode) {
            $input = urldecode($input);
        }
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }

}
