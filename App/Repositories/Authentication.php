<?php

namespace KalnaBase\Repositories;
use KalnaBase\Utilities;
use KalnaBase\System;

class Authentication {

    private $registry;
    private $appConfig;
    private $environment;
    private $internalKey = "7aaKMGWjJGPxPfMx";
    private $iv;
    private $iv_size;
    private $query;
    private $object;
    private $username;
    private $password;
    private $hmacpassword;
    public $loginResult;
    public $userResult;
    public $userType;
    public $step;
    public $count;
    private $objects = array();

    // the constructor!
    public function __construct() {
        $_SESSION['step'] = array('Login');
        $this->registry = Utilities\SessionRegistry::getInstance();
        $this->appConfig = System\AppConfig::getInstance();
        $this->environment = $this->appConfig->values['application']['environment'];
        if (!in_array($this->environment, array('development', 'test'))) {
            include_once UTILPATH . 'ModelLoader.php';
            $this->query = new UserQuery();
        }
    }

    public function getHmacPassword($value) {
        return hash_hmac('sha256', $value, $this->internalKey);
    }

    public function login($username, $password) {
        array_push($_SESSION['step'], 'Login->login(' . $username . ',' . $password . ')');
        $this->username = $username;
        $this->password = $password;
        $_SESSION['login'] = 0;
        if ($this->username == '') {
            $_SESSION['login_msg_level'] = 'alert-danger';
            $_SESSION['login_msg'] = 'Brugernavnet skal angives';
        } elseif ($this->password == '') {
            $_SESSION['login_msg_level'] = 'alert-danger';
            $_SESSION['login_msg'] = 'Adgangskoden skal angives';
        } else {
            if (in_array($this->environment, array('development', 'test'))) {
                $this->userType = 'Unknown';
                $this->checkUserTest();
            } else {
                $this->checkUser();
            }
        }
    }

    private function checkUser() {
        array_push($_SESSION['step'], 'Login->checkUser()');
        $con = Propel::getConnection();
        $sql = "SELECT id 
                FROM users 
                WHERE username = :p_username";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(':p_username' => $this->username));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
//        $result = $this->query->create()->findByUsername($this->username);
        if (count($result) < 1) {
            array_push($_SESSION['step'], 'Login->checkUser()->(count($result) < 1)');
            $_SESSION['login_msg'] = 'Ukendt bruger: ' . $this->username;
            $this->loginResult = FALSE;
            $this->userResult = FALSE;
            $this->registry->authState = 9; // Login failed
            $_SESSION['login'] = 0; // pass check fails
            $this->create();
        } elseif (count($result) > 1) {
            array_push($_SESSION['step'], 'Login->checkUser()->(count($result) > 1)');
            $_SESSION['login_msg'] = 'Ukendt fejl';
            $this->loginResult = FALSE;
            $this->registry->authState = 9; // Login failed
            $_SESSION['login'] = 0; // pass check fails
        } elseif (count($result) == 1) {
            array_push($_SESSION['step'], 'Login->checkUser()->(count($result) == 1)');
            $this->userResult = TRUE;
            $this->hmacpassword = $this->getHmacPassword($this->password);
            $this->checkKey();
        }
    }

    private function checkUserTest() {
        array_push($_SESSION['step'], 'Login->checkUser()');
//        $result = $this->query->create()->findByUsername($this->username);
        if (in_array($this->username, $this->appConfig->values[$this->environment . '_users']['Kreditor'])) {
            array_push($_SESSION['step'], 'Login->checkUser()->(count($result) == 1)');
            $this->userResult = TRUE;
            $this->hmacpassword = $this->getHmacPassword($this->password);
            $this->userType = 'Kreditor';
            $this->checkKeyTest();
        } elseif (in_array($this->username, $this->appConfig->values[$this->environment . '_users']['Debitor'])) {
            array_push($_SESSION['step'], 'Login->checkUser()->(count($result) == 1)');
            $this->userResult = TRUE;
            $this->hmacpassword = $this->getHmacPassword($this->password);
            $this->userType = 'Debitor';
            $this->checkKeyTest();
        } else {
            array_push($_SESSION['step'], 'Login->checkUser()->(count($result) < 1)');
            $_SESSION['login_msg'] = 'Ukendt bruger: ' . $this->username;
            $this->loginResult = FALSE;
            $this->userResult = FALSE;
            $this->registry->authState = 9; // Login failed
            $_SESSION['login'] = 0; // pass check fails
        }
    }

    private function checkKey() {
        array_push($_SESSION['step'], 'Login->checkKey()');
        $con = Propel::getConnection();
        $sql = "SELECT * 
                FROM users 
                WHERE username = :p_username
                AND   password = :p_password";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(':p_username' => $this->username, ':p_password' => $this->hmacpassword));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
//        $result = $this->query->create()->filterByUsername($this->username)->filterByPassword($this->hmacpassword)->findOne();

        if (count($result) < 1) {
            array_push($_SESSION['step'], 'Login->checkKey()->(count($result) < 1)');
            // user/pass check failed
            // redirect to error page
            //$_SESSION['login_msg'] = 'Ukendt bruger';
            $_SESSION['login_msg'] = 'Forkert adgangskode';
            $_SESSION['login'] = 0; // pass check fails
            $this->registry->authState = 9; // Login failed
            $this->loginResult = FALSE;
        } else {
            array_push($_SESSION['step'], 'Login->checkKey()->(count($result) = 1)');
            // register some session variables
            session_regenerate_id(TRUE);
            $_SESSION['result'] = $result;
            $_SESSION['login'] = 1; // user/pass check succes
            $this->registry->authState = 1; // Logged in
            unset($_SESSION['login_msg']); // clear login message
            unset($_SESSION['page']); // clear page
            $_SESSION['username'] = $result[0]['username']; // including the username
            $_SESSION['userName'] = $result[0]['name']; // including the users name
            $_SESSION['userId'] = $result[0]['id']; // including the userid
            $update_sql = "UPDATE users 
                           SET last_login_at = NOW()
                           WHERE id = :p_userid";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->execute(array(':p_userid' => $_SESSION['userId']));
            $this->loginResult = TRUE;
        }
    }

    private function checkKeyTest() {
        array_push($_SESSION['step'], 'Login->checkKey()');

        if ($this->getHmacPassword($this->password) == $this->getHmacPassword($this->username)) {
            array_push($_SESSION['step'], 'Login->checkKey()->(count($result) = 1)');
            // register some session variables
            session_regenerate_id(TRUE);
            $_SESSION['login'] = 1; // user/pass check succes
            $this->registry->authState = 1; // Logged in
            unset($_SESSION['login_msg']); // clear login message
            unset($_SESSION['page']); // clear page
            $_SESSION['username'] = $this->username; // including the username
            $_SESSION['userName'] = $this->username; // including the users name
            $_SESSION['userId'] = 1; // including the userid
            $this->loginResult = TRUE;
        } else {
            array_push($_SESSION['step'], 'Login->checkKey()->(count($result) < 1)');
            // user/pass check failed
            // redirect to error page
            //$_SESSION['login_msg'] = 'Ukendt bruger';
            $_SESSION['login_msg'] = 'Forkert adgangskode';
            $_SESSION['login'] = 0; // pass check fails
            $this->registry->authState = 9; // Login failed
            $this->loginResult = FALSE;
        }
    }

    public function create() {
        $this->step = "create";
        if ($this->loginResult == FALSE) {
            if ($this->userResult == FALSE && !empty($this->username) && !empty($this->password)) {
                $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
                $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                $con = Propel::getConnection();
                $sql = "INSERT INTO users (username, password, name, ivery, created_at)
                         VALUE (:p_username, :p_password, :p_name, :p_ivery, NOW())";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(':p_username' => $this->username, ':p_password' => $this->hmacpassword, ':p_name' => $this->username, ':p_ivery' => $iv));

                $_SESSION['login_msg'] = 'Oprettet bruger';

                $this->checkUser();
            }
        }
    }

    public function logout() {
        $this->registry->clear();
        $this->registry->authState = 2; // Logged out
    }

    public function __set($index, $value) {
        $this->objects[$index] = $value;
    }

    public function __get($index) {
        return $this->objects[$index];
    }

}
