<?php

if (!class_exists('Director')) {
class Director extends Controller {
    private $proposalModel;
    private $semesterModel;

    public function __construct() {
        // Authorize access for Director ONLY
        $this->authorize(['director']);

        $this->proposalModel = $this->model('Proposal');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * The main Director's dashboard for approving proposals.
     * @param string $date A date within the week to display.
     */
    public function index($date = 'now') {
        $time = strtotime($date);
        $dayOfWeek = date('w', $time);
        $adjDayOfWeek = ($dayOfWeek + 1) % 7;
        $startOfWeekTime = strtotime("-$adjDayOfWeek days", $time);
        $weekStartDate = date('Y-m-d 00:00:00', $startOfWeekTime);
        $weekEndDate = date('Y-m-d 23:59:59', strtotime("+6 days", $startOfWeekTime));

        $pendingProposals = $this->proposalModel->getPendingByWeek($weekStartDate, $weekEndDate);
        $approvedProposals = $this->proposalModel->getApprovedByWeek($weekStartDate, $weekEndDate);

        // Enhance proposals with details for conflict detection
        $allProposals = array_merge($pendingProposals, $approvedProposals);
        foreach ($allProposals as $p) {
            $details = $this->proposalModel->getProposalDetails($p->id);
            $p->audiences = $details->audiences;
            $p->organizers = $details->organizers;
        }

        $pendingProposalsWithConflicts = $this->detectConflicts($pendingProposals, $allProposals);

        $data = [
            'week_start_date' => $weekStartDate,
            'pending_proposals' => $pendingProposalsWithConflicts
        ];

        $this->view('director/dashboard', $data);
    }

    private function detectConflicts($pendingProposals, $allProposals) {
        foreach ($pendingProposals as $pending) {
            $pending->conflicts = [];
            $startA = strtotime($pending->event_datetime);
            $endA = !empty($pending->event_end_datetime) ? strtotime($pending->event_end_datetime) : $startA;

            foreach ($allProposals as $other) {
                if ($pending->id == $other->id) continue;

                $startB = strtotime($other->event_datetime);
                $endB = !empty($other->event_end_datetime) ? strtotime($other->event_end_datetime) : $startB;

                // Check for time range overlap
                if ($startA < $endB && $endA > $startB) {
                    $audienceConflict = !empty(array_intersect($pending->audiences, $other->audiences));
                    $organizerConflict = !empty(array_intersect($pending->organizers, $other->organizers));

                    if ($audienceConflict || $organizerConflict) {
                        $pending->conflicts[] = [
                            'proposal_id' => $other->id,
                            'title' => $other->title,
                            'type' => $other->status
                        ];
                    }
                }
            }
        }
        return $pendingProposals;
    }

    /**
     * Approve a proposal.
     */
    public function approve($proposalId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->proposalModel->approve($proposalId);
        }
        // Redirect back to the director dashboard
        header('location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?url=director'));
        exit();
    }

    /**
     * Revoke approval for a proposal.
     */
    public function revoke($proposalId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->proposalModel->revoke($proposalId);
        }
        // Redirect back, probably from a different view, but this works for now
        header('location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?url=director'));
        exit();
    }
}
}
