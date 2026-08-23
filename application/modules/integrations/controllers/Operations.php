<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Operations extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('integrations/Integration_sync_runs_model');
    }

    public function index(): void
    {
        $this->layout->set([
            'runs' => $this->Integration_sync_runs_model->recent(),
        ]);

        $this->layout->buffer('content', 'integrations/operations');
        $this->layout->render();
    }
}
