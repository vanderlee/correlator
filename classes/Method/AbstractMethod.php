<?php

namespace Correlator\Method;

/**
 * Makes correlating arrays easy.
 *
 * @author Martijn W. van der Lee
 */
abstract class AbstractMethod
{

    /**
     * List of scores
     * @var \Correlator\Correlator
     */
    protected $correlator = null;

    /**
     * Create a new classes with an array of scores to be correlated to
     * any number of factors.
     *
     * You should have atleast 6 scores for a meaningful result.
     *
     * @param \Correlator\Correlator $correlator
     */
    public function __construct(\Correlator\Correlator &$correlator)
    {
        $this->correlator = &$correlator;
    }

}
