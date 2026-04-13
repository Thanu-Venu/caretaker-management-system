<?php

class LandingController extends Controller
{
    public function home()
    {
        $landingModel = $this->model('LandingModel');
        $landingMetrics = $landingModel->getPublicMetrics();
        $this->view('landing/home', ['landingMetrics' => $landingMetrics]);
    }
}
