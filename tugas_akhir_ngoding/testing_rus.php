<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "deteksi_hoax";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mendapatkan jumlah data non-hoax
$resultNonHoax = $conn->query("SELECT teks, label FROM data_preprocessing WHERE label='non-hoax'");
$countNonHoax = $resultNonHoax->num_rows;  

// Mengambil data hoax secara acak sebanyak jumlah data non-hoax 
$resultHoax = $conn->query("SELECT teks, label FROM data_preprocessing WHERE label='hoax' ORDER BY RAND() LIMIT $countNonHoax");
$countHoax = $resultHoax->num_rows;  

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Undersampling for Hoax Detection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Hoax Detection: Random Undersampling</h1>
    <h2>Summary of Data</h2>
    <table>
        <tr>
            <th>Type</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Non-Hoax</td>
            <td><?= $countNonHoax; ?></td>
        </tr>
        <tr>
            <td>Hoax</td>
            <td><?= $countHoax; ?></td>
        </tr>
    </table>

    <h2>Data Non-Hoax</h2>
    <table>
        <tr>
            <th>Text</th>
            <th>Label</th>
        </tr>
        <?php while ($row = $resultNonHoax->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($row["teks"]); ?></td>
                <td><?= htmlspecialchars($row["label"]); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Data Hoax (Randomly Sampled)</h2>
    <table>
        <tr>
            <th>Text</th>
            <th>Label</th>
        </tr>
        <?php while ($row = $resultHoax->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($row["teks"]); ?></td>
                <td><?= htmlspecialchars($row["label"]); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php $conn->close(); ?>
</body>

</html>