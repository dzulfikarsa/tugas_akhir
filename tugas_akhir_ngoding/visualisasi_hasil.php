<?php
require 'koneksi.php'; // Pastikan 'koneksi.php' menciptakan koneksi PDO
$database = new Database();
$conn = $database->connect();

// Mengambil jumlah total data training dengan PDO
$queryTraining = "SELECT COUNT(*) as totalTraining FROM data_training";
$stmtTraining = $conn->prepare($queryTraining);
$stmtTraining->execute();
$rowTraining = $stmtTraining->fetch(PDO::FETCH_ASSOC);
$totalTraining = $rowTraining['totalTraining'];

// Mengambil jumlah total data testing dengan PDO
$queryTesting = "SELECT COUNT(*) as totalTesting FROM data_testing";
$stmtTesting = $conn->prepare($queryTesting);
$stmtTesting->execute();
$rowTesting = $stmtTesting->fetch(PDO::FETCH_ASSOC);
$totalTesting = $rowTesting['totalTesting'];



$jsonFilePath = 'confusion_matrix.json';

if (file_exists($jsonFilePath)) {
    $jsonData = file_get_contents($jsonFilePath);
    $data = json_decode($jsonData, true); // Decoding the JSON as an associative array

    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = null; // Set to null if there's a decoding error
    }
} else {
    $data = null; // Set to null if the file does not exist
}

echo "<script>var confusionMatrix = " . json_encode($data) . ";</script>";


// Load JSON file
$jsonData = file_get_contents('prediction_results.json');
// Decode JSON data into PHP array
$data = json_decode($jsonData, true);

// Initialize counters
$totalHoax = 0;
$totalNonHoax = 0;

// Iterate through each item and count predictions
foreach ($data as $item) {
    $predictedLabel = strtolower($item['predicted_label']); // Convert label to lowercase
    if ($predictedLabel === 'hoax') {
        $totalHoax++;
    } elseif ($predictedLabel === 'non-hoax') {
        $totalNonHoax++;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, AdminWrap lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, AdminWrap lite design, AdminWrap lite dashboard bootstrap 5 dashboard template">
    <meta name="description" content="AdminWrap Lite is powerful and clean admin dashboard template, inpired from Bootstrap Framework">
    <meta name="robots" content="noindex,nofollow">
    <title>Deteksi Hoax</title>
    <link rel="canonical" href="https://www.wrappixel.com/templates/adminwrap-lite/" />
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon.png">
    <link href="assets/node_modules/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/node_modules/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <link href="assets/node_modules/c3-master/c3.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/pages/dashboard1.css" rel="stylesheet">
    <link href="assets/css/colors/default.css" id="theme" rel="stylesheet">
    <style>
        .card-custom {
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }

        .card-header-custom {
            background-color: transparent;
            border-bottom: none;
            color: #333;
        }

        .card-body-custom {
            font-size: 36px;
        }

        .card-icon {
            font-size: 24px;
        }

        .stats {
            font-family: 'Cambria', 'Times New Roman', serif;
            font-size: 18px;
            color: #333;
            line-height: 2;
        }

        .formula {
            margin-bottom: 20px;
            background: #f9f9f9;
            border-left: 5px solid #007BFF;
            padding: 10px;
        }

        .formula em {
            font-size: 20px;
            font-style: italic;
        }

        .calculation,
        .inline-block {
            display: inline-block;
            margin-right: 10px;
        }

        .breakdown,
        .result {
            font-size: 16px;
            margin-left: 5px;
        }

        .inline-block {
            vertical-align: middle;
        }

        .custom-spacing {
            padding-left: 50px;
            /* Jarak kiri */
            padding-right: 50px;
            /* Jarak kanan */
        }
    </style>
</head>

<body class="fix-header fix-sidebar card-no-border">
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Deteksi Hoax</p>
        </div>
    </div>
    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.html">
                        <h2>Deteksi Hoax</h2>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up waves-effect waves-dark" href="javascript:void(0)"><i class="fa fa-bars"></i></a> </li>
                    </ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="#" id="navbarDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="hidden-md-down">Text Mining - Deteksi Hoax &nbsp;</span> </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown"></ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li> <a class="waves-effect waves-dark" href="index.php" aria-expanded="false"><i class="fa fa-tachometer"></i><span class="hide-menu">Beranda</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="import_data.php" aria-expanded="false">
                                <i class="fa-solid fa-file-import"></i><span class="hide-menu">Import Data</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="preprocessing.php" aria-expanded="false">
                                <i class="fa-solid fa-gear"></i><span class="hide-menu">Preprocessing</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="labelling.php" aria-expanded="false">
                                <i class="fa-solid fa-tags"></i><span class="hide-menu">Labelling</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="split_data.php" aria-expanded="false">
                                <i class="fa-solid fa-scissors"></i><span class="hide-menu">Split Data</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="modelling.php" aria-expanded="false">
                                <i class="fa-solid fa-code-compare"></i><span class="hide-menu">Modelling</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="pengujian.php" aria-expanded="false">
                                <i class="fa-solid fa-flask-vial"></i><span class="hide-menu">Pengujian</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="visualisasi_hasil.php" aria-expanded="false">
                                <i class="fa-solid fa-chart-column"></i><span class="hide-menu">Visualisasi Hasil</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="container-fluid row">
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">Visualisasi Hasil</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                            <li class="breadcrumb-item active">Visualisasi Hasil</li>
                        </ol>
                    </div>
                </div>
                <div class="container mt-5">
                    <div class="card">
                        <div class="card-body row">
                            <div class="col-6 custom-spacing">
                                <table class="table table-bordered text-center border border-dark">
                                    <tr>
                                        <td colspan="2" rowspan="2" class="align-middle fw-bold border border-dark">
                                            Data Training = <?php echo $totalTraining; ?>
                                            <br>
                                            Data Testing = <?php echo $totalTesting; ?>
                                        </td>
                                        <td colspan="2" class="align-middle fw-bold border border-dark">Kelas Aktual</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold border border-dark">Hoax</td>
                                        <td class="fw-bold border border-dark">Non-Hoax</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2" class="align-middle fw-bold border border-dark">Kelas Prediksi</td>
                                        <td class="fw-bold border border-dark">Hoax</td>
                                        <td class="fw-bold border border-dark">TP = <span id="tp"></span></td>
                                        <td class="fw-bold border border-dark">FN = <span id="fn"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold border border-dark">Non-Hoax</td>
                                        <td class="fw-bold border border-dark">FP = <span id="fp"></span></td>
                                        <td class="fw-bold border border-dark">TN = <span id="tn"></span></td>
                                    </tr>
                                </table>
                                <!-- Displaying the formulas and results -->
                                <div class="stats">
                                    <p class="formula">
                                        <em>Accuracy = </em>
                                        <span class="calculation" id="accuracy_formula"></span>
                                        <br>
                                        <span class="inline-block" id="accuracy_details">
                                            <em>Accuracy = </em>
                                            <span class="breakdown" id="accuracy_breakdown"></span>
                                            <span class="result" id="accuracy_result"></span>
                                        </span>
                                    </p>
                                    <p class="formula">
                                        <em>Precision = </em>
                                        <span class="calculation" id="precision_formula"></span>
                                        <br>
                                        <span class="inline-block" id="precision_details">
                                            <em>Precision = </em>
                                            <span class="breakdown" id="precision_breakdown"></span>
                                            <span class="result" id="precision_result"></span>
                                        </span>
                                    </p>
                                    <p class="formula">
                                        <em>Recall = </em>
                                        <span class="calculation" id="recall_formula"></span>
                                        <br>
                                        <span class="inline-block" id="recall_details">
                                            <em>Recall = </em>
                                            <span class="breakdown" id="recall_breakdown"></span>
                                            <span class="result" id="recall_result"></span>
                                        </span>
                                    </p>
                                </div>
                                <div class="fw-normal">
                                    <h4 class="fw-bold">Keterangan:</h4>
                                    <ul>
                                        <li><strong>TP (True Positive):</strong><br> Kelas hasil prediksi dan kelas asli sama-sama hoax.</li>
                                        <br>
                                        <li><strong>FP (False Positive):</strong><br>Kelas hasil prediksi hoax dan kelas asli non-hoax.</li>
                                        <br>
                                        <li><strong>TN (True Negative):</strong><br>Kelas hasil prediksi dan kelas asli sama-sama non-hoax.</li>
                                        <br>
                                        <li><strong>FN (False Negative):</strong><br>Kelas hasil prediksi non-hoax dan kelas asli hoax.</li>
                                    </ul>
                                </div>

                            </div>
                            <div class="col-6 custom-spacing">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-custom shadow rounded-3">
                                            <div class="card-body-custom p-2">
                                                <h6 class="card-title card-header-custom m-0">Total Prediksi Hoax</h6>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="card-body-custom m-0"><?php echo $totalHoax; ?></p>
                                                    <i class="fa-solid fa-exclamation-triangle " style="color: #FFD43B;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card card-custom shadow rounded-3">
                                            <div class="card-body-custom p-2">
                                                <h6 class="card-title card-header-custom m-0">Total Prediksi Non-Hoax</h6>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="card-body-custom m-0"><?php echo $totalNonHoax; ?></p>
                                                    <i class="fa-solid fa-check-circle" style="color:#b8e0d2;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <canvas id="predictionChart"></canvas>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <footer class="footer"> Tugas Akhir - Dzulfikar Saif Assalam</footer>
    </div>
    </div>
    <script src="assets/node_modules/jquery/jquery.min.js"></script>
    <script src="assets/node_modules/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/custom.min.js"></script>
    <script src="assets/node_modules/raphael/raphael-min.js"></script>
    <script src="assets/node_modules/morrisjs/morris.min.js"></script>
    <script src="assets/node_modules/d3/d3.min.js"></script>
    <script src="assets/node_modules/c3-master/c3.min.js"></script>
    <script src="assets/js/dashboard1.js"></script>
    <script src="https://kit.fontawesome.com/32266cf13d.js" crossorigin="anonymous"></script>
    <script src="datatables/jquery.dataTables.min.js"></script>
    <script src="datatables/dataTables.bootstrap4.min.js"></script>
    <script src="datatables/datatables-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        // function loadData() {
        //     fetch('confusion_matrix.json')
        //         .then(response => response.json())
        //         .then(data => {
        //             const TP = data["TP (True Positive)"];
        //             const TN = data["TN (True Negative)"];
        //             const FP = data["FP (False Positive)"];
        //             const FN = data["FN (False Negative)"];
        //             console.log(TP);
        //             document.getElementById('tp').innerHTML = TP;
        //             document.getElementById('fn').innerHTML = FN;
        //             document.getElementById('fp').innerHTML = FP;
        //             document.getElementById('tn').innerHTML = TN;

        //             // Calculate metrics
        //             const accuracy = ((TP + TN) / (TP + TN + FP + FN)).toFixed(3);
        //             const precision = (TP / (TP + FP)).toFixed(3);
        //             const recall = (TP / (TP + FN)).toFixed(3);

        //             // Set formula LaTeX
        //             document.getElementById('accuracy_formula').innerText = `\\(\\frac{TP + TN}{TP + TN + FP + FN}\\)`;
        //             document.getElementById('accuracy_breakdown').innerText = `\\(\\frac{${TP} + ${TN}}{${TP} + ${TN} + ${FP} + ${FN}}\\)`;
        //             document.getElementById('accuracy_result').innerHTML = `= ${accuracy}`;

        //             document.getElementById('precision_formula').innerText = `\\(\\frac{TP}{TP + FP}\\)`;
        //             document.getElementById('precision_breakdown').innerText = `\\(\\frac{${TP}}{${TP} + ${FP}}\\)`;
        //             document.getElementById('precision_result').innerHTML = `= ${precision}`;

        //             document.getElementById('recall_formula').innerText = `\\(\\frac{TP}{TP + FN}\\)`;
        //             document.getElementById('recall_breakdown').innerText = `\\(\\frac{${TP}}{${TP} + ${FN}}\\)`;
        //             document.getElementById('recall_result').innerHTML = `= ${recall}`;

        //             // Ensure MathJax updates the display
        //             MathJax.typesetPromise();
        //         })
        //         .catch(error => console.error('Error loading the data:', error));
        // }

        document.addEventListener('DOMContentLoaded', function() {
            if (confusionMatrix) {
                const TP = confusionMatrix["TP (True Positive)"];
                const TN = confusionMatrix["TN (True Negative)"];
                const FP = confusionMatrix["FP (False Positive)"];
                const FN = confusionMatrix["FN (False Negative)"];
                const accuracy = confusionMatrix["Accuracy"];
                const precision = confusionMatrix["Precision"];
                const recall = confusionMatrix["Recall"];

                // Update the DOM elements with LaTeX syntax
                document.getElementById('accuracy_formula').innerText = `\\(\\frac{TP + TN}{TP + TN + FP + FN}\\)`;
                document.getElementById('accuracy_breakdown').innerText = `\\(\\frac{${TP} + ${TN}}{${TP} + ${TN} + ${FP} + ${FN}}\\)`;

                document.getElementById('precision_formula').innerText = `\\(\\frac{TP}{TP + FP}\\)`;
                document.getElementById('precision_breakdown').innerText = `\\(\\frac{${TP}}{${TP} + ${FP}}\\)`;

                document.getElementById('recall_formula').innerText = `\\(\\frac{TP}{TP + FN}\\)`;
                document.getElementById('recall_breakdown').innerText = `\\(\\frac{${TP}}{${TP} + ${FN}}\\)`;

                // Set the non-LaTeX content
                document.getElementById('tp').textContent = TP;
                document.getElementById('tn').textContent = TN;
                document.getElementById('fp').textContent = FP;
                document.getElementById('fn').textContent = FN;
                document.getElementById('accuracy_result').textContent = accuracy.toFixed(3);
                document.getElementById('precision_result').textContent = precision.toFixed(3);
                document.getElementById('recall_result').textContent = recall.toFixed(3);

                // Ask MathJax to typeset the updated page
                MathJax.typesetPromise();
            } else {
                console.error('No data available for confusion matrix.');
                // Optionally, update the DOM to reflect that no data is available
            }
        });



        function renderPredictionChart(totalHoax, totalNonHoax) {
            const ctx = document.getElementById('predictionChart').getContext('2d');
            const predictionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Hoax', 'Non-Hoax'],
                    datasets: [{
                        label: 'Hasil Prediksi',
                        data: [totalHoax, totalNonHoax],
                        backgroundColor: [
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            onClick: (e) => e.stopPropagation(),
                            labels: {
                                usePointStyle: false, // This disables the point style, i.e., the symbol
                                boxWidth: 0 // Set the width of the colored box to 0 to hide it
                            }
                        }
                    }
                }
            });
        }

        // Call this function with the actual data
        renderPredictionChart(<?php echo $totalHoax; ?>, <?php echo $totalNonHoax; ?>);

        window.onload = loadData;
    </script>
</body>

</html>