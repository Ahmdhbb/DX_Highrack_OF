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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Table</title>
    <link rel="stylesheet" href="../Asset/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="../Asset/jquery-1.11.3.min.js"></script>
    <style>
        .table-container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border: 1px solid black;
        }

        .table th {
            background-color: #4B70F5;
            color: white;
        }

        .table td {
            background-color: #F3F7EC;
        }

        h1 {
            text-align: center;
            font-size: 3em;
            font-weight: 700;
        }

        .btn {
            font-size: 0.9em;
            padding: 5px 10px;
        }

        .apaja {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            z-index: 1001;
        }

        .navbar {
            width: 100%;
            background-color: #343a40;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        ul.navlist {
            display: flex;
            align-items: center;
            margin-top: 15px;
            padding-right: 30px;
        }

        .navlist li {
            margin-left: 1rem;
        }

        .navlist li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 5px 17px;
            border: 2px solid #12f7ff;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .navlist li a:hover {
            box-shadow: 0 0 1rem #12f7ff;
            background: #12f7ff;
            color: #1d002c;
        }

        .highrack {
            font-size: 2rem;
            color: white;
            text-decoration: none;
            font-weight: 700;
            margin-left: 30px;
        }

        h1 {
            padding-top: 75px;
            font-size: 3em;
            text-align: center;
            font-weight: 700;
        }

        .search-container {
            margin-bottom: 20px;
        }

        #searchInput {
            width: 100%;
            max-width: 500px;
            margin-bottom: 20px;
            border: 1px solid #9BABB8;
        }

        .search_loc {
            display: flex;
            gap: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="apaja">
            <a href="#" class="highrack">HIGH RACK</a>
            <ul class="navlist">
                <li>
                    <a href="../DX_FORM/DX_FORM_IN_OUT.html" class="active">In - Out</a>
                </li>
                <li><a href="../DX_VISUAL/DX_VISUALIZATION.php">Visual</a></li>
                <li><a href="../DX_HISTORY/DX_DATA_TABLE.php">History</a></li>
            </ul>
        </div>
    </nav>
    <h1>Summary of Parts on Hand</h1>
    <div class="table-container">
        <div class="search_loc">
            <input type="text" id="searchInput" onkeyup="limitSearchLength(this, 14); searchTable()" placeholder="Search for part numbers" class="form-control mb-3">
        </div>
        <table class="table table-responsive table-bordered border-black rounded">
            <thead class="table-dark text-center">
                <tr>
                    <th scope="col">Part Number</th>
                    <th scope="col">Locations</th>
                    <th scope="col">Total Quantity</th>
                </tr>
            </thead>
            <tbody class="text-center" id="summaryTable">
                <?php
                // Memanggil fungsi fetchSummaryData dan menampilkan data
                $data = fetchSummaryData();

                if (!empty($data)) {
                    foreach ($data as $summary) { ?>
                        <tr>
                            <td><?= htmlspecialchars($summary['Part_Number']) ?></td>
                            <td><?= htmlspecialchars($summary['Locations']) ?></td>
                            <td><?= htmlspecialchars($summary['Total_Qty']) ?></td>
                        </tr>
                <?php }
                } else {
                    echo "<tr><td colspan='3'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function limitSearchLength(element, maxLength) {
            if (element.value.length > maxLength) {
                element.value = element.value.substring(0, maxLength);
            }
        }

        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("summaryTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        document.getElementById("searchInput").addEventListener("input", function () {
            limitSearchLength(this, 14);
        });
    </script>
</body>

</html>
