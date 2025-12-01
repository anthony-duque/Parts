<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Scheduled In VIN</title>
    <link rel='stylesheet' href='./styles/auth.css'>
</head>
<body>
    <div class="header">
        <a href='Admin.php'>Back to Admin</a>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    <form action='./php/Upload_Scheduled_In.php' method='POST'  enctype='multipart/form-data'>
    <table align='center'>
        <caption>
            <br/>
            <u><b>
            Upload Scheduled In with VIN File:
            </b></u>
            <br/>
            <br/>
        </caption>
        <thead>
            <tr>
                <th>
                    Scheduled In CSV file:
                </th>
                <td>
                    <input type='file' name='ScheduledInVIN' required />
                </td>
            </tr>
        </thead>
        <tbody>
            <td colspan='2' align='center'>
                <br/>
                <input type='submit' value='Upload File' />
            </td>
        </tbody>
    </table>
    </form>
</body>
</html>
