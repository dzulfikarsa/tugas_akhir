<?php
require 'koneksi.php'; // Assumes 'koneksi.php' sets up the database connection


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
            font-weight: bold;
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
                            <div class="col-6">
                                <table class="table table-bordered text-center">
                                    <tr>
                                        <td colspan="2" rowspan="2" class="align-middle">
                                            Data Training = 6756
                                            <br>
                                            Data Testing = 1689
                                        </td>
                                        <td colspan="2" class="align-middle">Kelas Aktual</td>
                                    </tr>
                                    <tr>
                                        <td>Hoax</td>
                                        <td>Non-Hoax</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2" class="align-middle">Kelas Prediksi</td>
                                        <td>Hoax</td>
                                        <td>TP = <span id="tp"></span></td>
                                        <td>FN = <span id="fn"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Non-Hoax</td>
                                        <td>FP = <span id="fp"></span></td>
                                        <td>TN = <span id="tn"></span></td>
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


                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-custom shadow-sm rounded">
                                            <div class="card-body-custom">
                                                <h6 class="card-title card-header-custom m-0">Total Prediksi Hoax</h6>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="card-body-custom m-0">1180</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card card-custom shadow-sm rounded">
                                            <div class="card-body-custom">
                                                <h6 class="card-title card-header-custom m-0">Total Prediksi Non-Hoax</h6>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="card-body-custom m-0">509</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        function loadData() {
            fetch('confusion_matrix.json')
                .then(response => response.json())
                .then(data => {
                    const TP = data["TP (True Positive)"];
                    const TN = data["TN (True Negative)"];
                    const FP = data["FP (False Positive)"];
                    const FN = data["FN (False Negative)"];

                    document.getElementById('tp').innerHTML = TP;
                    document.getElementById('fn').innerHTML = FN;
                    document.getElementById('fp').innerHTML = FP;
                    document.getElementById('tn').innerHTML = TN;

                    // Calculate metrics
                    const accuracy = ((TP + TN) / (TP + TN + FP + FN)).toFixed(3);
                    const precision = (TP / (TP + FP)).toFixed(3);
                    const recall = (TP / (TP + FN)).toFixed(3);

                    // Set formula LaTeX
                    document.getElementById('accuracy_formula').innerText = `\\(\\frac{TP + TN}{TP + TN + FP + FN}\\)`;
                    document.getElementById('accuracy_breakdown').innerText = `\\(\\frac{${TP} + ${TN}}{${TP} + ${TN} + ${FP} + ${FN}}\\)`;
                    document.getElementById('accuracy_result').innerHTML = `= ${accuracy}`;

                    document.getElementById('precision_formula').innerText = `\\(\\frac{TP}{TP + FP}\\)`;
                    document.getElementById('precision_breakdown').innerText = `\\(\\frac{${TP}}{${TP} + ${FP}}\\)`;
                    document.getElementById('precision_result').innerHTML = `= ${precision}`;

                    document.getElementById('recall_formula').innerText = `\\(\\frac{TP}{TP + FN}\\)`;
                    document.getElementById('recall_breakdown').innerText = `\\(\\frac{${TP}}{${TP} + ${FN}}\\)`;
                    document.getElementById('recall_result').innerHTML = `= ${recall}`;

                    // Ensure MathJax updates the display
                    MathJax.typesetPromise();
                })
                .catch(error => console.error('Error loading the data:', error));
        }

        window.onload = loadData;
    </script>
</body>

</html>