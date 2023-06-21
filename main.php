<?php
function calculateSS($shipment, $driver)
{
    $shipmentLength = strlen($shipment);
    $driverLength = strlen($driver);

    if ($shipmentLength % 2 == 0) {
        $baseScore = preg_match_all("/[aeiou]/i", $driver) * 1.5;
    } else {
        $baseScore = ($driverLength - preg_match_all("/[aeiou]/i", $driver));
    }

    for ($i = 2; $i <= min($shipmentLength, $driverLength); $i++) {
        if ($shipmentLength % $i == 0 && $driverLength % $i == 0) {
            $baseScore *= 1.5;
            break;
        }
    }

    return $baseScore;
}

if ($argc < 3) {
    echo "Usage: php program.php <shipment_file> <driver_file>" . PHP_EOL;
    exit(1);
}

$shipmentFile = $argv[1];
$shipments = file($shipmentFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$driverFile = $argv[2];
$drivers = file($driverFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$totalSS = 0;
$assignments = array();

$driverScores = array();
foreach ($drivers as $driver) {
    $driverScores[$driver] = 0;
}

foreach ($shipments as $shipment) {
    $maxScore = 0;
    $assignedDriver = '';

    foreach ($driverScores as $driver => $score) {
        $shipmentScore = calculateSS($shipment, $driver);
        if ($shipmentScore > $maxScore && !in_array($driver, $assignments)) {
            $maxScore = $shipmentScore;
            $assignedDriver = $driver;
        }
    }

    if ($assignedDriver != '') {
        $assignments[$shipment] = $assignedDriver;
        $totalSS += $maxScore;
        $driverScores[$assignedDriver] += $maxScore;
    }
}

echo "Total Suitability Score: " . $totalSS . PHP_EOL;

foreach ($assignments as $shipment => $driver) {
    echo $shipment . " - " . $driver . PHP_EOL;
}
?>
