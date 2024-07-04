<?php

namespace Correlator\Method;

/**
 * Makes correlating arrays easy.
 *
 * @author Martijn W. van der Lee
 */
class PearsonsR extends \Correlator\Method\AbstractMethod
{

    /**
     * Get the P-value for a Pearson product-moment correlation
     *
     * @param self|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float [-1,1] where 0 indicates no correlation. Sign indicates positive/negative correlation.
     */
    public function getP($other, $is_sample = false)
    {
        $n = count($other);
        $r = $this->getValue($other, $is_sample);
        $t = \Correlator\Utils::pearsonsRToStudentT($r, $n);
        return \Correlator\Utils::studentTToPValue($t, $n);
    }

    /**
     * Pearson product-moment correlation coefficient.
     * (also known as Pearson's R, PPMCC and PCC)
     * Use for monotonic, linear intervals of samples of the population data.
     *
     * @param \Correlator\Correlator|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float [-1,1] where 0 indicates no correlation. Sign indicates positive/negative correlation.
     * @throws \Exception
     */
    public function getValue($other, $is_sample = false)
    {
        $X = $this->correlator->getScores();

        if ($other instanceof \Correlator\Correlator) {
            $Y = $other->getScores();
        } else {
            $Y = &$other;
        }

        $count = count($X);

        if (count($Y) !== $count) {
            throw new \Exception('Different lengths');
        }

        $mean_X = $this->correlator->getMean();
        $stddev_X = $this->correlator->getStandardDeviation($is_sample);

        $mean_Y = \Correlator\Utils::mean($Y);
        $stddev_Y = \Correlator\Utils::standardDeviation($Y, $is_sample, $mean_Y);

        if ($is_sample) {
            $r = (\Correlator\Utils::sumOfProducts($X, $Y) - ($count * $mean_X * $mean_Y)) / (($count - 1) * $stddev_X * $stddev_Y);
        } else {
            $r = (\Correlator\Utils::sumOfProducts($X, $Y) - ($count * $mean_X * $mean_Y)) / ($count * $stddev_X * $stddev_Y);
        }

        return $r;
    }

}
