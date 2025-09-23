<?php

if (!class_exists('Macroplan')) {
class Macroplan extends Controller {
    private $macroPlanModel;
    private $semesterModel;

    public function __construct() {
        // Authorize access for Director and Assessor
        $this->authorize(['director', 'assessor']);

        $this->macroPlanModel = $this->model('MacroPlanModel');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * List all macro plan events for the current semester, with an option to filter by month.
     */
    public function index() {
        $currentSemester = $this->semesterModel->getCurrent();
        if (!$currentSemester) {
            die('No active semester found.');
        }

        // Get selected month from query string, e.g., "2025-10"
        $selectedMonth = $_GET['month'] ?? null;

        // Generate a list of months for the dropdown
        $start = new DateTime($currentSemester->start_date);
        $end = new DateTime($currentSemester->end_date);
        $interval = new DateInterval('P1M');
        $period = new DatePeriod($start, $interval, $end->modify('+1 month'));
        $months = [];
        foreach ($period as $dt) {
            $months[] = [
                'value' => $dt->format('Y-m'),
                'name' => jDateTime::date('F Y', $dt->getTimestamp())
            ];
        }

        // Fetch events, filtered by month if selected
        $events = $this->macroPlanModel->getEvents($currentSemester->id, $selectedMonth);

        $data = [
            'events' => $events,
            'semester' => $currentSemester,
            'months' => $months,
            'selected_month' => $selectedMonth
        ];

        $this->view('macroplan/index', $data);
    }

    /**
     * Add a new macro plan event.
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $currentSemester = $this->semesterModel->getCurrent();
            if (!$currentSemester) die('No active semester.');

            $gregorian_date = jalaliToGregorian(trim($_POST['event_date']), 'Y-m-d');

            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'semester_id' => $currentSemester->id,
                'event_date' => $gregorian_date
            ];

            if (!empty($data['title']) && !empty($data['event_date'])) {
                $this->macroPlanModel->add($data);
                header('location: index.php?url=macroplan');
            } else {
                // Handle error
                $this->view('macroplan/add', ['error' => 'Title and date are required']);
            }
        } else {
            $this->view('macroplan/add');
        }
    }

    /**
     * Edit a macro plan event.
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $gregorian_date = jalaliToGregorian(trim($_POST['event_date']), 'Y-m-d');
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'event_date' => $gregorian_date
            ];
            if (!empty($data['title']) && !empty($data['event_date'])) {
                $this->macroPlanModel->update($data);
                header('location: index.php?url=macroplan');
            } else {
                $event = $this->macroPlanModel->getById($id);
                $data['event_date_jalali'] = jDateTime::date('Y/m/d', strtotime($event->event_date));
                $this->view('macroplan/edit', $data);
            }
        } else {
            $event = $this->macroPlanModel->getById($id);
            if (!$event) {
                header('location: index.php?url=macroplan');
                exit();
            }
            $data = [
                'id' => $id,
                'title' => $event->title,
                'description' => $event->description,
                'event_date_jalali' => jDateTime::date('Y/m/d', strtotime($event->event_date))
            ];
            $this->view('macroplan/edit', $data);
        }
    }

    /**
     * Delete a macro plan event.
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->macroPlanModel->delete($id);
            header('location: index.php?url=macroplan');
        } else {
            header('location: index.php?url=macroplan');
        }
    }
}
}
