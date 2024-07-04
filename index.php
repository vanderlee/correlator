<?php

require_once 'classes/autoloader.php';

//$scores = [20, 17, 12, 30, 17, 30];
//$categories = ['maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'donderdag'];
//$weights = [1, 1, 1, 1, 1, 1];
//
//$classes = new classes\classes($scores);
//$p_value = $classes->getPValueOfChiSquaredGoodnessOfFit($categories, $weights);	// 0.053745847459089
//var_dump($p_value);
//die;

$scores = [1, 2, 3, 4, 5];
$factor = [1, 2, 9, 4, 4, 4];
$Correlator = new Correlator\Correlator($scores);
var_dump($Correlator->correlate($factor, \Correlator\Correlator::METHOD_ANOVA));
var_dump($Correlator->probability($factor, \Correlator\Correlator::METHOD_ANOVA));
//var_dump($classes->getPValueOfAnova($factor, true));	// .492681
//var_dump($classes->getPValueOfSpearmansRank($factor, true)); // 0.93471
die;

$ITERS = 1e1;

$s = microtime(1);
$i = $ITERS;
while ($i--) {
    $Correlator->getDistanceCorrelation($categories);
}
$e = microtime(1);
echo(($e - $s) / $ITERS * 1e3 . ' msec.');
