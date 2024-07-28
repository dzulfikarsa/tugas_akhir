<?php
require 'koneksi.php';
$database = new Database();
$conn = $database->connect();

// Initialize count variables
$trainingCount = 0;
$testingCount = 0;

$message_submit = "";
$message_delete = "";
$alert_message = "";

function countDataPreprocessing($conn)
{
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM data_preprocessing");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function countDataHoaxAndNonHoaxTrainingTesting($conn)
{
    $stmt = $conn->query("SELECT 'total' AS source, 
    (SELECT COUNT(*) FROM data_training WHERE label = 'Non-Hoax') + 
    (SELECT COUNT(*) FROM data_testing WHERE label = 'Non-Hoax') AS total;");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalNonHoaxSesudah = $result['total'];

    $stmt = $conn->query("SELECT 'total' AS source, 
    (SELECT COUNT(*) FROM data_training WHERE label = 'Hoax') + 
    (SELECT COUNT(*) FROM data_testing WHERE label = 'Hoax') AS total;");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalHoaxSesudah = $result['total'];

    return array('non_hoax' => $totalNonHoaxSesudah, 'hoax' => $totalHoaxSesudah);
}

function countDataSetelahSplit($conn)
{
    $stmt = $conn->query("SELECT 'total' AS source, 
    (SELECT COUNT(*) FROM data_training) + 
    (SELECT COUNT(*) FROM data_testing) AS total;");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function countDataTraining($conn)
{
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM data_training");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function countDataTesting($conn)
{
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM data_testing");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function isTableEmpty($conn, $tableName)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM " . $tableName);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] == 0;
}

function splitData($conn, $rasio)
{
    $seed = 9447; #check point akurasi 
    srand($seed);
    $stmtPreprocessing = $conn->prepare("SELECT id_preprocessing, teks, label FROM data_preprocessing ORDER BY RAND($seed)");
    $stmtPreprocessing->execute();
    $resultPreprocessing = $stmtPreprocessing->fetchAll(PDO::FETCH_ASSOC);

    $conn->exec("TRUNCATE TABLE data_training");
    $conn->exec("TRUNCATE TABLE data_testing");

    $splitPoint = round($rasio * count($resultPreprocessing));
    $stmtTraining = $conn->prepare("INSERT INTO data_training (id_training, real_text, clean_text, label) VALUES (?, ?, ?, ?)");
    $stmtTesting = $conn->prepare("INSERT INTO data_testing (id_testing, real_text, clean_text, label) VALUES (?, ?, ?, ?)");

    foreach ($resultPreprocessing as $index => $row) {
        if ($index < $splitPoint) {
            $stmtTraining->execute([$row['id_preprocessing'], $row['teks'], $row['teks'], $row['label']]);
        } else {
            $stmtTesting->execute([$row['id_preprocessing'], $row['teks'], $row['teks'], $row['label']]);
        }
    }
    return "Data traning dan testing berhasil di split";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['splitData'])) {
        $rasio = $_POST['split_ratio'];
        if (isTableEmpty($conn, "data_preprocessing")) {
            $alert_message = "Data preprocessing kosong, tidak dapat melakukan split data.";
        } else {
            $message_submit = splitData($conn, $rasio);
            $trainingCount = countDataTraining($conn);
            $testingCount = countDataTesting($conn);
        }
    } elseif (isset($_POST['delete_all'])) {
        if (isTableEmpty($conn, "data_training") && isTableEmpty($conn, "data_testing")) {
            $alert_message = "Tidak ada data latih dan uji untuk dihapus.";
        } else {
            // Perform delete operation
            $conn->exec("TRUNCATE TABLE data_training");
            $conn->exec("TRUNCATE TABLE data_testing");
            $trainingCount = 0;
            $testingCount = 0;
            $message_delete = "Semua data latih dan uji berhasil dihapus.";
        }
    }
}

// Refresh counts if not a POST or no form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || (empty($message_submit) && empty($message_delete))) {
    $trainingCount = countDataTraining($conn);
    $testingCount = countDataTesting($conn);
}

$jumlahDataPreprocessing = countDataPreprocessing($conn);
$jumlahDataSetelahSplit = countDataSetelahSplit($conn);
// Menghitung jumlah total data preprocessing dan training
$trainingCount = countDataTraining($conn);
$testingCount = countDataTesting($conn);

// Mendefinisikan nilai rasio default jika tidak ada data training
$defaultRatio = '0.8';

// Jika training count adalah 0 atau tidak ada data preprocessing, gunakan rasio default
if ($trainingCount == 0 || $testingCount == 0) {
    $selected_ratio = $defaultRatio;
} else {
    // Menghitung rasio training dari total data
    $totalData = $trainingCount + $testingCount;
    $computedRatio = $trainingCount / $totalData;

    // Mendefinisikan nilai rasio yang mungkin
    $ratios = [
        '0.9' => 0.9,
        '0.8' => 0.8,
        '0.7' => 0.7,
        '0.6' => 0.6,
        '0.5' => 0.5
    ];

    // Menentukan rasio yang paling mendekati hasil perhitungan
    $closest = null;
    $closestDistance = PHP_FLOAT_MAX;
    foreach ($ratios as $key => $ratio) {
        $distance = abs($computedRatio - $ratio);
        if ($distance < $closestDistance) {
            $closest = $key;
            $closestDistance = $distance;
        }
    }

    $selected_ratio = $closest;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, AdminWrap lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, AdminWrap lite design, AdminWrap lite dashboard bootstrap 5 dashboard template">
    <meta name="description" content="AdminWrap Lite is powerful and clean admin dashboard template, inpired from Bootstrap Framework">
    <meta name="robots" content="noindex,nofollow">
    <title>Deteksi Hoax</title>
    <link rel="canonical" href="https://www.wrappixel.com/templates/adminwrap-lite/" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon.png">
    <!-- Bootstrap Core CSS -->
    <link href="assets/node_modules/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/node_modules/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <!--c3 CSS -->
    <link href="assets/node_modules/c3-master/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="assets/css/pages/dashboard1.css" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="assets/css/colors/default.css" id="theme" rel="stylesheet">
    <style>
        .card-horizontal {
            display: flex;
            flex: 1 1 auto;
            position: relative;
            padding-right: 50px;
            /* Padding to ensure text does not overlap icon */
            border-radius: 0.25rem;
            /* Rounded corners matching Bootstrap's style */
        }

        .card-body-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            font-size: 24px;
            /* Normal icon size */
        }

        .bold-text {
            font-weight: bold;
            /* Bold font for the numbers */
        }
    </style>
</head>

<body class="fix-header fix-sidebar card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Deteksi Hoax</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.html">
                        <h2>Deteksi Hoax</h2>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up waves-effect waves-dark" href="javascript:void(0)"><i class="fa fa-bars"></i></a> </li>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="#" id="navbarDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="hidden-md-down">Text Mining - Deteksi Hoax &nbsp;</span> </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown"></ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
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
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">Split Data</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                            <li class="breadcrumb-item active">Split Data</li>
                        </ol>
                    </div>
                </div>

                <div class="container mt-5">
                    <!-- Card Container -->
                    <div class="card">
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-custom shadow rounded-3">
                                        <div class="card-body-custom p-3">
                                            <h6 class="card-title card-header-custom m-0">Jumlah Data</h6>
                                            <h2 class="display-4"><?= $jumlahDataPreprocessing ?></h2>
                                            <div class="card-body-icon">
                                                <i class="fa-solid fa-database"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <button form="dataSplitForm" type="submit" class="btn btn-primary w-100" name="splitData">Split Data</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">Hapus Semua Data</button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus semua data?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form method="post">
                                                        <button type="submit" class="btn btn-danger" name="delete_all">Hapus Semua Data</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row mb-4'>
                                <div>
                                    <form id='dataSplitForm' action='' method='post'>
                                        <label for='split-ratio'>Pilih Rasio Pembagian Data Training dan Testing. Nilai default adalah 80:20</label>
                                        <select class='form-select' id='split-ratio' name='split_ratio'>
                                            <?php
                                            $options = ['0.9' => '90:10', '0.8' => '80:20', '0.7' => '70:30', '0.6' => '60:40', '0.5' => '50:50'];
                                            foreach ($options as $value => $label) {
                                                $isSelected = ($selected_ratio == $value) ? 'selected' : '';
                                                echo "<option value='{$value}' {$isSelected}>{$label} (Training : Test)</option>";
                                            }
                                            ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                            <?php if (!empty($message_delete)) : ?>
                                <div class="alert alert-info mt-2"><?php echo $message_delete; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($message_submit)) : ?>
                                <div class="alert alert-info mt-2"><?php echo $message_submit; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($alert_message)) : ?>
                                <div class="alert alert-warning mt-2"><?php echo $alert_message; ?></div>
                            <?php endif; ?>

                            <div class="row">
                                <!-- Training Data Card -->
                                <div class="col-md-6">
                                    <div class="card card-custom shadow rounded-3">
                                        <div class="card-body-custom p-3">
                                            <h6 class="card-title card-header-custom m-0">Jumlah Data Training</h6>
                                            <h2 class="display-4"><?= $trainingCount ?></h2>
                                            <div class="card-body-icon">
                                                <i class="fa-solid fa-dumbbell"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Testing Data Card -->
                                <div class="col-md-6">
                                    <div class="card card-custom shadow rounded-3">
                                        <div class="card-body-custom p-3">
                                            <h6 class="card-title card-header-custom m-0">Jumlah Data Testing</h6>
                                            <h2 class="display-4"><?= $testingCount ?></h2>
                                            <div class="card-body-icon">
                                                <i class="fa-solid fa-vial"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <canvas id="dataChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        <footer class="footer"> Tugas Akhir - Dzulfikar Saif Assalam</footer>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="assets/node_modules/jquery/jquery.min.js"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="assets/node_modules/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="assets/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="assets/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="assets/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="assets/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!--morris JavaScript -->
    <script src="assets/node_modules/raphael/raphael-min.js"></script>
    <script src="assets/node_modules/morrisjs/morris.min.js"></script>
    <!--c3 JavaScript -->
    <script src="assets/node_modules/d3/d3.min.js"></script>
    <script src="assets/node_modules/c3-master/c3.min.js"></script>
    <!-- Chart JS -->
    <script src="assets/js/dashboard1.js"></script>
    <script src="https://kit.fontawesome.com/32266cf13d.js" crossorigin="anonymous"></script>

    <!-- Page level plugins -->
    <script src="datatables/jquery.dataTables.min.js"></script>
    <script src="datatables/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Page level custom scripts -->
    <script src="datatables/datatables-demo.js"></script>

    <script>
        function confirmDelete() {
            $('#deleteModal').modal('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('dataChart').getContext('2d');
            const dataChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Data Training', 'Data Testing'],
                    datasets: [{
                        label: 'Jumlah Data',
                        data: [<?= $trainingCount ?>, <?= $testingCount ?>],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
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
        });
    </script>



</body>

</html>