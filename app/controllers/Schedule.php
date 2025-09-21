<?php

class Schedule extends Controller {
    private $proposalModel;

    public function __construct() {
        // All logged-in users can view the final schedule
        $this->authorize();
        $this->proposalModel = $this->model('Proposal');
    }

    /**
     * Display the final weekly schedule.
     * @param string $date A date within the week to display.
     */
    public function index($date = 'now') {
        $time = strtotime($date);
        $dayOfWeek = date('w', $time);
        $adjDayOfWeek = ($dayOfWeek + 1) % 7;
        $startOfWeekTime = strtotime("-$adjDayOfWeek days", $time);
        $weekStartDate = date('Y-m-d 00:00:00', $startOfWeekTime);
        $weekEndDate = date('Y-m-d 23:59:59', strtotime("+6 days", $startOfWeekTime));

        $approvedProposals = $this->proposalModel->getApprovedByWeek($weekStartDate, $weekEndDate);

        $data = [
            'week_start_date' => $weekStartDate,
            'week_end_date' => $weekEndDate,
            'proposals' => prepareProposalsForGrid($approvedProposals, $weekStartDate)
        ];

        $this->view('schedule/index', $data);
    }
}
