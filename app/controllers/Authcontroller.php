<?php

require_once APPROOT . '/models/AccountModel.php';
require_once APPROOT . '/models/ClientModel.php';
require_once APPROOT . '/models/CaretakerModel.php';
require_once APPROOT . '/models/UserModel.php';

class AuthController extends Controller
{
    private $accountModel;
    private $clientModel;
    private $caretakerModel;
    private $userModel;

    public function __construct()
    {
        $this->accountModel = $this->model('AccountModel');
        $this->clientModel = $this->model('ClientModel');
        $this->caretakerModel = $this->model('CaretakerModel');
        $this->userModel = $this->model('UserModel');
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

            if ($this->accountModel->isAccountsReady() && $this->accountModel->findByEmail($data['email'])) {
                $this->view("auth/register", [
                    'error' => 'This email is already registered. Please log in instead.'
                ]);
                return;
            }

            $registered = false;
            if ($this->accountModel->isAccountsReady()) {
                $registered = $this->accountModel->createClientAccountAndProfile($data);
            } else {
                // Backward-compatible path if migrations were not executed yet.
                $registered = $this->clientModel->register($data);
            }

            if ($registered) {
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
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

<<<<<<< HEAD

        // 1️⃣ Check in users table (admin & HR)
        $user = $this->userModel->login($email, $password);
        if ($user) {
            $_SESSION['user']       = $user;
            $_SESSION['role']       = $user['role']; // 'admin' or 'hr'
            $_SESSION['profile_id'] = (int)($user['id'] ?? 0);
            $_SESSION['account_id'] = (int)($user['account_id'] ?? ($user['id'] ?? 0));
            $_SESSION['name']       = $user['name'] ?? ($user['username'] ?? '');
            $_SESSION['email']      = $user['email'] ?? $email;
            $_SESSION['logged_in']  = true;

            if ($user['role'] === 'admin') {
                header("Location: index.php?url=admin/ad_dashboard"); exit;
            } else {
                header("Location: index.php?url=hr/hr_dashboard"); exit;
=======
            if ($email === '' || $password === '') {
                $this->view('auth/login', ['error' => 'Please provide both email and password']);
                return;
>>>>>>> ed6b121a36694e713c70d196d3713eb4c3ea2e14
            }

            if ($this->accountModel->isAccountsReady()) {
                $account = $this->accountModel->authenticate($email, $password);
                if (!$account) {
                    $this->view('auth/login', ['error' => 'Invalid credentials']);
                    return;
                }

                $role = AccountModel::normalizeRole($account['role'] ?? '');
                $profile = $this->accountModel->getProfileByAccount((int)$account['id'], $role);
                if (!$profile) {
                    $this->view('auth/login', ['error' => 'Profile not found for this account']);
                    return;
                }

                session_regenerate_id(true);
                AuthSession::setAuthenticated($account, $profile);
                $this->redirectByRole($role);
                return;
            }

            // Legacy fallback: old three-table authentication
            $user = $this->userModel->login($email, $password);
            if ($user) {
                $legacyRole = (string)($user['role'] ?? '');
                $role = AccountModel::normalizeRole($legacyRole);

                $_SESSION['user'] = $user;
                $_SESSION['role'] = $role;
                $_SESSION['legacy_role'] = $legacyRole;
                $_SESSION['name'] = $user['name'] ?? $user['username'] ?? '';
                $_SESSION['email'] = $user['email'] ?? '';
                $_SESSION['profile_id'] = (int)($user['id'] ?? 0);
                $_SESSION['account_id'] = (int)($user['account_id'] ?? 0);
                $_SESSION['logged_in'] = true;

                $this->redirectByRole($role);
                return;
            }

            $caretaker = $this->caretakerModel->login($email, $password);
            if ($caretaker) {
                $_SESSION['user'] = $caretaker;
                $_SESSION['role'] = 'caretaker';
                $_SESSION['legacy_role'] = 'caretaker';
                $_SESSION['name'] = $caretaker['name'] ?? '';
                $_SESSION['email'] = $caretaker['email'] ?? '';
                $_SESSION['profile_id'] = (int)($caretaker['id'] ?? 0);
                $_SESSION['account_id'] = (int)($caretaker['account_id'] ?? 0);
                $_SESSION['logged_in'] = true;
                header("Location: index.php?url=caretaker/ct_dashboard");
                exit;
            }

            $client = $this->clientModel->login($email, $password);
            if ($client) {
                $_SESSION['user'] = $client;
                $_SESSION['role'] = 'client';
                $_SESSION['legacy_role'] = 'client';
                $_SESSION['name'] = $client['name'] ?? '';
                $_SESSION['email'] = $client['email'] ?? '';
                $_SESSION['profile_id'] = (int)($client['id'] ?? 0);
                $_SESSION['account_id'] = (int)($client['account_id'] ?? 0);
                $_SESSION['logged_in'] = true;
                header("Location: index.php?url=client/c_dashboard");
                exit;
            }

            $this->view('auth/login', ['error' => 'Invalid credentials']);
            return;
        }

<<<<<<< HEAD
        // 2️⃣ Check in caretakers table
        $caretaker = $this->caretakerModel->login($email, $password);
        if ($caretaker) {
            $_SESSION['user']       = $caretaker;
            $_SESSION['role']       = 'caretaker';
            $_SESSION['profile_id'] = (int)($caretaker['id'] ?? 0);
            $_SESSION['account_id'] = (int)($caretaker['account_id'] ?? ($caretaker['id'] ?? 0));
            $_SESSION['name']       = $caretaker['name'] ?? '';
            $_SESSION['email']      = $caretaker['email'] ?? $email;
            $_SESSION['logged_in']  = true;
            header("Location: index.php?url=caretaker/ct_dashboard"); exit;
        }

        // 3️⃣ Check in clients table
        $client = $this->clientModel->login($email, $password);
        if ($client) {
            $_SESSION['user']       = $client;
            $_SESSION['role']       = 'client';
            $_SESSION['profile_id'] = (int)($client['id'] ?? 0);
            $_SESSION['account_id'] = (int)($client['account_id'] ?? ($client['id'] ?? 0));
            $_SESSION['name']       = $client['name'] ?? '';
            $_SESSION['email']      = $client['email'] ?? $email;
            $_SESSION['logged_in']  = true;
            header("Location: index.php?url=client/c_dashboard"); exit;
        }

        // If none matched
        $this->view('auth/login', ['error' => 'Invalid credentials']);
    } else {
=======
>>>>>>> ed6b121a36694e713c70d196d3713eb4c3ea2e14
        $this->view('auth/login');
    }

    private function redirectByRole(string $role): void
    {
        if ($role === 'admin') {
            header("Location: index.php?url=admin/ad_dashboard");
            exit;
        }

        if ($role === 'manager') {
            header("Location: index.php?url=hr/hr_dashboard");
            exit;
        }

        if ($role === 'caretaker') {
            header("Location: index.php?url=caretaker/ct_dashboard");
            exit;
        }

        header("Location: index.php?url=client/c_dashboard");
        exit;
    }

    // Logout
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600);

        header("Location: index.php?url=auth/login");
        exit;
    }
}
