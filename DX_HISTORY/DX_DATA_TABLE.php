<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Part Data</title>
    <link rel="stylesheet" href="../Asset/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="../Asset/jquery-1.11.3.min.js"></script>

    <!-- SheetJS untuk Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF dan plugin untuk AutoTable untuk PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

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
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #4B70F5;
            color: white;
        }

        .table td {
            background-color: #F3F7EC;
        }

        .btn {
            font-size: 0.9em;
            padding: 5px 10px;
        }

        .btn-edit {
            color: blue;
            border: 1px solid blue;
            background-color: transparent;
        }

        .btn-delete {
            color: red;
            border: 1px solid red;
            background-color: transparent;
        }

        h1 {
            padding-top: 10px;
            font-size: 3em;
            text-align: center;
            font-weight: 700;
        }

        .apaja {
            display: flex;
            align-items: center;
            left: 0;
            top: 0;
            justify-content: space-between;
            width: 100%;
            z-index: 1001;
        }

        ul.navlist {
            display: flex;
            align-items: center;
            padding-top: 15px;
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
            margin-left: 15px;
        }

        .table th,
        .table td {
            border: 1px solid #100f0f;
        }

        #searchInput {
            width: 100%;
            max-width: 500px;
            margin-bottom: 20px;
            border: 1px solid #9BABB8;
        }

        .filter-buttons {
            margin-bottom: 20px;
            text-align: center;
        }

        .filter-buttons button {
            margin-right: 10px;
            border: none;
            background-color: #4B70F5;
            color: white;
            border-radius: 4px;
            font-size: 18px;
            padding: 5px;
        }

        .filter {
            display: flex;
            justify-content: space-between;
        }
        .excel{
            background-color: green;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            margin-bottom: 10px;
            
        }
        .pdf{
            background-color: red;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            margin-bottom: 10px;
        }
      .clear{
        background-color: #9BABB8;
        color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            margin-bottom: 10px;
      }
    </style>

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="apaja">
            <a href="#" class="highrack">HIGH RACK</a>
            <ul class="navlist">
                <li><a href="../DX_FORM/DX_FORM_IN_OUT.html" class="active">In - Out</a></li>
                <li><a href="../DX_SUMMARY/DX_SUMMARY_TABLE.php">Summary</a></li>
                <li><a href="../DX_VISUAL/DX_VISUALIZATION.php">Visual</a></li>
            </ul>
        </div>
    </nav>
    <h1>History Part Number</h1>

    <div class="table-container">
        <div class="filter">
            <div class="search-container">
                <input type="text" id="searchInput" onkeyup="limitSearchLength(this, 15); searchTable()" placeholder="Search for part numbers" class="form-control mb-3">
            </div>
            <div class="filter-buttons">
                <button onclick="filterByDay()">Daily</button>
                <button onclick="filterByWeek()">Weekly</button>
                <button onclick="filterByMonth()">Monthly</button>
              
            </div>
          
        </div>
        <div class="file">
                <button class="excel" onclick="downloadExcel()">Excel</button>
                <button class="pdf" onclick="downloadPDF()">PDF</button>
                <button class="clear" onclick="clearFilters()">Clear</button>
            </div>
        <table id="refresh" class="table table-responsive table-bordered border-black rounded">
            <thead class="table-dark text-center">
                <tr>
                    <th style="text-align: center;" scope="col">Part Number</th>
                    <th style="text-align: center;" scope="col">Location</th>
                    <th style="text-align: center;" scope="col">Qty</th>
                    <th style="text-align: center;" scope="col">Date</th>
                    <th style="text-align: center;" scope="col">Time Put In</th>
                    <th style="text-align: center;" scope="col">Operator In</th>
                    <th style="text-align: center;" scope="col">Time Out</th>
                    <th style="text-align: center;" scope="col">Operator Out</th>
                    <th style="text-align: center;" scope="col">Status</th>
                    <th style="text-align: center;" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody id="tableOverflowUpdate" class="text-center">
                <?php
                require '../DX_MODEL/DX_FETCH_DATA.php';
                $data = fetchData();

                if (!empty($data)) {
                    foreach ($data as $key => $highrack) { ?>
                        <tr>
                            <td><?= htmlspecialchars($highrack['Part_Number']) ?></td>
                            <td><?= htmlspecialchars($highrack['Location']) ?></td>
                            <td><?= htmlspecialchars($highrack['Qty']) ?></td>
                            <td><?= $highrack['Time_Put_In'] ? $highrack['Time_Put_In']->format("d-m-Y") : '' ?></td>
                            <td><?= $highrack['Time_Put_In'] ? $highrack['Time_Put_In']->format("H:i:s") : '' ?></td>
                            <td><?= htmlspecialchars($highrack['Operator_In']) ?></td>
                            <td><?= $highrack['Time_Take_Out'] ? $highrack['Time_Take_Out']->format("H:i:s") : '' ?></td>
                            <td><?= htmlspecialchars($highrack['Operator_Out']) ?></td>
                            <td><?= htmlspecialchars($highrack['Status']) ?></td>
                            <td>
                                <form method="post" action="../DX_HISTORY/DX_DELETE_DATA.php" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    <input type="hidden" name="partNumber" value="<?= htmlspecialchars($highrack['Part_Number']) ?>">
                                    <input type="hidden" name="location" value="<?= htmlspecialchars($highrack['Location']) ?>">
                                    <button type="submit" name="delete" class="btn btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                <?php }
                } else {
                    echo "<tr><td colspan='10'>No data available</td></tr>";
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
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.substring(0, 15).toUpperCase();
            table = document.getElementById("refresh");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }

        function filterByDay() {
            let now = new Date();
            now.setHours(0, 0, 0, 0); // Set time to midnight
            filterTableByDateRange(now, new Date());
        }

        function filterByWeek() {
            let now = new Date();
            let firstDayOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            firstDayOfWeek.setHours(0, 0, 0, 0); // Set time to midnight
            let lastDayOfWeek = new Date(firstDayOfWeek);
            lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);
            lastDayOfWeek.setHours(23, 59, 59, 999); // Set time to end of the day
            filterTableByDateRange(firstDayOfWeek, lastDayOfWeek);
        }

        function filterByMonth() {
            let now = new Date();
            let firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            firstDayOfMonth.setHours(0, 0, 0, 0); // Set time to midnight
            let lastDayOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            lastDayOfMonth.setHours(23, 59, 59, 999); // Set time to end of the day
            filterTableByDateRange(firstDayOfMonth, lastDayOfMonth);
        }

        function filterTableByDateRange(startDate, endDate) {
            let table = document.getElementById("refresh");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let dateCell = tr[i].getElementsByTagName("td")[3];
                if (dateCell) {
                    let dateValue = dateCell.textContent || dateCell.innerText;
                    let date = new Date(dateValue.split('-').reverse().join('-'));

                    date.setHours(0, 0, 0, 0); // Set time to midnight for comparison

                    if (date >= startDate && date <= endDate) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function clearFilters() {
            let table = document.getElementById("refresh");
            let tr = table.getElementsByTagName("tr");
            for (let i = 1; i < tr.length; i++) {
                tr[i].style.display = "";
            }
        }

        function downloadExcel() {
            let table = document.getElementById("refresh");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, "Part_Data.xlsx");
        }

        function downloadPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();
            doc.autoTable({
                html: '#refresh'
            });
            doc.save('Part_Data.pdf');
        }

        document.getElementById("searchInput").addEventListener("input", function() {
            limitSearchLength(this, 14);
        });
    </script>
</body>

</html>
