<?php

// Load the config files
$config1 = parse_ini_file('config1.cnf',true)["client"]; // Development
$config2 = parse_ini_file('config2.cnf',true)["client"]; // Production

// Connect to the development database
$prod = mysqli_connect($config1['host'], $config1['user'], $config1['password'], $config1['db']);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Connect to production
$dev = mysqli_connect($config2['host'], $config2['user'], $config2['password'], $config2['db']);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Validate input
$table = mysqli_real_escape_string($prod, $_GET['table']);
if (empty($table)) {
    exit ("Table name required");
}

// Make sure the table exists
$sql = "SHOW CREATE TABLE " . $table;
$result = $prod->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $prodCreate = substr($row['Create Table'], 0, strpos($row['Create Table'], "ENGINE="));
    }
}
else {
    exit("Table not found in development");
}

$sql = "SHOW CREATE TABLE " . $table;
$result = $dev->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $devCreate = substr($row['Create Table'], 0, strpos($row['Create Table'], "ENGINE="));
    }
}
else {
    exit("Table not found in production");
}

// Handle error
if ($devCreate != $prodCreate) {
    echo "<h1 style='text-align: center'>Structures Are Not Identical</h1>";
    echo "<table><tr><th>Production DB</th><th>Development DB</th></tr>";
    echo "<tr><td>";
    echo $devCreate;
    echo "</td><td>";
    echo $prodCreate;
    echo "</td></tr></table>";
    die();
}

// Perform the copy
$command = "/usr/bin/mysqldump --defaults-extra-file=config2.cnf " . $config2['db'] . " $table |/usr/bin/mysql --defaults-extra-file=" . getcwd() . "/config1.cnf " . $config1['db'] . " 2>&1";
$output = shell_exec($command);
//exit("mysqldump --defaults-extra-file=config2.cnf " . $config2['db'] . " $table |/usr/local/mysql/bin/mysql --defaults-extra-file=config1.cnf " . $config1['db'] . " 2>&1");
if (!($output == NULL)) {
    exit("Error copying data. " . $output);
}

header("Location: index.php?success=1");
