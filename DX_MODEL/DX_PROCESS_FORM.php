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

    if (isset($_POST['registerIn'])) {
        $partNumbers = validation($_POST['partNumbersIn']);
        $location = validation($_POST['locationIn']);
        $qty = validation($_POST['qtyIn']);
        $operator = validation($_POST['operatorIn']);
        $timePutIn = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $status = 'IN';

        // Cek apakah lokasi sudah ada
        $sql = "SELECT COUNT(*) AS count FROM [dbo].[DX_WH_OF_HR] WHERE [Location] = ? AND [Status] = 'IN'";
        $params = array($location);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($row['count'] > 0) {
            // Lokasi sudah ada, kirim pesan error atau lakukan tindakan lain
            echo "Location already exists.";
            sqlsrv_close($conn);
            exit;
        }

        // Proses untuk menyimpan data jika lokasi belum ada
        $partNumbersArray = preg_split('/\s*,\s*|\s*\n\s*/', $partNumbers);
        $qtyArray = preg_split('/\s*,\s*|\s*\n\s*/', $qty);

        // Pastikan jumlah Part Number sama dengan jumlah Qty
        if (count($partNumbersArray) !== count($qtyArray)) {
            echo "Number of quantities must match number of part numbers.";
            sqlsrv_close($conn);
            exit;
        }

        foreach ($partNumbersArray as $index => $partNumber) {
            $partNumber = trim($partNumber);
            $qtyValue = trim($qtyArray[$index]);

            $sql = "INSERT INTO [dbo].[DX_WH_OF_HR] ([Part_Number], [Location], [Qty], [Date], [Time_Put_In], [Operator_In], [Status]) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = array($partNumber, $location, $qtyValue, $date, $timePutIn, $operator, $status);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
        header("Location:../DX_HISTORY/DX_DATA_TABLE.php");
        exit;
    }

    if (isset($_POST['registerOut'])) {
        $partNumber = validation($_POST['partNumberOut']);
        $location = validation($_POST['locationOut']);
        $operator = validation($_POST['operatorOut']);
        $timeOut = date('Y-m-d H:i:s');
        $status = 'OUT';

        // Update the row with matching part number and location, set Time_Out and Operator_Out
        $sql = "UPDATE [dbo].[DX_WH_OF_HR] SET [Time_Take_Out] = ?, [Operator_Out] = ?, [Status] = ? WHERE [Part_Number] = ? AND [Location] = ? AND [Status] = 'IN'";
        $params = array($timeOut, $operator, $status, $partNumber, $location);

        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            header("Location: ../DX_HISTORY/DX_DATA_TABLE.php");
            exit;
        }
    }

    sqlsrv_close($conn);
}
?>
