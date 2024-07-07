<?php

namespace Vanderlee\Correlator;

use Countable;

/**
 * @todo implement array access; invalidate or fix caches if possible
 */
class Collection implements Countable
{

    /**
     * List of scores
     *
     * @var float[]
     */
    private $scores = [];

    /**
     * Matrix (linear!) of distances between scores.
     *
     * @var float[]
     */
    private $distanceMatrix;

    /**
     * Mean average
     *
     * @var float
     */
    private $mean;

    /**
     * Standard deviation for samples
     *
     * @var float
     */
    private $standardDeviationSample;

    /**
     * Standard deviation for population
     *
     * @var float
     */
    private $standardDeviationPopulation;

    /**
     * Fractional ranking of scores
     *
     * @var float[]
     */
    private $fractionalRanking;

    /**
     * Create a new classes with an array of scores to be correlated to
     * any number of factors.
     *
     * You should have atleast 6 scores for a meaningful result.
     *
     * Scores are assumed to be atleast interval.
     *
     * @param float[] $scores
     */
    public function __construct(array $scores)
    {
        $this->scores = &$scores;

        // Invalidate caches
        $this->distanceMatrix = null;
        $this->mean = null;
        $this->standardDeviationPopulation = null;
        $this->standardDeviationSample = null;
        $this->fractionalRanking = null;
    }

    /**
     * Get the scores
     *
     * @return float[]
     */
    public function scores(): array
    {
        return $this->scores;
    }

    /**
     * Get the number of scores
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->scores);
    }

    /**
     * Get a distance matrix for the scores
     *
     * @return float[]
     */
    public function distanceMatrix(): array
    {
        if ($this->distanceMatrix === null) {
            return $this->distanceMatrix = Utils::arrayToDistanceMatrix($this->scores);
        }

        return $this->distanceMatrix;
    }

    /**
     * Get the standard deviation
     *
     * @param bool $sample
     * @return float
     */
    public function stddev(bool $sample = false): float
    {
        if ($sample) {
            if ($this->standardDeviationSample === null) {
                $this->standardDeviationSample = Utils::standardDeviation($this->scores, true, $this->mean());
            }
            return $this->standardDeviationSample;
        } else {
            if ($this->standardDeviationPopulation === null) {
                $this->standardDeviationPopulation = Utils::standardDeviation($this->scores, false, $this->mean());
            }
            return $this->standardDeviationPopulation;
        }
    }

    /**
     * Get the mean average
     *
     * @return float
     */
    public function mean(): float
    {
        if ($this->mean === null) {
            $this->mean = Utils::mean($this->scores);
        }

        return $this->mean;
    }
}