<?php

class Login extends common_function {
    public $db_connection = null;
    private $login_user_id = null;
    private $email = "";
    private $user_email = "";
    private $user_is_logged_in = false;
    public $user_gravatar_image_url = "";
    public $user_gravatar_image_tag = "";
    private $password_reset_link_is_valid = false;
    private $first_time_password_reset_link_is_valid = false;
    private $password_reset_was_successful = false;
    public $errors = array();
    public $messages = array();
    public $current_user = array();
    public function __construct() { 
          
        if ($this->db_connection == null) {
            $db_connection = new DB_Class();
            $this->db_connection = $GLOBALS['conn'];
        }
       
      
        if (isset($_GET["logout"])) {
            $this->doLogout();
        } elseif (!empty($_SESSION['email']) && ($_SESSION['user_logged_in'] == 1)) {
   
            $this->loginWithSessionData();
            if (isset($_POST["user_edit_submit_name"])) {
                $this->editUserName($_POST['user_name']);
            } elseif (isset($_POST["user_edit_submit_email"])) {
                $this->editUserEmail($_POST['user_email']);
            } elseif (isset($_POST["user_edit_submit_password"])) {
                $this->editUserPassword($_POST['user_password_old'], $_POST['user_password_new'], $_POST['user_password_repeat']);
            }
        } elseif (isset($_POST["login"])) {
            $this->loginWithPostData($_POST['email'], md5($_POST['password']));
        }
        if (isset($_POST["request_password_reset"]) && isset($_POST['user_name'])) {
            $this->setPasswordResetDatabaseTokenAndSendMail($_POST['user_name']);
        } elseif (isset($_GET["user_name"]) && isset($_GET["verification_code"]) && !isset($_POST["submit_new_password"])) {
            $this->checkIfEmailVerificationCodeIsValid($_GET["user_name"], $_GET["verification_code"]);
        } elseif (isset($_POST["submit_new_password"])) {
            $this->editNewPassword($_POST['user_name'], $_POST['user_password_reset_hash'], $_POST['user_password_new'], $_POST['user_password_repeat']);
        }
        if (isset($_GET["id"]) && isset($_GET["verification_code"]) && !isset($_POST["first_time_submit_new_password"])) {
            $this->checkIfEmailVerificationCode_and_user_id_IsValid($_GET["id"], $_GET["verification_code"]);
        } elseif (isset($_POST["first_time_submit_new_password"])) {
            $this->first_time_editNewPassword($_POST['user_id'], $_POST['user_password_reset_hash'], $_POST['user_password_new'], $_POST['user_password_repeat']);
        }
    }
    public function getUserData($user_name = '') {
        $user_name = ($user_name == '') ? $this->user_email : $user_name;
        $query_user = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE user_email = '$user_name'");
        if (isset($query_user) && $query_user->num_rows > 0) {
            $this->current_user = $query_user->fetch_object();
                } else {
            return false;
        }
        $user_id = $this->current_user->user_id;
        $this->current_user->role = $this->getUserRole();
        return $this->current_user;
    }

    public function getUserDataById($user_id = '') {
        $query_user = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE user_id = '$user_id'");
        if (isset($query_user) && $query_user->num_rows > 0) {
            return $query_user->fetch_object();
        } else {
            return false;
        }
    }
    private function loginWithSessionData() {
        $this->user_email = $_SESSION['email'];
        $this->current_user = $_SESSION['current_user'];     
        $this->user_is_logged_in = true;
    }
    private function loginWithCookieData() {
        if (isset($_COOKIE['rememberme'])) {
            list ($user_id, $token, $hash) = explode(':', $_COOKIE['rememberme']);
            if ($hash == hash('sha256', $user_id . ':' . $token . COOKIE_SECRET_KEY) && !empty($token)) {
                if ($this->db_connection) {
                    $sth = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE user_id = $user_id
                                                      AND user_rememberme_token = '$token' AND user_rememberme_token IS NOT NULL");

                    $result_row = $sth->fetch_object();
                    if (isset($result_row->user_id)) {
                         $_SESSION['store']=$_GET['store'];
                        $_SESSION['user_id'] = $result_row->user_id;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['current_user'] = $result_row;
                        $_SESSION['user_logged_in'] = 1;
                        $this->user_id = $result_row->user_id;
                        $this->user_email = $result_row->user_email;
                        $this->user_is_logged_in = true;
                        $this->newRememberMeCookie();
                        return true;
                    }
                }
            }
            $this->deleteRememberMeCookie();
            $this->errors[] = MESSAGE_COOKIE_INVALID;
        }
        return false;
    }
    public function loginWithPostData($email, $password) {
        $flg = 0;
        if (empty($email)) {
            $this->errors[] = CLS_MESSAGE_USERNAME_EMPTY;
        } else if (empty($password)) {
            $this->errors[] = CLS_MESSAGE_PASSWORD_EMPTY;
        } else {
            if ($this->db_connection) {
            $where_query = array(["", "email", "=", "$email"],["AND", "password", "=", "$password"]);
            $resource_array = array('single' => true);
            $comeback = $this->select_result(CLS_TABLE_LOGIN_USER, '*', $where_query, $resource_array);
            if (isset($comeback['status']) && $comeback['status'] == 1) {
//                $row = $store_information;
                $_SESSION['store']=$comeback['data']->store;
                $_SESSION['login_user_id'] = $comeback['data']->login_user_id;
                $_SESSION['email'] = $comeback['data']->email;
                $_SESSION['current_user'] = $comeback['data'];
                $_SESSION['user_logged_in'] = 1;
                $this->login_user_id = $comeback['data']->login_user_id;
                $this->email = $comeback['data']->email;
                $this->user_is_logged_in = true;
                $this->current_user = $comeback['data'];
                $flg = 1;
                return $flg;
            } else {
                 $this->errors[] =  CLS_LOGIN_MESSAGE ;
            }
              
        }
    }
    }
    private function newRememberMeCookie() {
        if ($this->db_connection) {
            $random_token_string = hash('sha256', mt_rand());
            $sth = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_rememberme_token = '$random_token_string' WHERE user_id = " . $_SESSION['user_id']);
            $cookie_string_first_part = $_SESSION['user_id'] . ':' . $random_token_string;
            $cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
            $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;
            setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
        }
    }
    private function deleteRememberMeCookie() {
        if ($this->db_connection) {
            $sth = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_rememberme_token = NULL WHERE user_id =" . $_SESSION['user_id']);
        }
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }
    public function doLogout() {
        if (isset($_SESSION['user_id'])) {
            $this->deleteRememberMeCookie();

            $_SESSION = array();
            session_destroy();
        }
        $this->user_is_logged_in = false;
    }
    public function isUserLoggedIn() {
        return $this->user_is_logged_in;
    }
    public function editUserName($user_name) {
        $user_name = substr(trim($user_name), 0, 64);

        if (!empty($user_name) && $user_name == $_SESSION['user_name']) {
            $this->errors[] = MESSAGE_USERNAME_SAME_LIKE_OLD_ONE;
        } elseif (empty($user_name) || !preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $user_name)) {
            $this->errors[] = MESSAGE_USERNAME_INVALID;
        } else {
            $result_row = $this->getUserData($user_name);
            if (isset($result_row->user_id)) {
                $this->errors[] = MESSAGE_USERNAME_EXISTS;
            } else {
                $query_edit_user_name = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_email = '$user_name' WHERE user_id = " . $_SESSION['user_id']);
                if ($query_edit_user_name) {
                    $_SESSION['user_name'] = $user_name;
                    $this->messages[] = MESSAGE_USERNAME_CHANGED_SUCCESSFULLY . $user_name;
                } else {
                    $this->errors[] = MESSAGE_USERNAME_CHANGE_FAILED;
                }
            }
        }
    }
    public function editUserEmail($user_email) {
        $user_email = substr(trim($user_email), 0, 64);
        if (!empty($user_email) && $user_email == $_SESSION["user_email"]) {
            $this->errors[] = MESSAGE_EMAIL_SAME_LIKE_OLD_ONE;
        } elseif (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = MESSAGE_EMAIL_INVALID;
        } else if ($this->db_connection) {
            $query_user = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE user_email = '$user_email'");
            $result_row = $query_user->fetch_object();
            if (isset($result_row->user_id)) {
                $this->errors[] = MESSAGE_EMAIL_ALREADY_EXISTS;
            } else {
                $user_activation_hash = sha1(uniqid(mt_rand(), true));
                $user_id = $_SESSION['user_id'];
                if ($this->sendVerificationEmail($user_id, $user_email, $user_activation_hash)) {
                    $query_edit_user_email = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_email = '$user_email',user_activation_hash='$user_activation_hash',user_active=0 WHERE user_id =" . $user_id);
                    if ($query_edit_user_email) {
                        $_SESSION['user_email'] = $user_email;
                        $this->messages[] = MESSAGE_EMAIL_CHANGED_SUCCESSFULLY . $user_email;
                    } else {
                        $this->errors[] = MESSAGE_EMAIL_CHANGE_FAILED;
                    }
                } else {
                    $this->errors[] = MESSAGE_EMAIL_CHANGE_FAILED;
                }
            }
        }
    }

    public function sendVerificationEmail($user_id, $user_email, $user_activation_hash) {
        require_once ABS_PATH . '/libraries/vendor/autoload.php';

        $from = EMAIL_VERIFICATION_FROM;
        $fromname = EMAIL_VERIFICATION_FROM_NAME;
        $subject = EMAIL_VERIFICATION_SUBJECT;
        $link = EMAIL_VERIFICATION_URL . '?id=' . urlencode($user_id) . '&verification_code=' . urlencode($user_activation_hash);
        $message = EMAIL_VERIFICATION_CONTENT . ' ' . $link;

        $from = new SendGrid\Email($fromname, $from);
        $to = new SendGrid\Email("", $user_email);
        $content = new SendGrid\Content("text/html", $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $apiKey = SENDGRID_APIKEY;
        $sg = new \SendGrid($apiKey);
        $response = $sg->client->mail()->send()->post($mail);
        if ($response->statusCode() == '202') {
            return true;
        } else {
            $msg = SOMETHING_WENT_WRONG_MSG;
            if (isset(json_decode($response->body())->errors[0]->message) && !empty(json_decode($response->body())->errors[0]->message)) {
                $msg = json_decode($response->body())->errors[0]->message;
            }
            $this->errors[] = MESSAGE_VERIFICATION_MAIL_NOT_SENT . $msg;
            return false;
        }
    }

    public function sendVerificationEmail_old($user_id, $user_email, $user_activation_hash) {
        $mail = new PHPMailer;

        if (EMAIL_USE_SMTP) {
            $mail->IsSMTP();
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }
        $mail->From = EMAIL_VERIFICATION_FROM;
        $mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_VERIFICATION_SUBJECT;
        $link = EMAIL_VERIFICATION_URL . '?id=' . urlencode($user_id) . '&verification_code=' . urlencode($user_activation_hash);
        $mail->Body = EMAIL_VERIFICATION_CONTENT . ' ' . $link;
        if (!$mail->Send()) {
            $this->errors[] = MESSAGE_VERIFICATION_MAIL_NOT_SENT . $mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }
    public function editUserPassword($user_password_old, $user_password_new, $user_password_repeat) {
        if (empty($user_password_new) || empty($user_password_repeat) || empty($user_password_old)) {
            $this->errors[] = MESSAGE_PASSWORD_EMPTY;
        } elseif ($user_password_new !== $user_password_repeat) {
            $this->errors[] = MESSAGE_PASSWORD_BAD_CONFIRM;
        } elseif (strlen($user_password_new) < 6) {
            $this->errors[] = MESSAGE_PASSWORD_TOO_SHORT;
        } else {
            $result_row = $this->getUserData($_SESSION['user_name']);
            if (isset($result_row->user_password_hash)) {
                if (md5($user_password_old) == $result_row->user_password_hash) {
                    $user_password_hash = md5($user_password_new);
                    $query_update = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_password_hash = '$user_password_hash' WHERE user_id = " . $_SESSION['user_id'] . " LIMIT 1;");
                    if ($query_update) {
                        $query_update = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_type = '3' WHERE user_id = " . $_SESSION['user_id'] . " AND user_type = '2' LIMIT 1;");

                        $this->messages[] = MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY;
                    } else {
                        $this->errors[] = MESSAGE_PASSWORD_CHANGE_FAILED;
                    }
                } else {
                    $this->errors[] = MESSAGE_OLD_PASSWORD_WRONG;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
    }
    public function setPasswordResetDatabaseTokenAndSendMail($user_name) {
        $shop = $_GET['shop'];
        $user_name = trim($user_name);
        if (empty($user_name)) {
            $this->errors[] = MESSAGE_USERNAME_EMPTY;
        } else {
            $temporary_timestamp = time();
            $user_password_reset_hash = sha1(uniqid(mt_rand(), true));
            $result_row = $this->getUserData($user_name);
            if (isset($result_row->user_id)) {
                $query_update = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_password_reset_hash = '$user_password_reset_hash',
                                                               user_password_reset_timestamp = '$temporary_timestamp'
                                                               WHERE user_email = '$user_name'");

                if (isset($query_update)) {
                    $pass_link = EMAIL_PASSWORDRESET_URL . '?shop=' . $shop . '&user_name=' . urlencode($user_name) . '&verification_code=' . urlencode($user_password_reset_hash);
                    $user_email = $user_name;
                    $subject = 'ReConvert affiliate password reset';
                    $subject = htmlspecialchars($subject, ENT_QUOTES, "ISO-8859-1");
                    $template_url = SITE_URL . 'email_template/forget-password.html';
                    $body = file_get_contents($template_url);
                    $find_repl_arr = array(
                        '{first_name}' => $result_row->first_name,
                        '{last_name}' => $result_row->last_name,
                        '{password_reset_url}' => $pass_link,
                        '{MAIL_TO}' => 'mailto:' . SITE_EMAIL,
                        '{CALL_TO}' => 'tel:' . SITE_PHONE,
                        '{SITE_EMAIL}' => SITE_EMAIL,
                        '{SITE_PHONE}' => SITE_PHONE
                    );
                    $body = str_replace(array_keys($find_repl_arr), array_values($find_repl_arr), $body);
                    $mail_sent = $this->send_email($user_email, $subject, $body);
                } else {
                    $this->errors[] = MESSAGE_DATABASE_ERROR;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
        return false;
    }

    public function sendPasswordResetMail($user_email, $subject, $message) {
        require_once ABS_PATH . '/libraries/vendor/autoload.php';

        $from = EMAIL_PASSWORDRESET_FROM;
        $fromname = EMAIL_PASSWORDRESET_FROM_NAME;
        $from = new SendGrid\Email($fromname, $from);
        $to = new SendGrid\Email("", $user_email);
        $content = new SendGrid\Content("text/html", $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $apiKey = SENDGRID_APIKEY;
        $sg = new \SendGrid($apiKey);
        $response = $sg->client->mail()->send()->post($mail);
        if ($response->statusCode() == '202') {
            $this->messages[] = MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT;
            return true;
        } else {
            $msg = SOMETHING_WENT_WRONG_MSG;
            if (isset(json_decode($response->body())->errors[0]->message) && !empty(json_decode($response->body())->errors[0]->message)) {
                $msg = json_decode($response->body())->errors[0]->message;
            }
            $this->errors[] = MESSAGE_PASSWORD_RESET_MAIL_FAILED . $msg;
            return false;
        }
    }
    public function sendPasswordResetMail_old($user_email, $subject, $message) {
        $mail = new PHPMailer;
        if (EMAIL_USE_SMTP) {
            $mail->IsSMTP();
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }
        $mail->From = EMAIL_PASSWORDRESET_FROM;
        $mail->FromName = EMAIL_PASSWORDRESET_FROM_NAME;
        $mail->AddAddress($user_email);
        $mail->Subject = $subject;
        $mail->Body = $message;
        if (!$mail->Send()) {
            $this->errors[] = MESSAGE_PASSWORD_RESET_MAIL_FAILED . $mail->ErrorInfo;
            return false;
        } else {
            $this->messages[] = MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT;
            return true;
        }
    }
    public function checkIfEmailVerificationCodeIsValid($user_name, $verification_code) {
        $user_name = trim($user_name);
        if (empty($user_name) || empty($verification_code)) {
            $this->errors[] = MESSAGE_LINK_PARAMETER_EMPTY;
        } else {
            $result_row = $this->getUserData($user_name);
            if (isset($result_row->user_id) && $result_row->user_password_reset_hash == $verification_code) {
                $timestamp_one_hour_ago = time() - 3600; // 3600 seconds are 1 hour
                if ($result_row->user_password_reset_timestamp > $timestamp_one_hour_ago) {
                    // set the marker to true, making it possible to show the password reset edit form view
                    $this->password_reset_link_is_valid = true;
                } else {
                    $this->errors[] = MESSAGE_RESET_LINK_HAS_EXPIRED;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
    }
    public function editNewPassword($user_name, $user_password_reset_hash, $user_password_new, $user_password_repeat) {
        $user_name = trim($user_name);
        if (empty($user_name) || empty($user_password_reset_hash) || empty($user_password_new) || empty($user_password_repeat)) {
            $this->errors[] = MESSAGE_PASSWORD_EMPTY;
        } else if ($user_password_new !== $user_password_repeat) {
            $this->errors[] = MESSAGE_PASSWORD_BAD_CONFIRM;
        } else if (strlen($user_password_new) < 6) {
            $this->errors[] = MESSAGE_PASSWORD_TOO_SHORT;
        } else if ($this->db_connection) {
            $user_password_hash = md5($user_password_new);
            $query_update = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_password_hash = '$user_password_hash',
                                                           user_password_reset_hash = NULL, user_password_reset_timestamp = NULL,updated = '" . DATE . "'
                                                           WHERE user_email = '$user_name' AND user_password_reset_hash = '$user_password_reset_hash'");

            if ($query_update) {
                $this->password_reset_was_successful = true;
                $this->messages[] = MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY;
            } else {
                $this->errors[] = MESSAGE_PASSWORD_CHANGE_FAILED;
            }
        }
        $this->password_reset_link_is_valid = true;
    }

    public function getUserDataByUserid($user_id = '') {
        $user_id = ($user_id == '') ? $this->user_id : $user_id;
        $query_user = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE user_id = '$user_id'");
        if (isset($query_user) && $query_user->num_rows > 0) {
            $this->current_user = $query_user->fetch_object();
        } else {
            return false;
        }
        $user_id = $this->current_user->user_id;
        $this->current_user->role = $this->getUserRole();
        return $this->current_user;
    }
    public function checkIfEmailVerificationCode_and_user_id_IsValid($user_name, $verification_code) {
        $user_name = trim($user_name);
        if (empty($user_name) || empty($verification_code)) {
            $this->errors[] = MESSAGE_LINK_PARAMETER_EMPTY;
        } else {
            $result_row = $this->getUserDataByUserid($user_name);
            if (isset($result_row->user_id) && $result_row->user_password_reset_hash == $verification_code) {
                $timestamp_one_hour_ago = time() - (3600 * 48); // 3600 seconds are 1 hour
                if ($result_row->user_password_reset_timestamp > $timestamp_one_hour_ago) {
                    $this->first_time_password_reset_link_is_valid = true;
                } else {
                    $this->errors[] = MESSAGE_RESET_LINK_HAS_EXPIRED;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
    }
    public function first_time_editNewPassword($user_name, $user_password_reset_hash, $user_password_new, $user_password_repeat) {
        $user_name = trim($user_name);
        if (empty($user_name) || empty($user_password_reset_hash) || empty($user_password_new) || empty($user_password_repeat)) {
            $this->errors[] = MESSAGE_PASSWORD_EMPTY;
        } else if ($user_password_new !== $user_password_repeat) {
            $this->errors[] = MESSAGE_PASSWORD_BAD_CONFIRM;
        } else if (strlen($user_password_new) < 6) {
            $this->errors[] = MESSAGE_PASSWORD_TOO_SHORT;
        } else if ($this->db_connection) {
            $user_password_hash = md5($user_password_new);
            $query_update = $this->db_connection->query("UPDATE " . TABLE_SELLER_USERS . " SET user_password_hash = '$user_password_hash',
                                                           user_password_reset_hash = NULL, user_password_reset_timestamp = NULL,updated = '" . DATE . "'
                                                           WHERE user_id = '$user_name' AND user_password_reset_hash = '$user_password_reset_hash'");
            if ($query_update) {
                $query_user = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE user_id = '$user_name'");
                $result_row = $query_user->fetch_object();
                $_SESSION['user_id'] = $result_row->user_id;
                $_SESSION['user_name'] = $result_row->user_email;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['current_user'] = $result_row;
                $_SESSION['user_logged_in'] = 1;
                $this->user_id = $result_row->user_id;
                $this->user_name = $result_row->user_email;
                $this->user_email = $result_row->user_email;
                $this->user_is_logged_in = true;
                $this->current_user = $result_row;
                $html = '';
                $flg = 1;
               
            }
            if ($query_update) {
                $this->messages[] = MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY;
            } else {
                $this->errors[] = MESSAGE_PASSWORD_CHANGE_FAILED;
            }
        }
        $this->first_time_password_reset_link_is_valid = true;
    }

    public function firsttimepasswordResetLinkIsValid() {
        return $this->first_time_password_reset_link_is_valid;
    }

    public function passwordResetLinkIsValid() {
        return $this->password_reset_link_is_valid;
    }
    public function passwordResetWasSuccessful() {
        return $this->password_reset_was_successful;
    }
    public function getUserRole() {
        $user_role = new stdClass();
        if ($this->current_user->user_type == 1) {
            $user_role->is_admin = 1;
        } elseif ($this->current_user->user_type == 2) {
            $user_role->is_marchant = 1;
        } elseif ($this->current_user->user_type == 3) {
            $user_role->is_marchant = 1;
        }
        return $user_role;
    }

    public function getValidationMessage() {
        if ($this->errors) {
            echo '<div class="alert alert-danger">';
            foreach ($this->errors as $error) {
                echo $error . '<br>';
            }
            echo '</div>';
        }
        if ($this->messages) {
            echo '<div class="alert alert-success">';
            foreach ($this->messages as $message) {
                echo $message . '<br>';
            }
            echo '</div>';
        }
    }

    public function getUserName($userid = '') {
        $name = '';
        if ($userid) {
            $query_user = $this->db_connection->query("SELECT * FROM " . TABLE_SELLER_USERS . " WHERE id = '$userid'");
            $result_row = $query_user->fetch_object();
            $name = $result_row->first_name . ' ' . $result_row->last_name;
        }
        return $name;
    }
}
