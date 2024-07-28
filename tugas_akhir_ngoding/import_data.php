<?php
require 'koneksi.php';  // Memasukkan definisi kelas Database

$database = new Database(); // Membuat instance dari kelas Database
$conn = $database->connect(); // Memanggil fungsi connect untuk mendapatkan koneksi PDO
$result = "";
$message_import = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["import"])) {
    if (isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] == 0) {
        $fileName = $_FILES["file"]["tmp_name"];

        if ($_FILES["file"]["size"] > 0) {
            $file = fopen($fileName, "r");

            if ($file !== FALSE) {
                $conn->beginTransaction(); // Start transaction
                fgetcsv($file); // Skip the header row
                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if (count($column) >= 17) { // Ensure all columns are present
                        $title = $conn->quote($column[4]);
                        $query = "SELECT 1 FROM data_raw WHERE title = $title";
                        $check = $conn->query($query);

                        if ($check->rowCount() == 0) {
                            $insertValues = array_map(function ($value) use ($conn) {
                                return $conn->quote($value);
                            }, $column);
                            $sqlInsert = "INSERT INTO data_raw (id_raw, authors, status, classification, title, content, fact, references_link, source_issue, source_link, picture1, picture2, tanggal, tags, conclusion, claim_review, media) VALUES (" . implode(",", $insertValues) . ")";
                            $conn->exec($sqlInsert);
                        } else {
                            $message_import .= "Skipped duplicate title: $title<br>";
                        }
                    } else {
                        $message_import .= "Error: Data CSV not complete or invalid.<br>";
                    }
                }
                $conn->commit(); // Commit transaction
                fclose($file);
                if (empty($message_import)) {
                    $message_import = "Data successfully imported.";
                }
            } else {
                $message_import = "Error: Failed to open CSV file.";
            }
        } else {
            $message_import = "Error: CSV file size is empty or too small.";
        }
    }
    header("Location: " . $_SERVER["PHP_SELF"]); // Redirect to avoid resubmission
    exit();
}

$importSuccess = false; // Flag to track import success

if ($result) {
    $message_import = "Data berhasil diimpor.";
    $importSuccess = true; // Set flag to true on successful import
} else {
    $message_import = "Error pada saat import data.";
}

$deleteSuccess = false; // Flag to track delete success
$nothingToDelete = false; // Flag if there's nothing to delete

if (isset($_POST['delete_all'])) {
    $checkData = $conn->query("SELECT COUNT(*) AS count FROM data_raw");
    $dataCount = $checkData->fetchColumn();

    if ($dataCount > 0) {
        $deleteQuery = "DELETE FROM data_raw"; // Replace 'data_raw' with your table name
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute();
        $deleteSuccess = true; // Set flag to true on successful delete
        $message_delete = "Semua data berhasil dihapus.";
    } else {
        $nothingToDelete = true; // Set flag if there's nothing to delete
    }
}

// Mengambil data dari database untuk ditampilkan
$query = "SELECT id_raw, title, status FROM data_raw";  // Menyesuaikan kolom yang diambil
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
                        <h3 class="text-themecolor">Import Data</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                            <li class="breadcrumb-item active">Import Data</li>
                        </ol>
                    </div>
                </div>

                <div class="container mt-5">
                    <!-- Card Container -->
                    <div class="card">
                        <!-- Card Header -->
                        <!-- Card Body -->
                        <div class="card-body">
                            <form class="form-horizontal" action="" method="post" name="uploadCsv" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <p>Anda hanya bisa melakukan import data dengan format CSV</p>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <input type="file" name="file" id="csvFileInput" class="form-control">
                                        </div>
                                        <div class="mb-3 text-center">
                                            <button type="submit" class="btn btn-primary d-block w-100" name="import" id="importButton">Import Data ke Database</button>
                                        </div>
                                    </form>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Card Container -->
                    <div class="card">
                        <!-- Card Header -->
                        <div class="card-header">
                            <h4 class="card-title">Hasil Data</h4>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus Semua Data</button>
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
                            <table class="table table-striped" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data Asli</th> <!-- Ini sebelumnya adalah 'Author' -->
                                        <th>Label</th> <!-- Ini sebelumnya adalah 'Status' -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $row) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id_raw']) ?></td>
                                            <td><?= htmlspecialchars($row['title']) ?></td> <!-- Menggunakan kolom 'title' -->
                                            <td><?= htmlspecialchars($row['status']) ?></td> <!-- Tetap menggunakan kolom 'status' -->
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($results)) : ?>
                                        <tr>
                                            <td colspan="3">No data found</td> <!-- Ubah colspan menjadi 3 karena sekarang hanya ada tiga kolom -->
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

    <script>
        function confirmDelete() {
            $('#deleteModal').modal('show');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            <?php if ($importSuccess) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: 'Data berhasil diimpor.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
            <?php endif; ?>
        });
    </script>
    <script>
        $(document).ready(function() {
            // Check for import success
            <?php if ($importSuccess) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: 'Data berhasil diimpor.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
            <?php endif; ?>

            // Check for delete success
            <?php if ($deleteSuccess) : ?>
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Semua data berhasil dihapus.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
            <?php endif; ?>

            // Check if there was nothing to delete
            <?php if ($nothingToDelete) : ?>
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Tidak ada data untuk dihapus.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            <?php endif; ?>
        });
    </script>
    <script>
        document.getElementById('importButton').addEventListener('click', function(e) {
            var fileInput = document.getElementById('csvFileInput');
            if (!fileInput.value) {
                e.preventDefault(); // Stop the form from submitting
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'File tidak ada atau belum dimasukan!'
                });
            }
        });
    </script>

</body>

</html>