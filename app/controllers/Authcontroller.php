<?php
session_start();
require_once APPROOT . '/models/client_login.php';
require_once APPROOT . '/models/staff_login.php';

class AuthController extends Controller
{
    private $clientModel;
    private $staffModel;

    public function __construct()
    {
        $db = new Database();

        if (!$db->conn) {
            die("Connection failed");
        }

        $this->clientModel = new Client($db->conn);
        $this->staffModel = new Staff($db->conn);
    }

<<<<<<< HEAD
    public function register() {
        $this->view("auth/register");
        
    }
}
/*<?php
class AuthController extends Controller {

    public function login() {
=======
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

            if ($this->clientModel->register($data)) {
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
>>>>>>> 1c913aaaa248cb8bdbf13aa5c6646aff1fac4701
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

<<<<<<< HEAD
            $user = $this->model('CaretakerModel')->login($email, $password);

            if ($user) {
                session_start();  // session start
                $_SESSION['caretaker_id'] = $user->id; // DB-la irundha ID
                $_SESSION['caretaker_name'] = $user->name; // optional
                header("Location: " . URLROOT . "/leaveCRUD/index"); // redirect
                exit;
            } else {
                $this->view('auth/login', ['error' => 'Invalid credentials']);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register() {
        $this->view("auth/register");
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: " . URLROOT . "/login");
        exit;
    }

}
*/
=======
            // Try normal client first
            $staffUser = $this->staffModel->login($email, $password);
            if ($staffUser) {
                $_SESSION['user'] = $staffUser;
                $_SESSION['role'] = $staffUser['role'];
                switch ($staffUser['role']) {
                    case 'caretaker':
                        header("Location: index.php?url=caretaker/ct_dashboard");
                        exit;
                    case 'admin':
                        header("Location: index.php?url=admin/ad_dashboard");
                        exit;
                    case 'hr':
                        header("Location: index.php?url=hr/hr_dashboard");
                        exit;
                }
            }
            // Then client
            $clientUser = $this->clientModel->login($email, $password);
            if ($clientUser) {
                $_SESSION['user'] = $clientUser;
                $_SESSION['role'] = 'client';
                header("Location:index.php?url=client/c_dashboard");
                exit;
            }

            // if none matched:
            $this->view('auth/login', ['error' => 'Invalid credentials']);
            return;
        }

        $this->view('auth/login');
    }

}
>>>>>>> 1c913aaaa248cb8bdbf13aa5c6646aff1fac4701
