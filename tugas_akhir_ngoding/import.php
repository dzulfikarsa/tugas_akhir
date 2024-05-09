<?php
require 'koneksi.php';  // Memasukkan definisi kelas Database

$database = new Database(); // Membuat instance dari kelas Database
$conn = $database->connect(); // Memanggil fungsi connect untuk mendapatkan koneksi PDO

if (isset($_POST["import"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");

        if ($file !== FALSE) {
            // Mengabaikan baris pertama (header)
            fgetcsv($file);

            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if (isset($column[4])) { // Pastikan kolom title ada
                    $title = $conn->quote($column[4]);

                    // Cek apakah judul sudah ada di database
                    $query = "SELECT 1 FROM data_raw WHERE title = $title";
                    $check = $conn->query($query);

                    if ($check->rowCount() == 0) { // Jika judul belum ada, import data
                        if (count($column) >= 17) { // Pastikan semua kolom ada
                            $id = $conn->quote($column[0]);
                            $authors = $conn->quote($column[1]);
                            $status = $conn->quote($column[2]);
                            $classification = $conn->quote($column[3]);
                            $content = $conn->quote($column[5]);
                            $fact = $conn->quote($column[6]);
                            $references_link = $conn->quote($column[7]);
                            $source_issue = $conn->quote($column[8]);
                            $source_link = $conn->quote($column[9]);
                            $picture1 = $conn->quote($column[10]);
                            $picture2 = $conn->quote($column[11]);
                            $tanggal = $conn->quote($column[12]);
                            $tags = $conn->quote($column[13]);
                            $conclusion = $conn->quote($column[14]);
                            $claim_review = $conn->quote($column[15]);
                            $media = $conn->quote($column[16]);

                            $sqlInsert = "INSERT INTO data_raw (id, authors, status, classification, title, content, fact, references_link, source_issue, source_link, picture1, picture2, tanggal, tags, conclusion, claim_review, media) VALUES ($id, $authors, $status, $classification, $title, $content, $fact, $references_link, $source_issue, $source_link, $picture1, $picture2, $tanggal, $tags, $conclusion, $claim_review, $media)";
                            $result = $conn->exec($sqlInsert);

                            if ($result) {
                                echo "Data berhasil diimpor.<br>";
                            } else {
                                echo "Error pada saat import data.<br>";
                            }
                        } else {
                            echo "Error: Data CSV tidak lengkap atau tidak valid.<br>";
                        }
                    } else {
                        echo "Duplikasi judul ditemukan dan diabaikan: $title<br>";
                    }
                }
            }
            fclose($file); // Tutup file CSV setelah selesai mengimpor
        } else {
            echo "Error: Gagal membuka file CSV.<br>";
        }
    } else {
        echo "Error: Ukuran file CSV kosong atau terlalu kecil.<br>";
    }
}
?>

<form class="form-horizontal" action="" method="post" name="uploadCsv" enctype="multipart/form-data">
    <div>
        <label>Choose CSV File</label>
        <input type="file" name="file" accept=".csv">
        <button type="submit" name="import">Import</button>
    </div>
</form>
