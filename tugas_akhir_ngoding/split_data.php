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

function splitData($conn)
{
    $stmtNonHoax = $conn->prepare("SELECT id, teks, label FROM data_preprocessing WHERE label='non-hoax'");
    $stmtNonHoax->execute();
    $resultNonHoax = $stmtNonHoax->fetchAll(PDO::FETCH_ASSOC);
    $countNonHoax = $stmtNonHoax->rowCount();

    $stmtHoax = $conn->prepare("SELECT id, teks, label FROM data_preprocessing WHERE label='hoax' ORDER BY RAND() LIMIT :countNonHoax");
    $stmtHoax->bindParam(':countNonHoax', $countNonHoax, PDO::PARAM_INT);
    $stmtHoax->execute();
    $resultHoax = $stmtHoax->fetchAll(PDO::FETCH_ASSOC);

    $conn->exec("TRUNCATE TABLE data_training");
    $conn->exec("TRUNCATE TABLE data_testing");

    $data = array_merge($resultNonHoax, $resultHoax);
    shuffle($data);

    $splitPoint = round(0.8 * count($data));
    $stmtTraining = $conn->prepare("INSERT INTO data_training (real_text, clean_text, label) VALUES (?, ?, ?)");
    $stmtTesting = $conn->prepare("INSERT INTO data_testing (real_text, clean_text, label) VALUES (?, ?, ?)");

    foreach ($data as $index => $row) {
        if ($index < $splitPoint) {
            $stmtTraining->execute([$row['teks'], $row['teks'], $row['label']]);
        } else {
            $stmtTesting->execute([$row['teks'], $row['teks'], $row['label']]);
        }
    }
    return "Data traning dan testing berhasil di split";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['splitData'])) {
        if (isTableEmpty($conn, "data_preprocessing")) {
            $alert_message = "Data preprocessing kosong, tidak dapat melakukan split data.";
        } else {
            $message_submit = splitData($conn);
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
                            <div class="row mb-5">
                                <div class="col-6">
                                    <form action="" method="post">
                                        <button type="submit" class="btn btn-primary w-100" name="splitData">Split Data</button>
                                    </form>
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
                            <?php if (!empty($message_delete)) : ?>
                                <div class="alert alert-info mt-2"><?php echo $message_delete; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($message_submit)) : ?>
                                <div class="alert alert-info mt-2"><?php echo $message_submit; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($alert_message)) : ?>
                                <div class="alert alert-warning mt-2"><?php echo $alert_message; ?></div>
                            <?php endif; ?>
                            <div class="container mt-5">
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

    <!-- Page level custom scripts -->
    <script src="datatables/datatables-demo.js"></script>
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

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            border: none;
            /* Remove border */
        }
    </style>
    <script>
        function confirmDelete() {
            $('#deleteModal').modal('show');
        }
    </script>



</body>

</html>