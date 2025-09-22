<?php
/**
 * Base Controller
 * This loads the models and views
 */
if (!class_exists('Controller')) {
class Controller {
    /**
     * Loads a model file.
     * @param string $model The model name.
     * @return object The model object.
     */
    public function model($model) {
        // Require model file
        require_once __DIR__ . '/../models/' . $model . '.php';
        // Instantiate model
        return new $model();
    }

    /**
     * Loads a view file.
     * @param string $view The view file to load (e.g., 'users/login').
     * @param array $data Data to pass to the view.
     */
    public function view($view, $data = []) {
        // Make data available to the view
        extract($data);

        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once __DIR__ . '/../views/layouts/header.php';
            require_once $viewFile;
            require_once __DIR__ . '/../views/layouts/footer.php';
        } else {
            // A real app would have a proper error page.
            die('View does not exist: ' . htmlspecialchars($view));
        }
    }

    /**
     * Authorizes a user based on roles.
     * Redirects to login if not logged in, or to home if not authorized.
     * @param array $allowedRoles An array of roles that are allowed. If empty, only checks for login.
     */
    protected function authorize($allowedRoles = []) {
        if (!Session::has('user_id')) {
            // Not logged in, redirect to login
            header('location: index.php?url=users/login');
            exit();
        }

        if (!empty($allowedRoles)) {
            $userRole = Session::get('user_role');
            if (!in_array($userRole, $allowedRoles)) {
                // Role not allowed, redirect to home
                Session::flash('error', 'شما اجازه دسترسی به این صفحه را ندارید.');
                header('location: index.php');
                exit();
            }
        }
    }
}
}
