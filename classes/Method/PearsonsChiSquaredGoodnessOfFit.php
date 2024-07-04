<?php

namespace Correlator\Method;

/**
 * Run the Chi-squared Goodness of Fit test
 *
 * @author Martijn W. van der Lee
 */
class PearsonsChiSquaredGoodnessOfFit extends \Correlator\Method\AbstractMethod
{

    /**
     * log( sqrt( pi ) )
     *
     * @var float
     */
    private static $LOG_SQRT_PI = 0.5723649429247000870717135;

    /**
     * 1 / sqrt( pi )
     *
     * @var float
     */
    private static $INV_SQRT_PI = 0.5641895835477562869480795;

    /**
     * Get the P value of a Chi-squared Goodness of Fit test
     *
     * @param string[] $categories list of categories matching the scores
     * @param float[] $weights Relative weights to apply to the scores.
     * @return float P value
     */
    public function getP(array $categories, array $weights = null)
    {
        $n = count(array_unique($categories));
        $chi = $this->getValue($categories, $weights);
        return $p = self::chiSquaredToPValue($chi, $n);
    }

    /**
     * Run the Chi-squared Goodness of Fit test
     *
     * @param string[] $categories list of categories matching the scores
     * @param float[] $weights Relative weights to apply to the scores.
     * @return float Chi-squared critical value
     */
    public function getValue(array $categories, array $weights = null)
    {
        $scores = $this->correlator->getScores();

        // @todo nominalization of $this->array. Assume nominalized for now.
        // weighted if weights supplied!
        $category_scores = array_fill_keys(array_unique($categories), 0);
        $category_weights = array_fill_keys(array_unique($categories), 0);
        foreach ($categories as $index => $category) {
            $weight = $weights === null ? 1 : $weights[$index];
            $category_scores[$category] += $scores[$index] * $weight;
            $category_weights[$category] += $weight;
        }

        $category_scores = array_map(function ($score, $weight) {
            return $score / $weight;
        }, $category_scores, $category_weights);

        $average = array_sum($category_scores) / count($category_scores);

        array_walk($category_scores, function (&$score) use ($average) {
            $diff = $score - $average;
            $score = ($diff * $diff) / $average;
        });

        return array_sum($category_scores);
    }

    /**
     * Calculate the P value of a Chi-squared critical value
     *
     * Sources:
     * https://www.swogstat.org/stat/public/chisq_calculator.htm
     *
     * @param float $chi Chi-squared critical value
     * @param integer $n Number of pairs
     * @return float
     */
    private static function chiSquaredToPValue($chi, $n)
    {
        $df = $n - 1;
        if ($chi <= 0 || $df < 1) {
            return 1;
        }

        $a = 0.5 * $chi;
        $y = $df > 1 ? exp(-$a) : 0;
        $even = !($df & 1);

        $s = $even ? $y : (2 * \Correlator\Utils::zScoreToPValue(-sqrt($chi)));
        if ($df > 2) {
            $chi = 0.5 * ($df - 1);
            $z = $even ? 1 : 0.5;
            if ($a > 20) {
                $e = $even ? 0 : self::$LOG_SQRT_PI;
                $c = log($a);
                while ($z <= $chi) {
                    $e += log($z) + exp($c * $z - $a - $e);
                    ++$z;
                }
            } else {
                $e = $even ? 1 : (self::$INV_SQRT_PI / sqrt($a));
                $c = 0;
                while ($z <= $chi) {
                    $e *= $a / $z;
                    $c += $e;
                    ++$z;
                }
                $s = $c * $y + $s;
            }
        }
        return $s;
    }

}
