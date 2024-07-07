<?php

namespace Vanderlee\Correlator\Method;

/**
 * Get the distance correlation
 * Use for non-monotonic, non-linear intervals or ordinals
 *
 * @author Martijn W. van der Lee
 */
class DistanceCorrelation extends \Correlator\Method\AbstractMethod
{


    /**
     * Get the distance correlation
     * Use for non-monotonic, non-linear intervals or ordinals
     *
     * @param self|float[] $other
     * @return float [-1,1] where 0 indicates no correlation. Sign has no meaning.
     * @throws \Exception
     */
    public function getP($other)
    {
        $n = count($other);
        $Rn = $this->getValue($other);
        $t = self::distanceCorrelationToStudentT($Rn, $n);
        return \Correlator\Utils::studentTToPValue($t, $n);
    }

    /**
     * Get the distance correlation
     * Use for non-monotonic, non-linear intervals or ordinals
     *
     * @param \Correlator\Correlator|float[] $other
     * @return float [-1,1] where 0 indicates no correlation. Sign has no meaning.
     * @throws \Exception
     */
    public function getValue($other)
    {
        $X = &$this->correlator->getDistanceMatrix();

        if ($other instanceof \Correlator\Correlator) {
            $Y = &$other->getDistanceMatrix();
        } else {
            $Y = &\Correlator\Utils::arrayToDistanceMatrix($other);
        }

        if (count($X) !== count($Y)) {
            throw new \Exception('Different lengths');
        }

        $dVarX = $dVarY = $dCovXY = 0;
        foreach ($X as $index => $x) {
            $y = &$Y[$index];
            $dVarX += $x * $x;
            $dVarY += $y * $y;
            $dCovXY += $x * $y;
        }

        return sqrt($dCovXY / (sqrt($dVarX) * sqrt($dVarY)));
    }

    /**
     * Get T for distance correlation.
     *
     * Source:
     *    http://www.sciencedirect.com/science/article/pii/S0047259X13000262 (2.4.3)
     *
     * @param float $Rn result of distance correlation Rn(X,Y)
     * @param integer $n number of pairs. >= 4 is unbiased (~= usable)
     */
    private static function distanceCorrelationToStudentT($Rn, $n)
    {
        if ($n < 4) {
            throw new \Exception('Cannot calculate Student T for distance correlation with less than 4 data points');
        }

        $v = ($n * ($n - 3)) / 2;
        $t = sqrt($v - 1) * ($Rn / sqrt(1 - ($Rn * $Rn)));
        return $t;
    }

}
