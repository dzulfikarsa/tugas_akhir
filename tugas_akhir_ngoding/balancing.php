<?php
include 'koneksi.php'; // Pastikan path ke file db.php benar
$database = new Database();
$conn = $database->connect();

$stmtDataTraining = "SELECT COUNT(*) AS total FROM data_training";
$stmt = $conn->prepare($stmtDataTraining);
$stmt->execute();
$totalDataTraining = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtDataTrainingHoax = "SELECT COUNT(*) AS total FROM data_training WHERE label = 'Hoax'";
$stmt = $conn->prepare($stmtDataTrainingHoax);
$stmt->execute();
$totalDataTrainingHoax = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtDataTrainingNonHoax = "SELECT COUNT(*) AS total FROM data_training WHERE label = 'Non-Hoax'";
$stmt = $conn->prepare($stmtDataTrainingNonHoax);
$stmt->execute();
$totalDataTrainingNonHoax = $stmt->fetch(PDO::FETCH_ASSOC);
$balancingDone = false;

if (isset($_POST['balancing'])) {
    if ($totalDataTrainingHoax > $totalDataTrainingNonHoax) {
        $jumlahLebih = $totalDataTrainingHoax['total'] - $totalDataTrainingNonHoax['total'];
        $stmtBalancingDataTrainingHoax = "DELETE dt FROM data_training dt
                                          JOIN (
                                            SELECT id_training
                                            FROM data_training
                                            WHERE label = 'Hoax'
                                            ORDER BY id_training DESC
                                            LIMIT $jumlahLebih
                                          ) sub ON dt.id_training = sub.id_training";
        $stmt = $conn->prepare($stmtBalancingDataTrainingHoax);
        $stmt->execute();
    } else {
        $jumlahLebih = $totalDataTrainingNonHoax['total'] - $totalDataTrainingHoax['total'];
        $stmtBalancingDataTrainingNonHoax = "DELETE dt FROM data_training dt
                                             JOIN (
                                               SELECT id_training
                                               FROM data_training
                                               WHERE label = 'Non-Hoax'
                                               ORDER BY id_training DESC
                                               LIMIT $jumlahLebih
                                             ) sub ON dt.id_training = sub.id_training";
        $stmt = $conn->prepare($stmtBalancingDataTrainingNonHoax);
        $stmt->execute();
    }
    echo "<script>
            Swal.fire({
                title: 'Balancing Selesai',
                text: 'Data training telah berhasil di-balancing.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
            });
        </script>";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .card-text {
            font-size: 36px;
        }

        .card-body-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            font-size: 24px;
            /* Normal icon size */
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
                    <a class="navbar-brand" href="index.php">
                        <h3>Deteksi Hoax</h3>
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
                                <<i class="fa-solid fa-wand-magic-sparkles"></i><span class="hide-menu">Demo Model</span>
                            </a>
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
                        <h3 class="text-themecolor">Balancing</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                            <li class="breadcrumb-item active">Balancing</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card rounded-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Data Training</h5>
                                <p class="card-text"><?php echo $totalDataTraining['total']; ?></p>
                                <div class="card-body-icon">
                                    <i class="fa-solid fa-database"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card rounded-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Data Hoax</h5>
                                <p class="card-text"><?php echo $totalDataTrainingHoax['total']; ?></p>
                                <div class="card-body-icon">
                                    <i class="fa-solid fa-database"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card rounded-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Data Non-Hoax</h5>
                                <p class="card-text"><?php echo $totalDataTrainingNonHoax['total']; ?></p>
                                <div class="card-body-icon">
                                    <i class="fa-solid fa-database"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <form method="post">
                        <button type="submit" class="btn btn-primary" name="balancing" style="width: fit-content;">Seimbangkan</button>
                    </form>
                </div>
                <div>
                    <canvas id="balanceChart" width="400" height="200"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('balanceChart').getContext('2d');
            const balanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Hoax', 'Non-Hoax'],
                    datasets: [{
                        label: 'Jumlah Data Training',
                        data: [<?= $totalDataTrainingHoax['total'] ?>, <?= $totalDataTrainingNonHoax['total'] ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
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