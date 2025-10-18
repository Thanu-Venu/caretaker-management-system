<?php
class AuthController extends Controller {
    public function login() {
        $this->view("auth/login");
    }

    public function register() {
        $this->view("auth/register");
        
    }
}
/*<?php
class AuthController extends Controller {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

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