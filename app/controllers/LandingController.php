<?php

class LandingController extends Controller
{
    public function home()
    {
        $landingModel = $this->model('LandingModel');
        $landingMetrics = $landingModel->getPublicMetrics();
        $landingTestimonials = $landingModel->getPublicTestimonials(12);
        $this->view('landing/home', [
            'landingMetrics' => $landingMetrics,
            'landingTestimonials' => $landingTestimonials,
        ]);
    }
}

















