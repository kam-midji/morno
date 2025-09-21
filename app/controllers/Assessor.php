<?php

class Assessor extends Controller {
    private $proposalModel;

    public function __construct() {
        // Authorize access for Assessor and Admin
        $this->authorize(['assessor', 'admin']);
        $this->proposalModel = $this->model('Proposal');
    }

    /**
     * The main Assessor's dashboard, showing all proposals.
     */
    public function index() {
        $allProposals = $this->proposalModel->getAllProposals();

        $data = [
            'proposals' => $allProposals,
            'title' => 'نمای ارزیاب - تمام پیشنهادات'
        ];

        $this->view('assessor/index', $data);
    }
}
