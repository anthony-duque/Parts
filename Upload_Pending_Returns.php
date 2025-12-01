<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Pending Returns</title>
    <link rel='stylesheet' href='./styles/auth.css'>
</head>
<body>
    <div class="header">
        <a href='Admin.php'>Back to Admin</a>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    <form action='./php/Upload_Pending_Returns.php' method='POST'  enctype='multipart/form-data'>
    <table align='center'>
        <caption>
            <br/>
            <u><b>
            Upload Pending Returns File:
            </b></u>
            <br/>
            <br/>
        </caption>
        <thead>
            <tr>
                <th>
                    Pending Returns CSV file:
                </th>
                <td>
                    <input type='file' name='PendingReturns' required />
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
