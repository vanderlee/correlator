<?php
declare(strict_types=1);

namespace Vanderlee\Correlator\Method;

use Vanderlee\Correlator\Collection;

interface Correlator
{
    public function __construct(Collection $a, Collection $b, bool $sample = false);

    public function correlation(): float;

    public function probability(): float;
}