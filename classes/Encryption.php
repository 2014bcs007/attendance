<?php

class Encryption {

    var $key = "UNCESystem2015Wr_Receipt"; // This is the Salt

    public function safe_b64encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
        return $string;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
        return $string;
    }

    public function encode1($value) {

        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
        return $value;
    }

    public function decode1($value) {

        if (!$value) {
            return false;
        }
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
        return $value;
    }

    //protected $key;
    protected $data;
    protected $method;

    /**
     * Available OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
     *
     * @var type $options
     */
    protected $options = 0;
    protected $phpVersion = 0;

    /**
     * 
     * @param type $data
     * @param type $key
     * @param type $blockSize
     * @param type $mode
     */
    function __construct() {
        //$data = null, $key = null, $blockSize = null, $mode = 'CBC'
        //$this->setData($data);
        $this->setKey($key);
        $this->setMethode(256, 'CBC');
        $this->phpVersion = phpversion();
    }

    /**
     * 
     * @param type $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * 
     * @param type $key
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     * CBC 128 192 256 
      CBC-HMAC-SHA1 128 256
      CBC-HMAC-SHA256 128 256
      CFB 128 192 256
      CFB1 128 192 256
      CFB8 128 192 256
      CTR 128 192 256
      ECB 128 192 256
      OFB 128 192 256
      XTS 128 256
     * @param type $blockSize
     * @param type $mode
     */
    public function setMethode($blockSize, $mode = 'CBC') {
        if ($blockSize == 192 && in_array('', array('CBC-HMAC-SHA1', 'CBC-HMAC-SHA256', 'XTS'))) {
            $this->method = null;
            throw new Exception('Invlid block size and mode combination!');
        }
        $this->method = 'AES-' . $blockSize . '-' . $mode;
    }

    /**
     * 
     * @return boolean
     */
    public function validateParams() {
        if ($this->data != null &&
                $this->method != null) {
            return true;
        } else {
            return FALSE;
        }
    }

//it must be the same when you encrypt and decrypt
    protected function getIV() {
        return '1234567890123456';
        //return mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND);
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
    }

    /**
     * @return type
     * @throws Exception
     */
    public function encode($data) {
        $this->setData($data);
        if ($this->phpVersion > 5.0) {
            if ($this->validateParams()) {
                return trim($this->safe_b64encode(openssl_encrypt($this->data, $this->method, $this->key, $this->options, $this->getIV())));
            } else {
                //throw new Exception('Invlid params!');
                return $data;
            }
        } else {
            return $this->encode1($data);
        }
    }

    /**
     * 
     * @return type
     * @throws Exception
     */
    public function decode($data) {
        if ($this->phpVersion > 5.0) {
            $data = $this->safe_b64decode($data);
            $this->setData($data);
            if ($this->validateParams()) {
                $ret = openssl_decrypt($this->data, $this->method, $this->key, $this->options, $this->getIV());

                return trim($ret);
            } else {
                //throw new Exception('Invlid params!');
                return $data;
            }
        } else {
            return $this->decode1($data);
        }
    }

    public function footer_left($data) {

        $this->setData($data);
        if ($this->phpVersion > 5.0) {
            if ($this->validateParams()) {
                return trim($this->safe_b64encode(openssl_encrypt($this->data, $this->method, $this->key, $this->options, $this->getIV())));
            } else {
                //throw new Exception('Invlid params!');
                return $data;
            }
        } else {
            return $this->encode1($data);
        }
    }

    public function navigation($data) {

        if ($this->phpVersion > 5.0) {
            $data = $this->safe_b64decode($data);
            $this->setData($data);
            if ($this->validateParams()) {
                $ret = openssl_decrypt($this->data, $this->method, $this->key, $this->options, $this->getIV());

                return trim($ret);
            } else {
                //throw new Exception('Invlid params!');
                return $data;
            }
        } else {
            return $this->decode1($data);
        }
    }


}
