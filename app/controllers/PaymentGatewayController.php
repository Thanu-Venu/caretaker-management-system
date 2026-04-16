<?php

require_once APPROOT . '/core/PayHereHelper.php';
require_once APPROOT . '/controllers/PaymentController.php';
require_once APPROOT . '/core/RecurringPaymentService.php';

class PaymentGatewayController extends Controller
{
    private $clientModel;

    public function __construct()
    {
        $this->clientModel = $this->model('ClientModel');
    }

    public function return()
    {
        $orderId = (string)($_GET['order_id'] ?? '');
        if ($orderId !== '') {
            $_SESSION['success'] = 'Payment return received. Final status will be confirmed shortly.';
        }

        header('Location: ' . URLROOT . '/client/c_paymentSuccess');
        exit;
    }

    public function cancel()
    {
        $orderId = (string)($_GET['order_id'] ?? '');
        if ($orderId !== '') {
            $payment = $this->clientModel->getPaymentByOrderId($orderId);
            if ($payment && strtolower((string)$payment['status']) === 'pending') {
                $this->clientModel->updatePaymentStatus($payment['id'], 'rejected');

                $paymentType = strtolower(trim((string)($payment['payment_type'] ?? '')));
                if ($paymentType === 'advance') {
                    $this->clientModel->updateBookingStatus((int)$payment['booking_id'], 'Payment_Requested');
                }
            }
        }

        $_SESSION['error'] = 'Payment was cancelled.';
        header('Location: ' . URLROOT . '/client/c_upcomingBookings');
        exit;
    }

    public function notify()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }

        if (!PayHereHelper::isConfigured()) {
            http_response_code(500);
            echo 'Gateway not configured';
            exit;
        }

        $payload = $_POST;
        $merchantSecret = defined('PAYHERE_MERCHANT_SECRET') ? PAYHERE_MERCHANT_SECRET : '';
        if ($merchantSecret === '' || !PayHereHelper::verifyNotifySignature($payload, $merchantSecret)) {
            http_response_code(400);
            echo 'Invalid signature';
            exit;
        }

        $orderId = (string)($payload['order_id'] ?? '');
        $statusCode = (int)($payload['status_code'] ?? 0);
        $statusMessage = (string)($payload['status_message'] ?? '');
        $payherePaymentId = (string)($payload['payment_id'] ?? '');
        $md5sig = (string)($payload['md5sig'] ?? '');

        $payment = $this->clientModel->getPaymentByOrderId($orderId);
        if (!$payment) {
            http_response_code(404);
            echo 'Payment not found';
            exit;
        }

        $isApproved = ($statusCode === 2);
        $newStatus = $isApproved ? 'approved' : 'rejected';

        if (strtolower((string)$payment['status']) !== 'approved') {
            $this->clientModel->updatePaymentGatewayStatus(
                (int)$payment['id'],
                $newStatus,
                $statusCode,
                $statusMessage,
                $payherePaymentId,
                $md5sig
            );

            if ($isApproved) {
                $notificationModel = $this->model('NotificationModel');
                $amountPaid = (float)($payment['amount'] ?? 0);
                $bookingId = (int)($payment['booking_id'] ?? 0);
                $paymentType = strtolower(trim((string)($payment['payment_type'] ?? 'advance')));
                $hrUsers = $notificationModel->getHRUsers();

                $clientTitle = 'Payment Successful';
                $clientMessage = "Your payment for booking #{$bookingId} was successful.\n" .
                    "Amount: LKR " . number_format($amountPaid, 2) . "\n" .
                    "You can check payment details in your payments dashboard.";

                $notificationModel->addNotification(
                    (int)$payment['client_id'],
                    'client',
                    $clientTitle,
                    $clientMessage,
                    URLROOT . '/client/payments?tab=paid_history'
                );

                if (!empty($hrUsers)) {
                    $paymentLabel = $paymentType === 'advance' ? 'Advance Payment' : 'Recurring Payment';
                    $hrMessage = $paymentLabel . " received for booking #{$bookingId}.\n" .
                        "Amount: LKR " . number_format($amountPaid, 2) . "\n" .
                        "Payment status: Approved\n" .
                        "Please review the booking in the HR pending requests page.";

                    foreach ($hrUsers as $hrUser) {
                        $notificationModel->addNotification(
                            (int) $hrUser['id'],
                            'Manager',
                            $paymentLabel . ' Approved',
                            $hrMessage,
                            URLROOT . '/hr/hr_pending_request?booking_id=' . $bookingId
                        );
                    }
                }

                $paymentType = strtolower(trim((string)($payment['payment_type'] ?? '')));
                $bookingStatus = (string)($payment['booking_status'] ?? '');
                $isAdvanceApproval = ($paymentType === 'advance')
                    || in_array($bookingStatus, ['Payment_Requested', 'Advance_Paid'], true);

                if ($isAdvanceApproval) {
                    $this->clientModel->updateBookingStatus((int)$payment['booking_id'], 'Advance_Paid');
                    $bookingDetails = $this->clientModel->getBookingById((int)$payment['booking_id']);
                    if ($bookingDetails) {
                        $this->clientModel->updateBookingAdvancePaidDate((int)$payment['booking_id']);
                        PaymentController::createRecurringPayments((int)$payment['booking_id'], $bookingDetails);
                    }

                    $notificationModel->addNotification(
                        (int)$payment['caretaker_id'],
                        'caretaker',
                        'Advance Payment Approved',
                        'Advance payment for booking #' . $payment['booking_id'] . ' has been approved. You can now view the booking details in your Bookings page.',
                        URLROOT . '/caretaker/ct_booking?booking_id=' . $payment['booking_id'] . '&tab=upcoming'
                    );
                } else {
                    $recurringService = new RecurringPaymentService();
                    $recurringService->markRecurringPaymentAsPaidByDetails(
                        (int)$payment['booking_id'],
                        (string)$payment['due_date'],
                        (float)$payment['amount'],
                        (int)$payment['id']
                    );
                }
            } else {
                $paymentType = strtolower(trim((string)($payment['payment_type'] ?? '')));
                if ($paymentType === 'advance') {
                    $this->clientModel->updateBookingStatus((int)$payment['booking_id'], 'Payment_Requested');
                }
            }
        }

        http_response_code(200);
        echo 'OK';
        exit;
    }
}
