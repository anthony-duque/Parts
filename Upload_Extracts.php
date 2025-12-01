<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html ng-app="csvUploadApp">
<head>
    <title>Extracts Upload</title>
    <link rel='stylesheet' href='./styles/auth.css'>
    <script src='./scripts/angularjs.min.js'></script>
    <script src='./scripts/Extracts_Upload.js'></script>
</head>
<body ng-controller='csvUploadController'>
    <div class="header">
        <a href='Admin.php'>Back to Admin</a>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    <form action="./php/Upload_Extracts.php" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='uploadDateTime' value='{{ currDateTime }}'>
        <table align='center'>
            <caption>
                <b>Upload Extract Files</b>
            </caption>
            <tr>
                <th>Daily Out CSV file:</th>
                <td>
                    <input type='file' name="DailyOutCSV" required />
                </td>
            </tr>
            <tr>
                <th>Parts Status CSV file:</th>
                <td>
                    <input type='file' name="PartsStatusCSV" required />
                </td>
            </tr>
            <tr>
                <td align='center' colspan='2'>
                    <input type='submit' value='Upload Files' />
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
