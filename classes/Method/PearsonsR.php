<?php

namespace Vanderlee\Correlator\Method;

use Exception;
use Vanderlee\Correlator\Collection;
use Vanderlee\Correlator\Utils;

/**
 * Makes correlating arrays easy.
 *
 * @author Martijn W. van der Lee
 */
class PearsonsR implements Correlator
{
    /** @var Collection */
    private $a;

    /** @var Collection */
    private $b;

    /** @var bool */
    private $sample;

    public function __construct(Collection $a, Collection $b, bool $sample = false)
    {
        $this->a = $a;
        $this->b = $b;
        $this->sample = $sample;
    }

    public function correlation(): float
    {
        return $this->getR();
    }

    public function probability(): float
    {
        return $this->getP();
    }

    /**
     * Get the P-value for a Pearson product-moment correlation
     *
     * @return float
     * @throws Exception
     */
    private function getP(): float
    {
        $n = count($this->a);
        $r = $this->getR();
        $t = Utils::pearsonsRToStudentT($r, $n);
        return Utils::studentTToPValue($t, $n);
    }

    /**
     * Pearson product-moment correlation coefficient.
     * (also known as Pearson's R, PPMCC and PCC)
     * Use for monotonic, linear intervals of samples of the population data.
     *
     * @return float [-1,1] where 0 indicates no correlation. Sign indicates positive/negative correlation.
     * @throws Exception
     */
    private function getR()
    {
        if (count($this->a) !== count($this->b)) {
            // @todo better defined exception
            throw new Exception('Different lengths');
        }

        $count = count($this->a);

        $sampleCount = $this->sample ? $count - 1 : $count;
        $sop = $this->a->sumOfProduct($this->b);

        // @todo move sumOfProducts to Collection
        return (Utils::sumOfProducts($this->a->scores(), $this->b->scores())
                - ($count * $this->a->mean() * $this->b->mean())
            )
            / ($sampleCount * $this->a->stddev() * $this->b->stddev());
    }
}
