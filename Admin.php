<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parts Department - Main Menu</title>
    <link rel='stylesheet' href='./styles/auth.css'>
    <style>
        body {
            margin: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        table {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border-collapse: collapse;
            border-radius: 5px;
            overflow: hidden;
        }
        caption {
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            background-color: #4CAF50;
            color: white;
        }
        td {
            padding: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 12px;
        }
        a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            width: 100%;
            box-sizing: border-box;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Parts Department - Main Menu</h1>
        <div>
            <div class="user-info">Logged in as: <?php echo htmlspecialchars(getLoggedInUsername()); ?></div>
            <form method="POST" action="logout.php" style="margin-top: 10px;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
    <table>
        <caption>Admin Menu</caption>
        <tr>
            <td>
                <ul>
                    <li>
                        <a href='index.html'>
                            Parts Department Page
                        </a>
                    </li>
                    <li>
                        <a href='Upload_Extracts.php'>
                            Upload CCC Extracts
                        </a>
                    </li>
                    <li>
                        <a href='Materials_Admin.php'>
                            Materials Admin
                        </a>
                    </li>
                    <li>
                        <a href='Upload_Scheduled_In.php'>
                            Upload Scheduled In VIN Extract
                        </a>
                    </li>
                    <li>
                        <a href='Upload_Vendors.php'>
                            Upload Vendors
                        </a>
                    </li>
                    <li>
                        <a href='Upload_Return_Form.php'>
                            Upload Return Forms
                        </a>
                    </li>
                    <li>
                        <a href='Upload_Pending_Returns.php'>
                            Upload Pending Returns
                        </a>
                    </li>
                    <li>
                        <a href='./extract_files/Labels.csv'>
                            Download Labels File (Labels.csv)
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
    </table>
</body>
</html>
