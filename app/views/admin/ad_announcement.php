<?php  include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php  include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Announcement</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
</head>
<body>
<div class="container">
   


    <!-- Main Content -->
    <main class="main-content">
         <h2>Send Announcement</h2>
         <section class="announcement">
            <form>
                <label>Title</label>
                <input type="text" placeholder="Enter announcement title">

                <label>Message</label>
                <textarea placeholder="Enter message"></textarea>

                <label>Recipient Role</label>
                <select>
                    <option>Select recipient role</option>
                    <option>All</option>
                    <option>HR manager</option>
                    <option>Caretaker</option>
                    <option>Clients</option>

                    
                </select>

                <label>Attachment (Optional)</label>
                <input type="file" class="file-button">

                <label>Schedule Send (Optional)</label>
                <input type="datetime-local">

                <div class="buttons">
                    <button type="submit">Send</button>
                    <button type="button" class="preview">Preview</button>
                </div>
            </form>
        </section>
        <section class="announcement-log">
            <h2>Announcement Log</h2>
            <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Target Audience</th>
                        <th>Title</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-07-26 10:00 AM</td>
                        <td>All</td>
                        <td>Company-wide Update</td>
                    </tr>
                    <tr>
                        <td>2024-07-25 02:30 PM</td>
                        <td>HR Manager</td>
                        <td>HR Policy Changes</td>
                    </tr>
                    <tr>
                        <td>2024-07-24 09:15 AM</td>
                        <td>Caretaker</td>
                        <td>New Training Module</td>
                    </tr>
                </tbody>
            </table>
            </div> 
        </section>
    </main>
</div>
</body>
</html>