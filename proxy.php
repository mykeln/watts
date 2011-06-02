<?php
// get variables
$username = isset($_GET['username']) ? $_GET['username'] : null;
$password = isset($_GET['password']) ? $_GET['password'] : null;
$date = isset($_GET['startDate']) ? $_GET['startDate'] : null;

// Set your return content type
header('Content-type: application/xml');

// Website url to open
$daurl = "http://www.trainingpeaks.com/tpwebservices/service.asmx/GetWorkoutsForAthlete?&username=$username&password=$password&startDate=$date&endDate=$date";

// Get that website's content
$handle = fopen($daurl, "r");

// If there is something, read and return
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        echo $buffer;
    }
    fclose($handle);
}
?>
