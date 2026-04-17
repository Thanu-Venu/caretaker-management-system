<?php

class LandingController extends Controller
{
    public function home()
    {
        // Redirect logged-in users to their appropriate dashboard
        if (AuthSession::isLoggedIn()) {
            $role = AuthSession::role();
            if ($role === 'admin') {
                header("Location: " . URLROOT . "/public/?url=admin/ad_dashboard");
                exit;
            } elseif ($role === 'manager') {
                header("Location: " . URLROOT . "/public/?url=hr/hr_dashboard");
                exit;
            } elseif ($role === 'caretaker') {
                header("Location: " . URLROOT . "/public/?url=caretaker/ct_dashboard");
                exit;
            } elseif ($role === 'client') {
                header("Location: " . URLROOT . "/public/?url=client/c_dashboard");
                exit;
            }
        }
        
        $landingModel = $this->model('LandingModel');
        $landingMetrics = $landingModel->getPublicMetrics();
        $this->view('landing/home', ['landingMetrics' => $landingMetrics]);
    }
}
