<?php
require_once "../app/models/ComplaintModel.php";
require_once "../app/models/NotificationModel.php";
require_once "../app/models/ClientModel.php";

class ComplaintController
{
    private $complaintModel;
    private $clientModel;
    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->clientModel = new ClientModel();

    }

    // Show the complaint form
    public function create()
    {
        include_once "../app/views/client/c_complaintReg.php";
    }

    // Store complaint in DB
    public function store()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $client_name = $_SESSION['user']['name'];

            $caretaker_name = trim($_POST["caretaker_name"]);
            $category = $_POST["category"];
            $details = trim($_POST["details"]);

            // Validate
            if (empty($client_name) || empty($caretaker_name) || empty($category) || empty($details)) {
                echo "<script>alert('All fields are required!'); window.history.back();</script>";
                exit;
            }

            $success = $this->complaintModel->createComplaint($client_name, $caretaker_name, $category, $details);

            if ($success) {
                echo "<script>
        alert('Complaint submitted successfully!');
        window.location.href='" . URLROOT . "/public/index.php?url=Complaint/complaintlist';

    </script>";
                exit;
            } else {
                echo "<script>alert('Error submitting complaint.'); window.history.back();</script>";
                exit;
            }
        }
    }




    public function index()
    {
         $complaintModel = new ComplaintModel();
        $complaints = $this->complaintModel->getAllComplaints();
            $data['ct_complaints'] = $complaintModel->getCaretakerComplaints();



        // echo '<pre>';
        // var_dump($complaints[0] ?? $complaints);
        // echo '</pre>';
        // exit;
        include_once "../app/views/hr/hr_complaint.php";
    

    }
    
public function accept($id)
{
    $complaintModel = new ComplaintModel();
    $complaintModel->updatect_ComplaintStatus($id, 'In Progress');

    header("Location: /hrComplaint/index");
}

public function resolve($id)
{
    $complaintModel = new ComplaintModel();
    $complaintModel->updatect_ComplaintStatus($id, 'Resolved');

    header("Location: /hrComplaint/index");
}


    // inside ComplaintController class

    // Show edit form
    public function edit($id = null)
    {
        // $id may come as string; cast to int for safety
        $id = (int) $id;
        if (!$id) {
            echo "<script>alert('Invalid complaint id'); window.location.href='/CMA/public/index.php?url=Complaint/index';</script>";
            exit;
        }

        $complaint = $this->complaintModel->getComplaintById($id);
        if (!$complaint) {
            echo "<script>alert('Complaint not found'); window.location.href='/CMA/public/index.php?url=Complaint/index';</script>";
            exit;
        }

        // make $complaint available in view
        include_once "../app/views/hr/complaint_edit.php";
    }

    // Process update (POST)
    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            echo "<script>alert('Invalid request'); window.location.href='/CMA/public/index.php?url=Complaint/index';</script>";
            exit;
        }

        // required fields from the form
        $id = isset($_POST['Id']) ? (int) $_POST['Id'] : 0;
        $client_name = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
        $caretaker_name = isset($_POST['caretaker_name']) ? trim($_POST['caretaker_name']) : '';
        $category = isset($_POST['category']) ? $_POST['category'] : '';
        $details = isset($_POST['details']) ? trim($_POST['details']) : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 'Open';

        if (!$id || empty($client_name) || empty($caretaker_name) || empty($category) || empty($details) || empty($status)) {
            echo "<script>alert('All fields are required.'); window.history.back();</script>";
            exit;
        }

        $success = $this->complaintModel->updateComplaint($id, $client_name, $caretaker_name, $category, $details, $status);

        if ($success) {
            echo "<script>
                alert('Complaint updated successfully!');
                window.location.href='/CMA/public/index.php?url=Complaint/index';
              </script>";
            exit;
        } else {
            echo "<script>alert('Failed to update complaint.'); window.history.back();</script>";
            exit;
        }
    }



    public function delete($id)
    {
        if ($this->complaintModel->deleteComplaintById($id)) {
            echo "<script>
            alert('Complaint deleted successfully!');
            window.location.href = '/CMA/public/index.php?url=Complaint/index';
        </script>";
        } else {
            echo "<script>
            alert('Failed to delete complaint!');
            window.location.href = '/CMA/public/index.php?url=Complaint/index';
        </script>";
        }
    }
    // =================== CLIENT-SIDE FUNCTIONS ===================

    // View complaints submitted by the logged-in client
    public function myComplaints()
    {


        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
            echo "<script>alert('Please login first.'); window.location.href='/CMA/public/index.php?url=auth/login';</script>";
            exit;
        }

        $client_name = $_SESSION['user']['name'];
        $complaints = $this->complaintModel->getComplaintsByClient($client_name);

        include_once APPROOT . "/views/templates/client/c_header.php";
        include_once APPROOT . "/views/templates/client/c_sidebar.php";

        // Then include main complaint list
        include_once APPROOT . "/views/client/c_complaintlist.php";
    }



    // Show edit form for client
    public function clientEdit($id = null)
    {
        $id = (int) $id;
        $complaint = $this->complaintModel->getComplaintById($id);

        if (!$complaint) {
            echo "<script>alert('Complaint not found'); window.location.href='/CMA/public/index.php?url=Complaint/myComplaints';</script>";
            exit;
        }

        include_once "../app/views/client/c_complaintedit.php";
    }


    // Handle update for client
    public function clientUpdate()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = (int) $_POST['Id'];
            $details = trim($_POST['details']);

            if (empty($details)) {
                echo "<script>alert('Details are required.'); window.history.back();</script>";
                exit;
            }

            $success = $this->complaintModel->updateComplaintByClient($id, $details);

            if ($success) {
                echo "<script>alert('Complaint updated successfully!'); window.location.href='/CMA/public/index.php?url=Complaint/myComplaints';</script>";
            } else {
                echo "<script>alert('Failed to update complaint.'); window.history.back();</script>";
            }
        }
    }


    // Delete complaint for client
    public function clientDelete($id)
    {
        if ($this->complaintModel->deleteComplaintByClient($id)) {
            echo "<script>alert('Complaint deleted successfully!'); window.location.href='/CMA/public/index.php?url=Complaint/myComplaints';</script>";
        } else {
            echo "<script>alert('Failed to delete complaint.'); window.history.back();</script>";
        }
    }

    public function complaintlist()
    {
        $client_name = $_SESSION['user']['name'];
        $complaints = $this->complaintModel->getComplaintsByClient($client_name);

        include_once APPROOT . "/views/templates/client/c_header.php";
        include_once APPROOT . "/views/templates/client/c_sidebar.php";
        include_once APPROOT . "/views/client/c_complaintlist.php";
    }

    public function updateStatus()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
            echo "<script>alert('Unauthorized');</script>";
            exit;
        }

        $id = (int) $_POST['Id'];
        $status = $_POST['status'];

        if (!$id || !$status) {
            echo "<script>alert('Invalid data');</script>";
            exit;
        }

        // Update status
        $this->complaintModel->updateComplaintStatus($id, $status);

        // Get complaint to notify client
        $complaint = $this->complaintModel->getComplaintById($id);

        // Make sure you have the client's ID
        $client_name = $complaint['client_name'];
        $client = $this->clientModel->getClientById($client_name); // Or find by email
        $client_id = $client['id'] ?? null;

        if ($client_id) {
            $notifModel = new NotificationModel();
            $notifModel->addNotification(
                $client_id,           // user_id
                'client',             // user_role
                "Complaint Status Update",  // title
                "Your complaint #{$id} status changed to '{$status}'",  // message
                URLROOT . "/public/index.php?url=Complaint/myComplaints" // optional link
            );
        }

        header("Location: " . URLROOT . "/public/index.php?url=Complaint/index");
        exit;
    }



}

?>