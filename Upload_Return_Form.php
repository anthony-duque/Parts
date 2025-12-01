<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Return Forms</title>
    <link rel='stylesheet' href='./styles/auth.css'>
</head>
<body>
    <div class="header">
        <a href='Admin.php'>Back to Admin</a>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    <form action='./php/Return_Forms.php' method='POST' enctype='multipart/form-data'>
        <table align='center' valign='middle'>
        <tr>
            <th colspan="2">
                <u>Upload Return Forms</u>
                <br/><br/>
            </th>
        </tr>
        <tr>
            <th>
                Signed Return Form:
            </th>
            <td>
                <input type='file'
                        name='returnForm'
                        accept="image/*"
                        required />
            </td>
        </tr>
        <tr>
            <td colspan='2' align='center'>
                <br/>
                <input type='submit' value='Upload' />
            </td>
        </tr>
        </table>
    </form>
</body>
</html>
