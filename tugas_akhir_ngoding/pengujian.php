<?php
require 'koneksi.php'; // Assumes 'koneksi.php' sets up the database connection

// Set default value for $show_card and initialize $message_success
$show_card = false;
$message_failed = "";
$results = [];

// Mengecek apakah file model.json ada
$file_path = 'prediction_results.json';  // Sesuaikan path sesuai lokasi file JSON Anda
if (file_exists($file_path)) {
    $results = json_decode(file_get_contents($file_path), true);
    $show_card = true;
}

// Process the event when the "Mulai" button is clicked
if (isset($_POST['mulai'])) {
    $show_card = true;  // Tampilkan card
    // Menjalankan skrip Python dan menangkap output
    $output = shell_exec("python C:\\xampp\\htdocs\\tugas_akhir\\tugas_akhir_ngoding\\util\\pengujian.py");
    // Decode output JSON menjadi array PHP

    if (file_exists($file_path)) {
        $results = json_decode(file_get_contents($file_path), true);
        $show_card = true;
    }
}

function getStatus($actual, $predicted)
{
    // Normalize the labels to lower case and trim any extra spaces
    $actual = strtolower(trim($actual));
    $predicted = strtolower(trim($predicted));

    if ($actual == 'hoax' && $predicted == 'hoax') {
        return '<span class="badge bg-success">TP (True Positive)</span>';
    } elseif ($actual == 'non-hoax' && $predicted == 'hoax') {
        return '<span class="badge bg-danger">FP (False Positive)</span>';
    } elseif ($actual == 'non-hoax' && $predicted == 'non-hoax') {
        return '<span class="badge bg-success">TN (True Negative)</span>';
    } elseif ($actual == 'hoax' && $predicted == 'non-hoax') {
        return '<span class="badge bg-danger">FN (False Negative)</span>';
    } else {
        return '<span class="badge bg-secondary">Unknown</span>';
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
        table.table td:nth-child(5) {
            font-size: 17px;
            /* Increase font size for status column */
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
                        <li> <a class="waves-effect waves-dark" href="balancing.php" aria-expanded="false">
                                <i class="fa-solid fa-scale-balanced"></i><span class="hide-menu">Balancing</span></a>
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
                        <li> <a class="waves-effect waves-dark" href="demo_model.php" aria-expanded="false">
                                <i class="fa-solid fa-wand-magic-sparkles"></i><span class="hide-menu">Demo Model</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">Pengujian</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                            <li class="breadcrumb-item active">Pengujian</li>
                        </ol>
                    </div>
                </div>
                <div class="container mt-5">
                    <form action="" method="post" style="margin-bottom: 20px;">
                        <button type="submit" class="btn btn-primary" name="mulai">Mulai</button>
                    </form>

                    <!-- Conditional display of card content after data submission -->
                    <?php if ($show_card) : ?>
                        <div class="card">
                            <div class="card-body">
                                <?php if (!empty($results)) : ?>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Teks</th>
                                                <th>Label Asli</th>
                                                <th>Label Prediksi</th>
                                                <th>Status</th> <!-- New column for status -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($results as $row) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                                    <td><?= htmlspecialchars($row['real_text']) ?></td>
                                                    <td><?= htmlspecialchars($row['label']) ?></td>
                                                    <td><?= htmlspecialchars($row['predicted_label']) ?></td>
                                                    <td><?= getStatus($row['label'], $row['predicted_label']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else : ?>
                                    <p><?= $message_failed ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
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


</body>

</html>