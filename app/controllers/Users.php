<?php

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
                    // Create Session
                    $this->createUserSession($loggedInUser);
                    // TODO: Handle "Remember Me"
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
        // Redirect to dashboard
        header('location: index.php?url=dashboard');
    }

    /**
     * Handles user logout.
     */
    public function logout() {
        Session::remove('user_id');
        Session::remove('username');
        Session::remove('user_role');
        Session::destroy();
        header('location: index.php?url=users/login');
    }

}
