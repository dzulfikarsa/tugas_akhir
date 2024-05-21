    <?php
    require 'koneksi.php'; // Memasukkan file koneksi.php
    $database = new Database(); // Membuat instance dari kelas Database
    $conn = $database->connect(); // Memanggil fungsi connect untuk mendapatkan koneksi database

    // Handle Preprocessing
    if (isset($_POST['run_preprocessing'])) {
        $sql = "SELECT * FROM data_raw";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $data = array();
            foreach ($result as $row) {
                $data[] = $row;
            }

            // Menyimpan data ke file JSON
            $filepath = 'C:\\xampp\\htdocs\\tugas_akhir\\tugas_akhir_ngoding\\util\\data.json';
            file_put_contents($filepath, json_encode($data));

            // Menjalankan script Python
            $command = "python C:\\xampp\\htdocs\\tugas_akhir\\tugas_akhir_ngoding\\util\\preprocessing.py";
            $output = shell_exec($command);
            $message_preprocessing = "Semua data berhasil dipreprocessing";
            $pythonOutput = $output ? $output : "Python script did not produce any output.";
        } else {
            $message_preprocessing = "Error: Data belum ada atau tidak ditemukan.";
        }
    }


    if (isset($_POST['update_label'])) {
        $id = $_POST['id'];
        $newLabel = $_POST['label'];
        $updateQuery = "UPDATE data_preprocessing SET label = :label WHERE id = :id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':label', $newLabel);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $message_update = "Label updated successfully.";
        } else {
            $message_update = "Failed to update label.";
        }
    }

    $query = "SELECT id, teks, label FROM data_preprocessing";
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
                            <h3 class="text-themecolor">Labelling</h3>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                                <li class="breadcrumb-item active">Labelling</li>
                            </ol>
                        </div>
                    </div>

                    <div class="container mt-5">
                        <!-- Card Container -->
                        <div class="card">
                            <!-- Card Header -->
                            <div class="card-header">
                                <h4 class="card-title">Labelling</h4>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <table class="table table-striped" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th style="width: 60%;">Clean Text</th>
                                            <th>Label</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $row) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['id']) ?></td>
                                                <td><?= htmlspecialchars($row['teks']) ?></td>
                                                <td>
                                                    <form method="post" action="">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="update_label" value="1">
                                                        <select name="label" class="form-control status-dropdown" onchange="this.form.submit()">
                                                            <option value="Hoax" <?= $row['label'] === 'Hoax' ? 'selected' : '' ?>>Hoax</option>
                                                            <option value="Non-Hoax" <?= $row['label'] === 'Non-Hoax' ? 'selected' : '' ?>>Non-Hoax</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

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