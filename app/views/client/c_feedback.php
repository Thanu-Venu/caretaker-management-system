<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Feedback</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_feedback_table.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }

        .main-content {
            margin-left: 270px;
            padding: 30px;
            background: #f8fafc;
            min-height: calc(100vh - 60px);
        }

        h2 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .info-text {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            color: #6366f1;
            font-size: 14px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-text:before {
            content: "ℹ";
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
        }

        .info-text a {
            color: #667eea;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .info-text a:hover {
            color: #764ba2;
            border-bottom-color: #764ba2;
        }

        .rating-display {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .star-filled {
            color: #ffc107;
            font-size: 16px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .star-empty {
            color: #e5e7eb;
            font-size: 16px;
        }

        .rating-text {
            color: #6b7280;
            font-size: 12px;
            margin-left: 6px;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <div class="main-content">

        <h2>My Feedback History</h2>
        <p class="info-text">To give feedback, go to <a href="<?= URLROOT ?>/client/c_pastBookings">Past Bookings</a> and click "Give Feedback" on a completed service.</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Caretaker</th>
                    <th>Rating</th>
                    <th>Feedback</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($feedbacks)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                            No feedback submitted yet. Complete a service and give feedback from Past Bookings.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $fb): ?>
                        <tr>
                            <td data-label="Caretaker"><?= htmlspecialchars($fb['caretaker_name']) ?></td>
                            <td data-label="Rating">
                                <div class="rating-display">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?= $i <= $fb['rating'] ? 'star-filled' : 'star-empty' ?>">★</span>
                                    <?php endfor; ?>
                                    <span class="rating-text">(<?= $fb['rating'] ?>/5)</span>
                                </div>
                            </td>
                            <td data-label="Feedback"><?= htmlspecialchars($fb['feedback']) ?></td>
                            <td data-label="Date"><?= date('M d, Y', strtotime($fb['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

    </div>

</body>

</html>