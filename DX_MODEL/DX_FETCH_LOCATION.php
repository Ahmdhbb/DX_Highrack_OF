<?php
include '../DX_DBCON/DX_DBCON.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partNumber = $_POST['partNumber'];

    $conn = dbcon();
    $sql = "SELECT Location FROM [dbo].[DX_WH_OF_HR] WHERE [Part_Number] = ? AND [Status] = 'IN'";
    $params = array($partNumber);

    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $location = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $response = array('location' => $location['Location'] ?? null);

    echo json_encode($response);

    sqlsrv_close($conn);
}
?>
