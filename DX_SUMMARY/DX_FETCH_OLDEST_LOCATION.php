<?php
include '../DX_DBCON/DX_DBCON.php';

function fetchOldestLocation($partNumber)
{
    $conn = dbcon();
    $sql = "SELECT TOP 1 [Location]
            FROM [dbo].[DX_WH_OF_HR]
            WHERE [Part_Number] = ? AND [Status] != 'OUT'
            ORDER BY [Time_Put_In] ASC"; // Get the oldest location

    $params = array($partNumber);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_close($conn);
    return $row ? $row['Location'] : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['partNumber'])) {
    $partNumber = htmlspecialchars($_POST['partNumber']);
    echo fetchOldestLocation($partNumber);
} else {
    echo ''; // Invalid request
}
?>
