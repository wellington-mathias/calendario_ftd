<?php
include_once "api/config/database.php";


$database = new Database();
$db = $database->getConnection();

$x = $db;
$y = 2;
$z = 0;

if (($x + $y) == 3) {
	$z = $x + $y;
}

echo "<p>Hello World! => <b>$z</b></p>";


?>
<?=$x?> + <?=$y?> = <?=($x + $y)?>