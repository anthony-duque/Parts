<?php
require_once 'php/session_handler.php';
requireLogin();
?>
<!DOCTYPE html>
<html ng-app="MatAdminApp">
<head>
    <title>Materials Admin</title>
    <link rel='stylesheet' href="./styles/Parts.css" />
    <link rel='stylesheet' href='./styles/auth.css'>
    <script src='./scripts/angularjs.min.js'></script>
    <script src='./scripts/Materials_Admin.js'></script>
    <script src='./scripts/UtilityFunctions.js'></script>
</head>
<body ng-controller="MatAdminCtlr">
    <div class="header">
        <a href='Admin.php'>Back to Admin</a>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    <table id='tblMain' width='100%' border=1 valign='top'>
    <tr>
        <td id='colEntry' width='50%' valign='top'>
            <table id='tblMatEntry' width='100%' align='top'>
            <caption>
                <b>New Material Entry</b>
            </caption>
            <tr>
                <th>
                    Part Number:
                </th>
                <td>
                    <input type='text' ng-model='newMaterial.part_number' placeholder="Part Number"/>
                </td>
            </tr>
            <tr>
                <th>
                    Brand:
                </th>
                <td>
                    <input type='text' ng-model='newMaterial.brand' placeholder="Brand"/>
                </td>
            </tr>
            <tr>
                <th>
                    Description:
                </th>
                <td>
                    <input type='text' ng-model='newMaterial.description' placeholder="Description" />
                </td>
            </tr>
            <tr>
                <th>
                    Unit:
                </th>
                <td>
                    <input type='text' ng-model='newMaterial.unit' placeholder="Unit" />
                </td>
            </tr>
            <tr>
                <th>
                    Material Type:
                </th>
                <td>
                    <select ng-model='newMaterial.type'>
                        <option ng-repeat='eachType in materialTypesList' value='{{ eachType.code }}'>
                            {{ eachType.description }}
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    Reorder Quantity:
                </th>
                <td>
                    <input  type='number'
                            ng-model='newMaterial.reorder_qty'
                            placeholder="Reorder Quantity"
                            min=0 max=10 />
                </td>
            </tr>
            <tr>
                <td colspan='2' align='center'>
                    <input type='submit' value='Add Material' ng-click='Add_Material();' >
                </td>
            </tr>
            </table>
            {{ duplicatePart }}
        </td>
        <td id='colMatList' align='center'>
            <table id='tblMatList' align='center' border='1' ng-show='materialsList.length > 0'>
            <thead>
            <caption>
                Search : <input type='text' ng-model='searchText' />&nbsp;
                <input type='button' ng-click='searchText = '' value="Clear" ng-show='searchText > ""' />
            </caption>
            <tr>
                <th ng-click='SortMaterials("brand");'>
                    Brand
                </th>
                <th ng-click='SortMaterials("description");'>
                    Description
                </th>
                <th ng-click='SortMaterials("part_number");'>
                    Part Number
                </th>
                <th ng-click='SortMaterials("unit");'>
                    Unit
                </th>
                <th ng-click='SortMaterials("reorder_qty");'>
                    Reorder Qty
                </th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat='eachMat in materialsList | orderBy: sortFld | filter: searchText'
                ng-class='ToggleColor($index,  "white", "lightBlue");'>
                <td align='center'>
                    {{ eachMat.brand }}
                </td>
                <td align='left'>
                    {{ eachMat.description }}
                </td>
                <td align='center'>
                    {{ eachMat.part_number }}
                </td>
                <td align='center'>
                    {{ eachMat.unit }}
                </td>
                <td align='center'>
                    {{ eachMat.reorder_qty }}
                </td>
            </tr>
            </tbody>
            </table>
        </td>
    </tr>
    </table>
</body>
</html>
