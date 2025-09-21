<?php

class Home extends Controller {
    public function __construct() {
        // Constructor for the Home controller
    }

    public function index() {
        if (Session::has('user_id')) {
            header('location: index.php?url=dashboard');
        } else {
            // This will be the public landing page.
            echo '<h1>' . __('appName') . '</h1>';
            echo '<p>' . __('welcome') . '</p>';
            echo '<a href="index.php?url=users/login">' . __('login') . '</a>';
        }
    }
}
