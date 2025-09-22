<?php

if (!class_exists('Dashboard')) {
class Dashboard extends Controller {
    private $proposalModel;
    private $macroPlanModel;
    private $semesterModel;

    public function __construct() {
        // This dashboard is for the 'user' role only.
        $this->authorize(['user']);
        $this->proposalModel = $this->model('Proposal');
        $this->macroPlanModel = $this->model('MacroPlan');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * The main dashboard/planning view.
     * @param string $date A date within the week to display, e.g., '2025-10-27'
     */
    public function index($date = 'now') {
        $time = strtotime($date);

        // Day of week (0 for Sunday, 6 for Saturday). We want Saturday to be the start.
        $dayOfWeek = date('w', $time);
        // Adjust so Saturday (6) becomes day 0.
        $adjDayOfWeek = ($dayOfWeek + 1) % 7;

        $startOfWeekTime = strtotime("-$adjDayOfWeek days", $time);
        $endOfWeekTime = strtotime("+6 days", $startOfWeekTime);

        $weekStartDate = date('Y-m-d 00:00:00', $startOfWeekTime);
        $weekEndDate = date('Y-m-d 23:59:59', $endOfWeekTime);

        $currentSemester = $this->semesterModel->getCurrent();
        if (!$currentSemester) {
            die('No active semester found.');
        }

        $approvedProposals = $this->proposalModel->getApprovedByWeek($weekStartDate, $weekEndDate);
        $pendingProposals = $this->proposalModel->getPendingForUserByWeek(Session::get('user_id'), $weekStartDate, $weekEndDate);
        $macroPlanEvents = $this->macroPlanModel->getBySemester($currentSemester->id);

        $data = [
            'week_start_date' => $weekStartDate,
            'week_end_date' => $weekEndDate,
            'approved_proposals' => prepareProposalsForGrid($approvedProposals, $weekStartDate),
            'pending_proposals' => prepareProposalsForGrid($pendingProposals, $weekStartDate),
            'macro_plan_events' => $macroPlanEvents,
            'current_semester' => $currentSemester
        ];

        $this->view('dashboard/index', $data);
    }
}
}
