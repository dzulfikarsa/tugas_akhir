<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run TF-IDF Analysis</title>
</head>

<body>
    <h1>TF-IDF Analysis from Python Script</h1>
    <form action="tf-idf.php" method="post">
        <input type="submit" name="run_script" value="Run TF-IDF Analysis">
    </form>

    <?php
    if (isset($_POST['run_script'])) {
        $command = escapeshellcmd('python tf-idf.py');
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
    }
    ?>
</body>

</html>