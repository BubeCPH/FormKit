<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of login
 *
 * @author:     chb
 * @org_author: 
 * @created:    31-05-2012
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       login
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
class Login {

    private $user;
    private $key;
    private $md5key;
    private $resultU;
    private $resultK;
    public $loginResult;
    public $userResult;
    public $step;
    public $count;
    var $Database; //external convert object

    // the constructor!

    public function __construct($user, $key) {
        session_start();
        $this->user = $user;
        $this->key = $key;
        $this->md5key = md5($key);
        $_SESSION['login'] = 0;
        if ($this->user == '') {
            $_SESSION['login_msg_level'] = 'alert-danger';
            $_SESSION['login_msg'] = 'Brugernavnet skal angives';
        } elseif ($this->key == '') {
            $_SESSION['login_msg_level'] = 'alert-danger';
            $_SESSION['login_msg'] = 'Adgangskoden skal angives';
        } else {
            $this->checkUser();
        }
    }

    private function checkUser() {
        $this->step = "checkUser";
        $sqlU = "SELECT id 
                FROM kln_common_users 
                WHERE user = '" . $this->user . "'";
        $this->Database = new Database();
        $this->Database->connect_db('kalnadk_common');
        $this->resultU = $this->Database->request($sqlU);
        $this->Database->close();
        $this->count = "checkUser count: " . count($this->resultU);
        if (count($this->resultU) < 1) {
            $_SESSION['login_msg'] = 'Ukendt bruger: ' . $this->user;
            $this->loginResult = FALSE;
            $this->userResult = FALSE;
            $_SESSION['login'] = 0; // pass check fails
            $this->create();
        } elseif (count($this->resultU) > 1) {
            $_SESSION['login_msg'] = 'Ukendt fejl';
            $this->loginResult = FALSE;
            $_SESSION['login'] = 0; // pass check fails
        } elseif (count($this->resultU) == 1) {
            $this->userResult = TRUE;
            $this->checkKey();
        }
    }

    private function checkKey() {
        $this->step = "checkKey";
        $sqlK = "SELECT *, id AS uid 
                FROM kln_common_users 
                WHERE user = '" . $this->user . "' 
                  AND passkey = '" . $this->md5key . "'";
        $this->Database = new Database();
        $this->Database->connect_db('kalnadk_common');
        $this->resultK = $this->Database->request($sqlK);
        $this->Database->close();
        $this->count = "checkKey count: " . count($this->resultK);

        if (count($this->resultK) < 1) {
            // user/pass check failed
            // redirect to error page
            //$_SESSION['login_msg'] = 'Ukendt bruger';
            $_SESSION['login_msg'] = 'Forkert adgangskode';
            $_SESSION['login'] = 0; // pass check fails
            $this->loginResult = FALSE;
        } else {
            for ($i = 0; $i < count($this->resultK); $i++) {
                extract($this->resultK[$i]);
                // register some session variables
                session_regenerate_id(TRUE);
                $_SESSION['login'] = 1; // user/pass check succes
                unset($_SESSION['login_msg']); // clear login message
                unset($_SESSION['page']); // clear page
                $_SESSION['brugernavn'] = $name; // including the users name
                $_SESSION['user'] = $user; // including the username
                $_SESSION['uid'] = $uid; // including the userid
                $this->loginResult = TRUE;
            }
        }
    }

    public function loginResult() {
        if ($this->loginResult == TRUE) {
            return TRUE;
        } elseif ($this->loginResult == FALSE) {
            return FALSE;
        }
    }

    public function loginStep() {
        return $this->step;
    }

    public function loginCount() {
        return $this->count . $this->md5key;
    }

    public function create() {
        $this->step = "create";
        if ($this->loginResult == FALSE) {
            if ($this->userResult == FALSE && $this->user != '' && $this->md5key != '') {
                $sqlC = "INSERT INTO kln_common_users (user, passkey)
                         VALUE ('" . $this->user . "','" . $this->md5key . "')";
                $this->Database = new Database();
                $this->Database->connect_db('kalnadk_common');
                $this->Database->execute($sqlC);
                $this->Database->close();

                $_SESSION['login_msg'] = 'Oprettet bruger';

                $this->checkUser();
            }
        }
    }

}

?>
