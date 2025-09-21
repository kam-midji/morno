<?php

class Admin extends Controller {
    private $userModel;
    private $audienceModel;
    private $organizerModel;
    private $semesterModel;

    public function __construct() {
        // Authorize admin access for all methods in this controller
        $this->authorize(['admin']);
        $this->userModel = $this->model('User');
        $this->audienceModel = $this->model('Audience');
        $this->organizerModel = $this->model('Organizer');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * Default method, shows the admin dashboard.
     */
    public function index() {
        $data = [
            'title' => 'داشبورد ادمین'
        ];
        $this->view('admin/index', $data);
    }

    /**
     * User management page.
     */
    public function users() {
        $users = $this->userModel->getUsers();
        $data = [
            'users' => $users
        ];
        $this->view('admin/users/index', $data);
    }

    /**
     * Add a new user.
     */
    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'role' => $_POST['role'],
                'errors' => []
            ];

            // Validation
            if (empty($data['full_name'])) {
                $data['errors']['full_name'] = 'نام کامل الزامی است.';
            }
            if (empty($data['username'])) {
                $data['errors']['username'] = 'نام کاربری الزامی است.';
            } elseif ($this->userModel->usernameExists($data['username'])) {
                $data['errors']['username'] = 'این نام کاربری قبلا استفاده شده است.';
            }
            if (empty($data['password'])) {
                $data['errors']['password'] = 'رمز عبور الزامی است.';
            } elseif (strlen($data['password']) < 6) {
                $data['errors']['password'] = 'رمز عبور باید حداقل 6 کاراکتر باشد.';
            }
            $allowedRoles = ['admin', 'director', 'assessor', 'user'];
            if (!in_array($data['role'], $allowedRoles)) {
                $data['role'] = 'user'; // Default to user if invalid role is submitted
            }

            // If no errors
            if (empty($data['errors'])) {
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($this->userModel->register($data)) {
                    // Redirect to user list
                    header('location: index.php?url=admin/users');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $this->view('admin/users/add', $data);
            }

        } else {
            // Show empty form
            $data = [
                'full_name' => '',
                'username' => '',
                'password' => '',
                'role' => 'user',
                'errors' => []
            ];
            $this->view('admin/users/add', $data);
        }
    }

    /**
     * Edit an existing user.
     */
    public function editUser($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'role' => $_POST['role'],
                'errors' => []
            ];

            // Validation
            if (empty($data['full_name'])) {
                $data['errors']['full_name'] = 'نام کامل الزامی است.';
            }
            if (empty($data['username'])) {
                $data['errors']['username'] = 'نام کاربری الزامی است.';
            } else {
                $user = $this->userModel->findByUsername($data['username']);
                if ($user && $user->id != $id) {
                    $data['errors']['username'] = 'این نام کاربری قبلا استفاده شده است.';
                }
            }
            if (!empty($data['password']) && strlen($data['password']) < 6) {
                $data['errors']['password'] = 'رمز عبور جدید باید حداقل 6 کاراکتر باشد.';
            }

            // If no errors
            if (empty($data['errors'])) {
                // Hash password if it was changed
                if (!empty($data['password'])) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }

                if ($this->userModel->updateUser($data)) {
                    // Redirect to user list
                    header('location: index.php?url=admin/users');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $this->view('admin/users/edit', $data);
            }
        } else {
            // Get existing user from model
            $user = $this->userModel->findById($id);

            // Check for owner
            // A simple check to prevent editing themselves through this interface maybe?
            // For now, we assume admin can edit anyone.

            $data = [
                'id' => $id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'role' => $user->role,
                'errors' => []
            ];
            $this->view('admin/users/edit', $data);
        }
    }

    /**
     * Delete a user.
     */
    public function deleteUser($id) {
        // Prevent admin from deleting themselves
        if ($id == Session::get('user_id')) {
            // Maybe set a flash message here
            header('location: index.php?url=admin/users');
            exit();
        }

        if ($this->userModel->deleteUser($id)) {
            // Redirect to user list
            header('location: index.php?url=admin/users');
        } else {
            die('Something went wrong');
        }
    }

    // --- Audience Management ---
    public function audiences() {
        $audiences = $this->audienceModel->getAll();
        $data = [
            'audiences' => $audiences
        ];
        $this->view('admin/audiences/index', $data);
    }

    public function addAudience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $this->view('admin/audiences/add', ['name' => $name, 'error' => 'نام مخاطب نمی‌تواند خالی باشد.']);
            } else {
                $this->audienceModel->add(['name' => $name]);
                header('location: index.php?url=admin/audiences');
            }
        } else {
            $this->view('admin/audiences/add', ['name' => '', 'error' => '']);
        }
    }

    public function editAudience($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $this->view('admin/audiences/edit', ['id' => $id, 'name' => $name, 'error' => 'نام مخاطب نمی‌تواند خالی باشد.']);
            } else {
                $this->audienceModel->update(['id' => $id, 'name' => $name]);
                header('location: index.php?url=admin/audiences');
            }
        } else {
            $audience = $this->audienceModel->getById($id);
            $this->view('admin/audiences/edit', ['id' => $id, 'name' => $audience->name, 'error' => '']);
        }
    }

    public function deleteAudience($id) {
        $this->audienceModel->delete($id);
        header('location: index.php?url=admin/audiences');
    }

    // --- Organizer Management ---
    public function organizers() {
        $organizers = $this->organizerModel->getAll();
        $data = [
            'organizers' => $organizers
        ];
        $this->view('admin/organizers/index', $data);
    }

    public function addOrganizer() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $this->view('admin/organizers/add', ['name' => $name, 'error' => 'نام برگزارکننده نمی‌تواند خالی باشد.']);
            } else {
                $this->organizerModel->add(['name' => $name]);
                header('location: index.php?url=admin/organizers');
            }
        } else {
            $this->view('admin/organizers/add', ['name' => '', 'error' => '']);
        }
    }

    public function editOrganizer($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $this->view('admin/organizers/edit', ['id' => $id, 'name' => $name, 'error' => 'نام برگزارکننده نمی‌تواند خالی باشد.']);
            } else {
                $this->organizerModel->update(['id' => $id, 'name' => $name]);
                header('location: index.php?url=admin/organizers');
            }
        } else {
            $organizer = $this->organizerModel->getById($id);
            $this->view('admin/organizers/edit', ['id' => $id, 'name' => $organizer->name, 'error' => '']);
        }
    }

    public function deleteOrganizer($id) {
        $this->organizerModel->delete($id);
        header('location: index.php?url=admin/organizers');
    }

    // --- Semester Management ---
    public function semesters() {
        $semesters = $this->semesterModel->getAll();
        $data = [
            'semesters' => $semesters
        ];
        $this->view('admin/semesters/index', $data);
    }

    public function addSemester() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'name' => trim($_POST['name']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'errors' => []
            ];

            if (empty($data['name'])) $data['errors']['name'] = 'نام ترم الزامی است.';
            if (empty($data['start_date'])) $data['errors']['start_date'] = 'تاریخ شروع الزامی است.';
            if (empty($data['end_date'])) $data['errors']['end_date'] = 'تاریخ پایان الزامی است.';

            if (empty($data['errors'])) {
                if ($this->semesterModel->add($data)) {
                    header('location: index.php?url=admin/semesters');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('admin/semesters/add', $data);
            }
        } else {
            $this->view('admin/semesters/add', ['name' => '', 'start_date' => '', 'end_date' => '', 'errors' => []]);
        }
    }

    public function editSemester($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'errors' => []
            ];

            if (empty($data['name'])) $data['errors']['name'] = 'نام ترم الزامی است.';
            if (empty($data['start_date'])) $data['errors']['start_date'] = 'تاریخ شروع الزامی است.';
            if (empty($data['end_date'])) $data['errors']['end_date'] = 'تاریخ پایان الزامی است.';

            if (empty($data['errors'])) {
                if ($this->semesterModel->update($data)) {
                    header('location: index.php?url=admin/semesters');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('admin/semesters/edit', $data);
            }
        } else {
            $semester = $this->semesterModel->getById($id);
            $this->view('admin/semesters/edit', ['id' => $id, 'name' => $semester->name, 'start_date' => $semester->start_date, 'end_date' => $semester->end_date, 'errors' => []]);
        }
    }

    public function archiveSemester($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->semesterModel->archive($id);
            header('location: index.php?url=admin/semesters');
        } else {
            // Redirect if not a POST request
            header('location: index.php?url=admin/semesters');
        }
    }
}
