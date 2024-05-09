<?php
require 'koneksi.php';  // Memasukkan definisi kelas Database

$database = new Database(); // Membuat instance dari kelas Database
$conn = $database->connect(); // Memanggil fungsi connect untuk mendapatkan koneksi PDO

$message = "";
if (isset($_POST["import"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");

        if ($file !== FALSE) {
            fgetcsv($file); // Mengabaikan baris pertama (header)
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if (isset($column[4])) {
                    $title = $conn->quote($column[4]);
                    $query = "SELECT 1 FROM data_raw WHERE title = $title";
                    $check = $conn->query($query);

                    if ($check->rowCount() == 0) {
                        if (count($column) >= 17) {
                            $id = $conn->quote($column[0]);
                            $authors = $conn->quote($column[1]);
                            $status = $conn->quote($column[2]);
                            $classification = $conn->quote($column[3]);
                            $content = $conn->quote($column[5]);
                            // ... Additional fields
                            $sqlInsert = "INSERT INTO data_raw (id, authors, status, classification, title, content) VALUES ($id, $authors, $status, $classification, $title, $content)";
                            $result = $conn->exec($sqlInsert);
                            $message = $result ? "Data berhasil diimpor." : "Error pada saat import data.";
                        } else {
                            $message = "Error: Data CSV tidak lengkap atau tidak valid.";
                        }
                    } else {
                        $message = "Duplikasi judul ditemukan dan diabaikan: $title";
                    }
                }
            }
            fclose($file);
        } else {
            $message = "Error: Gagal membuka file CSV.";
        }
    } else {
        $message = "Error: Ukuran file CSV kosong atau terlalu kecil.";
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
    <div id="main-wrapper">
        <header class="topbar">...</header>
        <aside class="left-sidebar">...</aside>
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Place where you want to show the form and messages -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">Import Data Excel</h3>
                    </div>
                </div>
                <form class="form-horizontal" action="" method="post" name="uploadCsv" enctype="multipart/form-data">
                    <div>
                        <label>Choose CSV File</label>
                        <input type="file" name="file" accept=".csv">
                        <button type="submit" name="import">Import</button>
                    </div>
                </form>
                <?php if (!empty($message)) : ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
            </div>
            <footer class="footer"> Tugas Akhir - Dzulfikar Saif Assalam</footer>
        </div>
    </div>
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
</body>

</html>