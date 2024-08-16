<?php
date_default_timezone_set('Asia/Jakarta');

include '../DX_DBCON/DX_DBCON.php';

function validation($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = dbcon();

    if (isset($_POST['delete'])) {
        $partNumber = validation($_POST['partNumber']);
        $location = validation($_POST['location']);

        // Delete the row with matching part number and location
        $sql = "DELETE FROM [dbo].[DX_WH_OF_HR] WHERE [Part_Number] = ? AND [Location] = ?";
        $params = array($partNumber, $location);

        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            header("Location:../DX_HISTORY/DX_DATA_TABLE.php");
            exit;
        }
    }
    
    sqlsrv_close($conn);
}
?>
