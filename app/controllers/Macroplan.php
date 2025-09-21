<?php

class Macroplan extends Controller {
    private $macroPlanModel;
    private $semesterModel;

    public function __construct() {
        // Authorize access for Director and Assessor
        $this->authorize(['director', 'assessor']);

        $this->macroPlanModel = $this->model('MacroPlan');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * List all macro plan events for the current semester.
     */
    public function index() {
        $currentSemester = $this->semesterModel->getCurrent();
        if (!$currentSemester) {
            die('No active semester found.');
        }

        $events = $this->macroPlanModel->getBySemester($currentSemester->id);

        $data = [
            'events' => $events,
            'semester' => $currentSemester
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

            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'semester_id' => $currentSemester->id
            ];

            if (!empty($data['title'])) {
                $this->macroPlanModel->add($data);
                header('location: index.php?url=macroplan');
            } else {
                // Handle error
                $this->view('macroplan/add', ['error' => 'Title is required']);
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
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description'])
            ];
            if (!empty($data['title'])) {
                $this->macroPlanModel->update($data);
                header('location: index.php?url=macroplan');
            } else {
                $data['event'] = $this->macroPlanModel->getById($id);
                $this->view('macroplan/edit', $data);
            }
        } else {
            $event = $this->macroPlanModel->getById($id);
            $data = [
                'id' => $id,
                'title' => $event->title,
                'description' => $event->description
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
