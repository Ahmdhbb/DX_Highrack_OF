<?php
include '../DX_DBCON/DX_DBCON.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $location = $_POST['location'];

    $conn = dbcon();
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $sql = "SELECT COUNT(*) AS count FROM [dbo].[DX_WH_OF_HR] WHERE [Location] = ? AND [Status] = 'IN'";
    $params = array($location);

    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row['count'] > 0) {
        echo 'exists';
    } else {
        echo 'available';
    }

    sqlsrv_close($conn);
}
?>
