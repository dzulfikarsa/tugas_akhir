<?php
require 'koneksi.php';
$database = new Database();
$conn = $database->connect();

$message_submit = "";
$message_delete = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Execute the Python script
    exec("python util/tf-idf.py", $output, $return);
    $results = json_decode(implode("", $output), true);

    if ($results && count($results) > 0) {
        // Prepare the SQL statement using PDO
        $stmt = $conn->prepare("INSERT INTO tf_idf (teks, score) VALUES (:teks, :score)");

        // Insert data into the database
        $allInserted = true;  // Assume all data will be inserted successfully
        foreach ($results as $result) {
            if (is_array($result) && count($result) == 2) {
                // Bind parameters to the prepared statement
                $stmt->bindParam(':teks', $result[0]); // $result[0] is the text
                $stmt->bindParam(':score', $result[1]); // $result[1] is the score

                // Execute the prepared statement
                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    $message_submit = "Error: " . $e->getMessage();
                    $allInserted = false;
                    break;
                }
            } else {
                $message_submit = "Skipping incomplete or incorrectly formatted item.";
                $allInserted = false;
            }
        }

        if ($allInserted) {
            $message_submit = "Data berhasil dibobotkan";
        }
    } else {
        $message_submit = "No data or failed to decode JSON.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $sql = "DELETE FROM tf_idf";
    try {
        $conn->exec($sql);
        $message_delete = "Data berhasil dihapus";
    } catch (PDOException $e) {
        $message_delete = "Error deleting data: " . $e->getMessage();
    }
}

$query = "SELECT * FROM tf_idf";
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="fa-solid fa-code-compare"></i>
                                <span class="hide-menu">Modelling</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="tf-idf.php">TF-IDF</a></li>
                                <li><a href="naive_bayes.php">Naive Bayes Clasifier</a></li>
                            </ul>
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
                        <h3 class="text-themecolor">TF-IDF</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Modelling</a></li>
                            <li class="breadcrumb-item active">TF-IDF</li>
                        </ol>
                    </div>
                </div>

                <div class="row page-titles">
                </div>

                <div class="container mt-5">
                    <!-- Card Container -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hasil Pembobotan TF-IDF</h4>
                        </div>
                        <div class="card-body">
                            <!-- Form to trigger TF-IDF computation -->
                            <div class="row">
                                <div class="col-6">
                                    <form action="" method="post">
                                        <button type="submit" class="btn btn-primary w-100" name="submit">Mulai Pembobotan TF-IDF</button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form method="post">
                                        <button type="submit" class="btn btn-danger w-100" name="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus semua data?');">Hapus Data</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($message_delete)) : ?>
                            <div class="alert alert-info mt-2"><?php echo $message_delete; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($message_submit)) : ?>
                            <div class="alert alert-info mt-2"><?php echo $message_submit; ?></div>
                        <?php endif; ?>
                        <div class="card-body">
                            <table class="table table-striped" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Teks</th>
                                        <th>Scoring</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php $counter = 1; // Initialize a counter 
                                    ?>
                                    <?php foreach ($results as $row) : ?>
                                        <tr>
                                            <td><?= $counter++; ?></td>
                                            <td><?= htmlspecialchars($row['teks']) ?></td>
                                            <td><?= htmlspecialchars($row['score']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

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


</body>

</html>