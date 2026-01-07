<?php
class CaretakerProfileController extends Controller
{
    private $profileModel;

    public function __construct()
    {
        $this->profileModel = $this->model('CaretakerModel');
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            session_start();
            $userId = $_SESSION['user_id'];

            // Image upload
            $imageName = null;
            if (!empty($_FILES['profile_image']['name'])) {
                $imageName = time() . '_' . $_FILES['profile_image']['name'];
                move_uploaded_file(
                    $_FILES['profile_image']['tmp_name'],
                    APPROOT . '/../public/uploads/' . $imageName
                );
            }

            $data = [
                'user_id' => $userId,
                'full_name' => $_POST['full_name'],
                'phone' => $_POST['phone'],
                'experience' => $_POST['experience'],
                'qualifications' => $_POST['qualifications'],
                'profile_image' => $imageName
            ];

            if ($this->profileModel->saveProfile($data)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }
}
