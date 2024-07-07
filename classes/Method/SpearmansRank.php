<?php

namespace Vanderlee\Correlator\Method;

use Vanderlee\Correlator\Utils;

/**
 * Calculate Spearman's rho
 * Use for non-monotonic, linear intervals or ordinals
 *
 * @author Martijn W. van der Lee
 */
class SpearmansRank extends \Correlator\Method\AbstractMethod
{

    /**
     * Calculate P value of Spearman's rho
     * Use for non-monotonic, linear intervals or ordinals
     *
     * Use Pearsons R to Student T conversion, as Spearman is essentially
     * Pearson's R after the fractional ranking phase.
     *
     * @param self|float[] $other
     * @return float 0 indicates no correlation, sign indicates negative/positive correlations
     */
    public function getP($other, $is_sample = true)
    {
        $n = count($other);
        $r = $this->getValue($other, $is_sample);
        $t = Utils::pearsonsRToStudentT($r, $n);
        return Utils::studentTToPValue($t, $n);
    }

    /**
     * Calculate Spearman's rho
     * Use for non-monotonic, linear intervals or ordinals
     *
     * Sources:
     * http://www.socscistatistics.com/tests/spearman/Default3.aspx
     *
     * @param \Correlator\Correlator|float[] $other
     * @return float 0 indicates no correlation, sign indicates negative/positive correlations
     * @throws \Exception
     */
    public function getValue($other, $is_sample = true)
    {
        $X = $this->correlator->getFractionalRanking();

        if ($other instanceof \Correlator\Correlator) {
            $Y = \Correlator\Utils::toFractionalRanking($other->getScores());
        } else {
            $Y = \Correlator\Utils::toFractionalRanking($other);
        }

        $count = count($X);

        if (count($Y) !== $count) {
            throw new \Exception('Different lengths');
        }

        $mean = ($count + 1) * 0.5;

        $stddev_X = \Correlator\Utils::standardDeviation($X, $is_sample, $mean);
        $stddev_Y = \Correlator\Utils::standardDeviation($Y, $is_sample, $mean);

        $sum = 0;
        for ($i = 0; $i < $count; ++$i) {
            $sum += ($X[$i] - $mean) * ($Y[$i] - $mean);
        }

        return ($sum / ($count - 1)) / ($stddev_X * $stddev_Y);
    }

}
