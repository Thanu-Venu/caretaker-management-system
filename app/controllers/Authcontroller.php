<?php

require_once APPROOT . '/models/client_login.php';
require_once APPROOT . '/models/caretaker_login.php';
require_once APPROOT . '/models/user_login.php';

class AuthController extends Controller
{
    private $clientModel;
    private $caretakerModel;
    private $userModel;

    public function __construct()
    {
        $db = new Database();

        if (!$db->conn) {
            die("Connection failed");
        }

        $this->clientModel = new Client($db->conn);
        $this->caretakerModel = new Caretaker($db->conn);
        $this->userModel = new User($db->conn);
    }

    // Client Signup
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($password !== $confirmPassword) {
                $data = [
                    'firstName' => $_POST['firstName'] ?? '',
                    'lastName' => $_POST['lastName'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'error' => 'Passwords do not match'
                ];
                $this->view("auth/register", $data);
                return;
            }

            $data = [
                'name' => $_POST['firstName'] . ' ' . $_POST['lastName'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'password' => $password
            ];
            if ($this->clientModel->findUserByEmail($data['email'])) {
                $this->view("auth/register", [
                    'error' => 'This email is already registered. Please log in instead.'
                ]);
                return;
            }
            if ($this->clientModel->register($data)) {
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header("Location: index.php?url=auth/login");
                exit;
            } else {
                $this->view("auth/register", ['error' => 'Registration failed']);
            }
        } else {
            $this->view("auth/register");
        }
    }

    // Login for all types
    public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];


        // 1️⃣ Check in users table (admin & HR)
        $user = $this->userModel->login($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $user['role']; // 'admin' or 'hr'
            
            if ($user['role'] === 'admin') {
                header("Location: index.php?url=admin/ad_dashboard"); exit;
            } else {
                header("Location: index.php?url=hr/hr_dashboard"); exit;
            }
        }

        // 2️⃣ Check in caretakers table
        $caretaker = $this->caretakerModel->login($email, $password);
        if ($caretaker) {
            $_SESSION['user'] = $caretaker;
            $_SESSION['role'] = 'caretaker';
            header("Location: index.php?url=caretaker/ct_dashboard"); exit;
        }

        // 3️⃣ Check in clients table
        $client = $this->clientModel->login($email, $password);
        if ($client) {
            $_SESSION['user'] = $client;
            $_SESSION['role'] = 'client';
            header("Location: index.php?url=client/c_dashboard"); exit;
        }

        // If none matched
        $this->view('auth/login', ['error' => 'Invalid credentials']);
    } else {
        $this->view('auth/login');
    }
}

     // Logout
    public function logout() {
        session_start();              // Start session if not already started
        $_SESSION = [];               // Clear all session variables
        session_unset();              // Unset session variables
        session_destroy();            // Destroy the session
        setcookie(session_name(), '', time() - 3600); // Clear session cookie

        // Redirect to login page
        header("Location: index.php?url=auth/login");
        exit;
    }


}