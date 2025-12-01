<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html ng-app='UploadVendorsApp'>

<head>
    <title>Upload Vendor</title>
    <link rel='stylesheet' href='./styles/auth.css'>
    <script src='./scripts/angularjs.js'></script>
    <script src='./scripts/Upload_Vendors.js'></script>
    <meta charset="utf-8">
</head>

<body ng-controller='UploadVendorsCtrlr'>
    <div class="header">
        <a href='Admin.php'>Back to Admin</a>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    <form method='POST' action='./php/Upload_Vendors.php' enctype='multipart/form-data' accept-charset="utf-8">
        <table align='center' valign='middle'>
        <tr>
            <th colspan="2">Upload Vendors File</th>
        </tr>
        <tr>
            <td align='right'>
                Vendors Extract File
            </td>
            <td>
                <input type='file' name='VendorFile' required></input>
            </td>
        </tr>
        <!--tr>
            <td align='right'>
                Shop:
            </td>
            <td colspan='2'>
                <select name='shopID'>
                    <option value='' disabled selected required>
                        Pick a Location
                    </option>
                    <option n -repeat='shop in locations' value='{{ shop.id }}'>
                        { { shop.location } }
                    </option>
                </select>
            </td>
        </tr -->
        <tr>
            <td align='center' colspan='2'>
                <input type='submit' value='Upload Vendors' />
            </td>
        </tr>
        </table>
    </form>
</body>
</html>
