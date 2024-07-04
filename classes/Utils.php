<?php

namespace Correlator;

/**
 * Utilities
 *
 * @author Martijn W. van der Lee
 */
abstract class Utils
{

    /**
     * Singleton
     */
    private function __construct()
    {

    }

    /**
     * Get the Student T for a Pearson's R.
     *
     * @param float $r Pearson's R
     * @param integer $n Number of pairs
     * @return float Student T
     * @throws Exception
     */
    public static function pearsonsRToStudentT($r, $n)
    {
        return $r / sqrt((1 - ($r * $r)) / ($n - 2));
    }

    /**
     * Calculate the P value from a Student T.
     *
     * @param float $t
     * @param type $n
     * @return type
     */
    public static function studentTToPValue($t, $n)
    {
        $df = $n - 2;
        $t = abs($t);
        $t2 = $t * $t;

        if ($df == 1) {
            $p = 1 - 2 * atan($t) / pi();
        } else if ($df == 2) {
            $p = 1 - $t / sqrt($t2 + 2);
        } else if ($df == 3) {
            $p = 1 - 2 * (atan($t / sqrt(3)) + $t * sqrt(3) / ($t2 + 3)) / pi();
        } else if ($df == 4) {
            $p = 1 - $t * (1 + 2 / ($t2 + 4)) / sqrt($t2 + 4);
        } else {
            $z = self::studentTToZScore($t, $df);
            $p = self::zScoreToPValue($z);
        }

        return $p;
    }

    /**
     * Calculate the Z score from a Student T.
     *
     * @param float $t Student T
     * @param float $n Number of pairs
     * @return float Z score
     */
    private static function studentTToZScore($t, $n)
    {
        $df = $n - 2;
        $A9 = $df - 0.5;
        $B9 = 48 * $A9 * $A9;
        $T9 = $t * $t / $df;
        $Z8 = $T9 >= 0.04 ? $A9 * log(1 + $T9) : $A9 * (((1 - $T9 * 0.75) * $T9 / 3 - 0.5) * $T9 + 1) * $T9;
        return (1 + (-(((0.4 * $Z8 + 3.3) * $Z8 + 24) * $Z8 + 85.5) / (0.8 * ($Z8 * $Z8) + 100 + $B9) + $Z8 + 3) / $B9) * sqrt($Z8);
    }

    /**
     * Calculate a P value from a Z score.
     *
     * @param float $z Z score
     * @return float P value
     */
    public static function zScoreToPValue($z)
    {
        $z = abs($z);
        return pow((((((0.0000053830 * $z + 0.0000488906) * $z + 0.0000380036) * $z + 0.0032776263) * $z + 0.0211410061) * $z + 0.0498673470) * $z + 1, -16);
    }

    /**
     * Calculate a P value from an F-test.
     *
     * Sources:
     * https://searchcode.com/codesearch/view/7519592/
     * https://github.com/drlippman/IMathAS/blob/master/assessment/libs/stats.php
     * http://statpages.info/anova1sm.html
     * https://www.easycalculation.com/statistics/f-test-p-value.php
     * http://www.danielsoper.com/statcalc/calculator.aspx?id=7
     *
     * @param float $f F-test
     * @param float $ndf Numerator degrees-of-freedom
     * @param float $ddf Denominator degrees-of-freedom
     * @return float P value
     */
    public static function fTestToPValue($f, $ndf, $ddf)
    {
        $half_pi = pi() * .5;

        $x = $ddf / ($ndf * $f + $ddf);

        if (($ndf % 2) === 0) {
            return self::ljSpin(1 - $x, $ddf, $ndf + $ddf - 4, $ddf - 2) * pow($x, $ddf * .5);
        }

        if (($ddf % 2) === 0) {
            return 1 - self::ljSpin($x, $ndf, $ndf + $ddf - 4, $ndf - 2) * pow(1 - $x, $ndf * .5);
        }

        $th = atan(Sqrt($ndf * $f / $ddf));
        $a = $th / $half_pi;
        $sth = sin($th);
        $cth = cos($th);

        if ($ddf > 1) {
            $a += $sth * $cth * self::ljSpin($cth * $cth, 2, $ddf - 3, -1) / $half_pi;
        }
        if ($ndf === 1) {
            return 1 - $a;
        }
        $c = 4 * self::ljSpin($sth * $sth, $ddf + 1, $ndf + $ddf - 4, $ddf - 2) * $sth * pow($cth, $ddf) / pi();

        if ($ddf === 1) {
            return 1 - $a + $c * .5;
        }

        $k = 2;
        while ($k <= ($ddf - 1) * .5) {
            $c *= ($k / ($k - .5));
            ++$k;
        }

        return 1 - $a + $c;
    }

    /**
     * @param float $q
     * @param float $i
     * @param float $j
     * @param float $b
     * @return float
     */
    private static function ljSpin($q, $i, $j, $b)
    {
        $z = $zz = 1;
        while ($i <= $j) {
            $zz *= $q * $i / ($i - $b);
            $z += $zz;
            $i += 2;
        }
        return $z;
    }

// Math

    /**
     * Add a value from each member of an array
     *
     * @param float[] $array
     * @param float $value
     * @return float[]
     */
    public static function array_add(array &$array, $value)
    {
        return array_map(function ($item) use ($value) {
            return $item + $value;
        }, $array);
    }

    /**
     * Sum of squares of an array
     *
     * @param float[] $array
     * @return float
     */
    public static function sumOfSquares($array)
    {
        return self::sumOfProducts($array, $array);
    }

    public static function sumOfProducts($A, $B)
    {
        return array_sum(array_map('array_product', array_map(null, $A, $B)));
    }

    /**
     * Calculate the standard deviation using a previously calculated average.
     *
     * @param float[] $A
     * @param boolean $is_sample Sample (true) or population (false, default)
     * @param float $mean Optional mean if already known.
     * @return float
     */
    public static function standardDeviation($A, $is_sample = false, $mean = null)
    {
        return sqrt(self::variance($A, $is_sample, $mean));
    }

    /**
     * Calculate the variance of an array.
     *
     * @param float[] $A
     * @param boolean $is_sample Sample (true) or population (false, default)
     * @return float
     */
    public static function variance($A, $is_sample = false, $mean = null)
    {
        if ($mean === null) {
            $mean = self::mean($A);
        }

        $var = 0.;
        foreach ($A as $item) {
            $dif = $item - $mean;
            $var += $dif * $dif;
        }

        $var /= $is_sample ? count($A) - 1 : count($A);

        return $var;
    }

    /**
     * Calculate the mean average of an array.
     *
     * @param float[] $A
     * @return float
     */
    public static function mean($A)
    {
        return array_sum($A) / count($A);
    }

    /**
     * Generate a distance matrix (linear) for a set of scores
     *
     * @param float[] $array
     * @return float[]
     */
    public static function &arrayToDistanceMatrix(&$array)
    {
        $n = count($array);
        $m = $n * $n;
        $A = [];
        $R = [];

        for ($i = 0; $i < $n; ++$i) {
            $R[$i] = 0.;
        }

        for ($ixn = 0, $i = 0; $i < $n; ++$i, $ixn += $n) {
            $A[$ixn + $i] = 0.;
            $I = &$array[$i];
            for ($jxn = $ixn + $n + $i, $j = $i + 1; $j < $n; ++$j, $jxn += $n) {
                $J = &$array[$j];
                $v = $I < $J ? $J - $I : $I - $J;

                $A[$ixn + $j] = $A[$jxn] = $v;
                $R[$i] += $v;
                $R[$j] += $v;
            }
        }

        $M = 0;
        for ($i = 0; $i < $n; ++$i) {
            $M += $R[$i];
            $R[$i] /= $n;
        }
        $M /= $m;

        for ($ixn = 0, $i = 0; $i < $n; ++$i, $ixn += $n) {
            for ($jxn = $ixn + $i, $j = $i; $j < $n; ++$j, $jxn += $n) {
                $A[$jxn] = $A[$ixn + $j] += ($M - $R[$i]) - $R[$j];
            }
        }

        return $A;
    }

    /**
     * Return a fractionally ranked list
     * https://en.wikipedia.org/wiki/Ranking#Fractional_ranking_.28.221_2.5_2.5_4.22_ranking.29
     *
     * @param type $values
     * @todo Move to \classes\Utils
     *
     */
    public static function toFractionalRanking($values, $start = 1)
    {
        // Get number of records per group as [value => count]
        $ranks = array_fill_keys(array_unique($values), 0);
        foreach ($values as $value) {
            ++$ranks[$value];
        }

        // Order the groups by value; rank ordering
        ksort($ranks);

        // Calculate fractional rank number for each group as [value => rank]
        array_walk($ranks, function (&$rank) use (&$start) {
            $new = $start + (($rank * ($rank - 1)) * .25);
            $start += $rank;
            $rank = $new;
        });

        // Map ranks to original array as [index => rank]
        array_walk($values, function (&$value) use ($ranks) {
            $value = $ranks[$value];
        });

        return $values;
    }

}
