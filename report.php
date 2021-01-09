<!DOCTYPE html>
<html>
<head>
<title>Reports</title>
<link href="default.css" rel="stylesheet">
</head>
<body>

<h1>Reports</h1>

<?php

require_once 'dbconn.php';

$db = new dbconn();

//-----------------------------------------------------------------------
// Fetch list of reports

$sqlReports = "SELECT `name`, `display_name`, `url` FROM `php-report`;";
$reportResult = $db->execQuery($sqlReports, 1);

foreach ($reportResult as $reportRow) {
    echo "<a href=$reportRow->url>$reportRow->display_name</a><br>";
}

echo "<br>";

//-----------------------------------------------------------------------
// Fetch table name passed

if (isset($_GET['r'])) {
    $tableName = $_GET['r'];
}
else {
    die();
}

$sqlTable = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db->dbname' AND TABLE_NAME = '$tableName' AND TABLE_TYPE IN ('VIEW');";
$tables = $db->execQuery($sqlTable, 1);
if (empty($tables)) {
    die();
}

//-----------------------------------------------------------------------
// Fetch column details

$sqlColumns = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db->dbname' AND TABLE_NAME = '$tableName' ORDER BY ORDINAL_POSITION;";
$columns = $db->execQuery($sqlColumns, 1);

if (!empty($columns)) {

    $columnList = array();

    //-----------------------------------------------------------------------
    // Fetch columns
    foreach ($columns as $column) {
        array_push($columnList, $column->COLUMN_NAME);
    }

    //-----------------------------------------------------------------------
    // Build SQL and Report Header
    $outputString = "<p>\n";
    $outputString .= "<table>\n";
    $sqlData = "SELECT ";

    $outputString .= "<tr>\n";
    for ($x = 0; $x < sizeof($columnList); $x++) {
        $sqlData .= "`$columnList[$x]`, ";
        $outputString .= "<th>$columnList[$x]</th>";
    }
    $outputString .= "</tr>\n";

    $sqlData = rtrim($sqlData,", ");
    $sqlData .= " FROM $tableName;";

    //-----------------------------------------------------------------------
    // Get result data
    $sqlResult = $db->execQuery($sqlData, 1);

    foreach ($sqlResult as $row) {
        $outputString .= "<tr>";
        foreach ($row as $rowElement) {
            $outputString .= "<td>$rowElement</td>";
        }
        $outputString .= "</tr>\n";
    }

    //-----------------------------------------------------------------------
    // Display Results
    $outputString .= "</table>\n</p>\n";
    echo $outputString;
}

else {
    echo "<h3>Table $tableName not found!</h3>";
}

?>

</body>
</html>