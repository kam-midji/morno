<?php

if (!class_exists('Users')) {
class Users extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    /**
     * Handles user login.
     * GET: Shows the login form.
     * POST: Processes login attempt.
     */
    public function login() {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'remember_me' => isset($_POST['remember_me']),
                'username_err' => '',
                'password_err' => '',
            ];

            // Validate Username
            if (empty($data['username'])) {
                $data['username_err'] = 'لطفا نام کاربری را وارد کنید.';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'لطفا رمز عبور را وارد کنید.';
            }

            // Check for user
            if(empty($data['username_err']) && empty($data['password_err'])){
                $loggedInUser = $this->userModel->findByUsername($data['username']);

                if($loggedInUser && password_verify($data['password'], $loggedInUser->password)){
                    // Check if "Remember Me" is checked
                    if ($data['remember_me']) {
                        $token = $this->userModel->createRememberMeToken($loggedInUser->id);
                        if ($token) {
                            // Set a cookie with selector and validator
                            $cookieValue = $token['selector'] . ':' . $token['validator'];
                            // Set cookie for 30 days, httponly for security
                            setcookie('remember_me', $cookieValue, time() + (86400 * 30), '/', '', false, true);
                        }
                    }
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'نام کاربری یا رمز عبور اشتباه است.';
                    $this->view('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }

        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'remember_me' => false,
                'username_err' => '',
                'password_err' => '',
            ];
            // Load view
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user) {
        Session::set('user_id', $user->id);
        Session::set('username', $user->username);
        Session::set('user_role', $user->role);

        // Redirect based on role
        switch ($user->role) {
            case 'admin':
                header('location: index.php?url=admin');
                break;
            case 'director':
                header('location: index.php?url=director');
                break;
            case 'assessor':
                header('location: index.php?url=assessor');
                break;
            case 'user':
            default:
                header('location: index.php?url=dashboard');
                break;
        }
    }

    /**
     * Handles user logout.
     */
    public function logout() {
        // Handle "Remember Me" cookie
        if (isset($_COOKIE['remember_me'])) {
            // Delete token from database
            if (Session::has('user_id')) {
                $this->userModel->deleteToken(Session::get('user_id'));
            }
            // Unset the cookie
            setcookie('remember_me', '', time() - 3600, '/');
        }

        Session::remove('user_id');
        Session::remove('username');
        Session::remove('user_role');
        Session::destroy();
        header('location: index.php?url=users/login');
    }

}
}
