<?php
require 'koneksi.php';
$database = new Database();
$conn = $database->connect();

// Initialize count variables
$trainingCount = 0;
$testingCount = 0;

$message_submit = "";
$message_delete = "";

// Fetch current counts from the database
function fetchCounts($conn)
{
    $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM data_preprocessing");
    $totalResult = $totalQuery->fetch(PDO::FETCH_ASSOC);
    $totalCount = $totalResult['total'];
    global $trainingCount, $testingCount;
    $trainingCount = round($totalCount * 0.8);
    $testingCount = round($totalCount * 0.2);
}

fetchCounts($conn);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['splitData'])) {
        // Calculate training and testing counts
        $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM data_preprocessing");
        $totalResult = $totalQuery->fetch(PDO::FETCH_ASSOC);
        $totalCount = $totalResult['total'];
        $trainingCount = round($totalCount * 0.8);
        $testingCount = round($totalCount * 0.2);

        // Truncate existing tables
        $conn->exec("TRUNCATE TABLE data_training");
        $conn->exec("TRUNCATE TABLE data_testing");

        // Insert data into data_training
        $conn->exec("INSERT INTO data_training (id_training, real_text, label) 
                     SELECT id, teks, label 
                     FROM data_preprocessing 
                     ORDER BY RAND() LIMIT $trainingCount");

        // Insert data into data_testing
        $conn->exec("INSERT INTO data_testing (id_testing, real_text, label) 
                     SELECT id, teks, label 
                     FROM data_preprocessing 
                     WHERE id NOT IN (SELECT id_training FROM data_training)");

        $message_submit = "Data berhasil di split";

        fetchCounts($conn);  // Update counts after the operation
    } elseif (isset($_POST['delete_all'])) {
        // Perform delete operation
        $conn->exec("TRUNCATE TABLE data_training");
        $conn->exec("TRUNCATE TABLE data_testing");
        $trainingCount = 0;
        $testingCount = 0;
        $message_delete = "Semua data berhasil dihapus.";
    }
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
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
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
                        <!-- Card Header -->
                        <div class="card-header">
                            <h4 class="card-title">Split Data</h4>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <form action="" method="post">
                                        <button type="submit" class="btn btn-primary w-100" name="splitData">Split Data</button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form method="post">
                                        <button type="submit" class="btn btn-danger w-100" name="delete_all" onclick="return confirm('Apakah Anda yakin ingin menghapus semua data?');">Hapus Data Latih dan Uji</button>
                                    </form>
                                </div>
                            </div>
                            <?php if (!empty($message_delete)) : ?>
                                <div class="alert alert-info mt-2"><?php echo $message_delete; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($message_submit)) : ?>
                                <div class="alert alert-info mt-2"><?php echo $message_submit; ?></div>
                            <?php endif; ?>
                            <div class="container mt-5">
                                <div class="row">
                                    <!-- Training Data Card -->
                                    <div class="col-md-6">
                                        <div class="card text-center">
                                            <div class="card-body card-horizontal">
                                                <div class="mr-4">
                                                    <h4 class="card-title">Jumlah Data Latih</h4>
                                                    <h2 class="display-4"><?= $trainingCount ?></h2>
                                                    <div class="card-body-icon">
                                                        <i class="fa-solid fa-dumbbell"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Testing Data Card -->
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body card-horizontal">
                                                <div class="mr-4">
                                                    <h4 class="card-title">Jumlah Data Uji</h4>
                                                    <h2 class="display-4"><?= $testingCount ?></h2>
                                                    <div class="card-body-icon">
                                                        <i class="fas fa-vial"></i>
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



</body>

</html>