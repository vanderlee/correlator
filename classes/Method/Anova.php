<?php

namespace Correlator\Method;

/**
 * ANOVA One-way
 *
 * @author Martijn W. van der Lee
 */
class Anova extends \Correlator\Method\AbstractMethod
{

    /**
     * Get the P-value for ANOVA one-way
     *
     * @param self|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float [-1,1] where 0 indicates no correlation. Sign indicates positive/negative correlation.
     */
    public function getP($other, $is_sample = false)
    {
        $f = $this->getValue($other, $is_sample);
        $ddf = $this->correlator->getCount() + ($other instanceof \Correlator\Correlator ? $other->getCount() : count($other)) - 2;
        return \Correlator\Utils::fTestToPValue($f, 1, $ddf);
    }

    /**
     * Calculate ANOVA One-way f-value
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

        $nX = count($X);
        $nY = count($Y);

        $sumX = array_sum($X);
        $sumY = array_sum($Y);

        $meanX = $sumX / $nX;
        $meanY = $sumY / $nY;
        $meanT = ($sumX + $sumY) / ($nX + $nY);

        $ssb = ($nX * pow($meanX - $meanT, 2)) + ($nY * pow($meanY - $meanT, 2));
        $ssw = \Correlator\Utils::sumOfSquares(\Correlator\Utils::array_add($X, -$meanX)) + \Correlator\Utils::sumOfSquares(\Correlator\Utils::array_add($Y, -$meanY));

        return $ssb / ($ssw / ($nX + $nY - 2));
    }

}
