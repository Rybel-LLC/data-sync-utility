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

// Display a message to the user
if ($_GET['success'] == "1") {
    echo "<script>alert('Successfully synced data!')</script>";
}

?>
<html>
    <head>
        <title>Rybel LLC Data Sync Utility</title>
    </head>
    <body>
        <script>
            function confirmSync(tableName) {
                var txt;
                var r = confirm("Are you sure you want to sync " + tableName + "?");
                if (r == true) {
                    this.document.location.href = 'table.php?table=' + tableName;
                }
            }
        </script>

<h1>Rybel LLC Data Sync Utility</h1>
<h3>Click on the name of the table that you want to copy data from</h3>
<h5><b>REMINDER:</b> This utility is meant to replicate production data into the development database and not vice-versa</h5>
<?php
    // Find all the tables in the production database
    $sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . $config1['db'] . "'";
    $result = $prod->query($sql);
    $prodTables = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tableName = $row['TABLE_NAME'];
            array_push($prodTables, $tableName);
        }
    }

    // Find all the tables in the development database
    $sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . $config2['db'] . "'";
    $result = $dev->query($sql);
    $devTables = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tableName = $row['TABLE_NAME'];
            array_push($devTables, $tableName);
        }
    }

    // Combine the arrays
    $master = array_unique(array_merge($prodTables,$devTables), SORT_REGULAR);

    // Enumerate the arrays
    echo "<table>";
    foreach ($master as $item) {
        echo "<tr>";
        echo "<td><a href='#' onclick='confirmSync(\"$item\")'>";
        echo $item;
        echo "</a></td>";
        echo "</tr>";
    }
    echo "</table>";
?>
    </body>
</html>
