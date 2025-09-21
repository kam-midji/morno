<?php

class Proposals extends Controller {
    private $proposalModel;
    private $audienceModel;
    private $organizerModel;
    private $semesterModel;

    public function __construct() {
        // Only 'user' role can create/edit/delete proposals.
        // We will check for general login here, and per-method for specific roles/ownership.
        $this->authorize();

        $this->proposalModel = $this->model('Proposal');
        $this->audienceModel = $this->model('Audience');
        $this->organizerModel = $this->model('Organizer');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * Shows the form to add a new proposal, and handles form submission.
     */
    public function add() {
        // Only users can add proposals
        $this->authorize(['user']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $gregorian_datetime = jalaliToGregorian($_POST['event_datetime']);

            $data = [
                'title' => trim($_POST['title']),
                'event_datetime_jalali' => $_POST['event_datetime'],
                'event_datetime' => $gregorian_datetime,
                'selected_audiences' => $_POST['audiences'] ?? [],
                'selected_organizers' => $_POST['organizers'] ?? [],
                'objective' => trim($_POST['objective']),
                'priority' => $_POST['priority'],
                'user_id' => Session::get('user_id'),
                'semester_id' => $_POST['current_semester_id'],
                'errors' => []
            ];

            // Basic Validation
            if(empty($data['title'])) $data['errors']['title'] = 'عنوان الزامی است.';
            if($data['event_datetime'] === false) $data['errors']['event_datetime'] = 'فرمت تاریخ و زمان نامعتبر است.';
            if(empty($data['audiences'])) $data['errors']['audiences'] = 'حداقل یک مخاطب انتخاب کنید.';
            if(empty($data['organizers'])) $data['errors']['organizers'] = 'حداقل یک برگزارکننده انتخاب کنید.';
            if(empty($data['objective'])) $data['errors']['objective'] = 'هدف برنامه الزامی است.';

            if(empty($data['errors'])){
                if($this->proposalModel->add($data)){
                    Session::flash('success', 'پیشنهاد شما با موفقیت ثبت شد.');
                    header('location: index.php?url=home/dashboard');
                } else {
                    die('Something went wrong while adding proposal.');
                }
            } else {
                // Reload form with errors and data
                $data['audiences'] = $this->audienceModel->getAll();
                $data['organizers'] = $this->organizerModel->getAll();
                $this->view('proposals/add', $data);
            }

        } else {
            // GET request: Show the form
            $audiences = $this->audienceModel->getAll();
            $organizers = $this->organizerModel->getAll();
            $currentSemester = $this->semesterModel->getCurrent();

            if (!$currentSemester) {
                // Handle case where no active semester is found
                die('No active semester found. Please contact an administrator.');
            }

            $data = [
                'audiences' => $audiences,
                'organizers' => $organizers,
                'current_semester_id' => $currentSemester->id,
                'errors' => []
            ];

            $this->view('proposals/add', $data);
        }
    }

    public function edit($id) {
        $proposal = $this->proposalModel->getProposalDetails($id);

        if (!$proposal || $proposal->user_id != Session::get('user_id') || $proposal->status != 'pending') {
            header('location: index.php?url=dashboard');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $gregorian_datetime = jalaliToGregorian($_POST['event_datetime']);

            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'event_datetime' => $gregorian_datetime,
                'audiences' => $_POST['audiences'] ?? [],
                'organizers' => $_POST['organizers'] ?? [],
                'objective' => trim($_POST['objective']),
                'priority' => $_POST['priority'],
            ];

            if ($this->proposalModel->update($data)) {
                header('location: index.php?url=dashboard');
            } else {
                die('Something went wrong');
            }
        } else {
            $data = [
                'id' => $id,
                'title' => $proposal->title,
                'objective' => $proposal->objective,
                'priority' => $proposal->priority,
                'event_datetime_jalali' => jDateTime::date('Y/m/d H:i:s', strtotime($proposal->event_datetime)),
                'all_audiences' => $this->audienceModel->getAll(),
                'selected_audiences' => $proposal->audiences,
                'all_organizers' => $this->organizerModel->getAll(),
                'selected_organizers' => $proposal->organizers
            ];
            $this->view('proposals/edit', $data);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $proposal = $this->proposalModel->getById($id);

            // Check for owner and status
            if ($proposal->user_id != Session::get('user_id') || $proposal->status != 'pending') {
                header('location: index.php?url=dashboard');
                exit();
            }

            if ($this->proposalModel->delete($id)) { // We need a delete method
                header('location: index.php?url=dashboard');
            } else {
                die('Something went wrong');
            }
        } else {
            header('location: index.php?url=dashboard');
        }
    }
}
