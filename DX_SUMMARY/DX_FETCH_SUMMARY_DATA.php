<?php
include '../DX_DBCON/DX_DBCON.php';

function fetchSummaryData() {
    $conn = dbcon();

    // Menggunakan CONVERT atau CAST untuk memastikan Qty menjadi tipe numerik
    $sql = "SELECT [Part_Number], STRING_AGG([Location], ', ') AS Locations, 
            SUM(CONVERT(INT, [Qty])) AS Total_Qty
            FROM [dbo].[DX_WH_OF_HR]
            WHERE [Status] != 'OUT'
            GROUP BY [Part_Number]
            ORDER BY Total_Qty DESC";

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

header('Content-Type: application/json');
echo json_encode(fetchSummaryData());
?>
