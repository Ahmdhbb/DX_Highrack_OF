<?php

include '../DX_DBCON/DX_DBCON.php';

function fetchData() {
    $conn = dbcon();
    // Query to sort by Status ('In' before 'Out') and then by Time_Put_In
    $sql = "SELECT [ID], [Part_Number], [Location], [Qty], [Time_Put_In], [Operator_In], [Time_Take_Out], [Operator_Out], [Status]
            FROM [dbo].[DX_WH_OF_HR]
            ORDER BY CASE WHEN [Status] = 'In' THEN 0 ELSE 1 END, 
                     [Time_Put_In] ASC"; // Sort by Status (In before Out) and Time_Put_In

    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    sqlsrv_close($conn);
    return $data;
}
?>
