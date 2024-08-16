<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualization</title>
    <link rel="stylesheet" href="../Asset/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="../Asset/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2/dist/chartjs-plugin-annotation.min.js"></script>
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px; /* Ensure space below the header */
        }
        .chart-container {
            width: 800px;
            padding: 20px;
            position: relative;
        }
        h1 {
            padding-top: 70px;
            text-align: center;
            font-size: 3em;
            font-weight: 700;
        }
        .ratio-text {
            position: absolute;
            top: -20px; /* Adjust position if needed */
            left: 20%;
            transform: translateX(-50%);
            font-size: 2em;
            font-weight: bold;
            text-align: center;
        }
        canvas {
            padding-top: 10px;
            width: 100% !important; /* Ensure canvas takes full width */
            height: 400px !important; /* Provide a fixed height */
        }
        .table th {
            background-color: #4B70F5; /* Blue header */
            color: white;
        }

        .table td {
            background-color: #F3F7EC;/* Light blue cells */
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
        .navbar {
            width: 100%;
            background-color: #343a40; /* bg-dark */
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            z-index: 1000;
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
            margin-left: 30px;
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
                <li><a href="../DX_HISTORY/DX_DATA_TABLE.php">History</a></li>
            </ul>
        </div>
    </nav>
    <h1>Visual Stock on Hand</h1>
    <div class="container">
        <div class="chart-container">
            <div id="ratioText" class="ratio-text"></div>
            <canvas id="stockChart"></canvas>
        </div>
        <div class="table-container">
            <table class="table table-responsive table-bordered border-black rounded">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="text-align: center;">Top 5 Part Number</th>
                        <th style="text-align: center;">Total Qty [Pallet]</th>
                    </tr>
                </thead>
                <tbody id="top5Parts" class="text-center">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('../DX_SUMMARY/DX_FETCH_SUMMARY_DATA.php')
            .then(response => {
                console.log(response); // Log the response object for debugging
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Fetched Data:', data); // Log fetched data

                if (!data || data.length === 0) {
                    console.error('No data available');
                    return;
                }

                const labels = ['Part Number Count'];
                const maxCapacity = 160;

                // Hitung jumlah kemunculan setiap Part Number
                const partNumberCounts = data.reduce((acc, item) => {
                    if (acc[item.Part_Number]) {
                        acc[item.Part_Number]++;
                    } else {
                        acc[item.Part_Number] = 1;
                    }
                    return acc;
                }, {});

                const totalPartNumberCount = Object.values(partNumberCounts).reduce((acc, count) => acc + count, 0);
                const ratio = ((totalPartNumberCount / maxCapacity) * 100).toFixed(2);

                // Update ratio text
                document.getElementById('ratioText').textContent = `Ratio: ${ratio}%`;

                // Populate top 5 parts
                const top5Parts = Object.entries(partNumberCounts)
                    .sort((a, b) => b[1] - a[1])
                    .slice(0, 5);

                const top5PartsContainer = document.getElementById('top5Parts');
                top5Parts.forEach(([partNumber, count]) => {
                    const row = document.createElement('tr');
                    const partNumberCell = document.createElement('td');
                    partNumberCell.textContent = partNumber;
                    const totalQtyCell = document.createElement('td');
                    totalQtyCell.textContent = count;
                    row.appendChild(partNumberCell);
                    row.appendChild(totalQtyCell);
                    top5PartsContainer.appendChild(row);
                });

                // Chart.js setup
                const ctx = document.getElementById('stockChart').getContext('2d');
                const stockChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Part Number Count',
                            data: [totalPartNumberCount],
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                enabled: false
                            },
                            legend: {
                                display: false
                            },
                            datalabels: {
                                color: 'black',
                                anchor: 'end',
                                align: 'start', // Align labels to start, so they appear inside the bar
                                formatter: (value) => value,
                                font: {
                                    size: 16
                                },
                                padding: 4 // Adjust padding to fit the label inside the bar
                            },
                            annotation: {
                                annotations: {
                                    line1: {
                                        type: 'line',
                                        yMin: maxCapacity,
                                        yMax: maxCapacity,
                                        borderColor: 'blue',
                                        borderWidth: 3,
                                        label: {
                                            content: 'Max Capacity Pallets',
                                            enabled: true,
                                            position: 'start',
                                            backgroundColor: '#606676',
                                            color: 'white',
                                            font: {
                                                size: 16,
                                                weight: 'bold'
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: 'black'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: maxCapacity,
                                ticks: {
                                    color: 'black'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error)); // Log fetch errors
    });
    </script>
</body>
</html>
