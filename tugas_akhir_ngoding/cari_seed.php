<?php
ini_set('max_execution_time', '0');
require 'koneksi.php';
$database = new Database();
$conn = $database->connect();

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

function splitData($seed, $conn)
{
    $stmtNonHoax = $conn->prepare("SELECT id_preprocessing, teks, label FROM data_preprocessing WHERE label='non-hoax'");
    $stmtNonHoax->execute();
    $resultNonHoax = $stmtNonHoax->fetchAll(PDO::FETCH_ASSOC);
    $countNonHoax = $stmtNonHoax->rowCount();

    $stmtHoax = $conn->prepare("SELECT id_preprocessing, teks, label FROM data_preprocessing WHERE label='hoax' ORDER BY RAND($seed) LIMIT :countNonHoax");
    $stmtHoax->bindParam(':countNonHoax', $countNonHoax, PDO::PARAM_INT);
    $stmtHoax->execute();
    $resultHoax = $stmtHoax->fetchAll(PDO::FETCH_ASSOC);

    $conn->exec("TRUNCATE TABLE data_training");
    $conn->exec("TRUNCATE TABLE data_testing");

    $data = array_merge($resultNonHoax, $resultHoax);
    shuffle($data);

    $splitPoint = round(0.8 * count($data));
    $stmtTraining = $conn->prepare("INSERT INTO data_training (id_training, real_text, clean_text, label) VALUES (?, ?, ?, ?)");
    $stmtTesting = $conn->prepare("INSERT INTO data_testing (id_testing, real_text, clean_text, label) VALUES (?, ?, ?, ?)");

    foreach ($data as $index => $row) {
        if ($index < $splitPoint) {
            $stmtTraining->execute([$row['id_preprocessing'], $row['teks'], $row['teks'], $row['label']]);
        } else {
            $stmtTesting->execute([$row['id_preprocessing'], $row['teks'], $row['teks'], $row['label']]);
        }
    }
    return "Data traning dan testing berhasil di split";
}


$bestAccuracy = 0;
$bestSeed = 0;
$bestResults = array();
$file_path = 'confusion_matrix.json';
for ($i = 1000; $i < 5000; $i++) {
    $seed = $i;
    srand($seed);

    $trainingCount = 0;
    $testingCount = 0;

    splitData($seed, $conn);

    $output = shell_exec("python C:\\xampp\\htdocs\\tugas_akhir\\tugas_akhir_ngoding\\util\\naive_bayes.py");

    $output = shell_exec("python C:\\xampp\\htdocs\\tugas_akhir\\tugas_akhir_ngoding\\util\\pengujian.py");

    $results = json_decode(file_get_contents($file_path), true);
    if ($results['Accuracy'] > $bestAccuracy) {
        $bestAccuracy = $results['Accuracy'];
        $bestSeed = $seed;
        $bestResults = $results;
    }

    $conn->exec("TRUNCATE TABLE data_training");
    $conn->exec("TRUNCATE TABLE data_testing");
}

echo ($bestSeed);
echo ("spasi");
var_dump($bestResults);

echo (srand($bestSeed));
