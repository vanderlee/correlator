<?php

namespace Vanderlee\Correlator;

/**
 * Makes correlating arrays easy.
 *
 * @author Martijn W. van der Lee
 */
class Correlator
{

    const POPULATION = 0x00000000;
    const SAMPLE = 0x00000100; // for completeness; default
    const METHOD_PEARSONS_R = 0x01000000;
    const METHOD_SPEARMANS_RANK = 0x02000000;
    const METHOD_PEARSONS_CHI_SQUARED_GOODNESS_OF_FIT = 0x03000000;
    const METHOD_DISTANCE_CORRELATION = 0x04000000;
    const METHOD_ANOVA = 0x05000000;

    /**
     * METHOD_* constants to explicitely specify a method.
     * Masked 0xFF000000;
     */
    private static $method_bitmask = 0xFF000000;

    /**
     * Mapping of constants to classes.
     *
     * @var \Correlator\Method\AbstractMethod[]
     */
    private static $methods = array(
        self::METHOD_PEARSONS_R => '\Correlator\Method\PearsonsR',
        self::METHOD_SPEARMANS_RANK => '\Correlator\Method\SpearmansRank',
        self::METHOD_PEARSONS_CHI_SQUARED_GOODNESS_OF_FIT => '\Correlator\Method\PearsonsChiSquaredGoodnessOfFit',
        self::METHOD_DISTANCE_CORRELATION => '\Correlator\Method\DistanceCorrelation',
        self::METHOD_ANOVA => '\Correlator\Method\Anova',
    );

    /**
     * Correlate according to the specified flags.
     *
     * @param float[] $array
     * @param int $flags
     * @param float[] $weights Optional weights, if usable by the method
     */
    public function correlate($array, $flags = self::METHOD_PEARSONS_R, $weights = null)
    {
        $method = $flags & self::$method_bitmask;
        if (isset(self::$methods[$method])) {
            $Object = new self::$methods[$method]($this);
        } else {
            throw new \Exception('Wizard not yet implemented');
        }

        $is_sample = $flags & self::SAMPLE;

        return $Object->getValue($array, $is_sample, $weights);
    }

    /**
     * Correlate according to the specified flags.
     *
     * @param float[] $array
     * @param int $flags
     * @param float[] $weights Optional weights, if usable by the method
     */
    public function probability($array, $flags = self::METHOD_PEARSONS_R, $weights = null)
    {
        $method = $flags & self::$method_bitmask;
        if (isset(self::$methods[$method])) {
            $Object = new self::$methods[$method]($this);
        } else {
            throw new \Exception('Wizard not yet implemented');
        }

        $is_sample = $flags & self::SAMPLE;

        return $Object->getP($array, $is_sample, $weights);
    }

    /**
     * Get the fractional ranking of the scores
     *
     * @return float[]
     */
    public function getFractionalRanking()
    {
        if ($this->fractionalRanking === null) {
            $this->fractionalRanking = Utils::toFractionalRanking($this->scores);
        }
        return $this->fractionalRanking;
    }

    /**
     * Calculate Spearman's rho
     * Use for non-monotonic, linear intervals or ordinals
     *
     * @param self|float[] $other
     * @return float 0 indicates no correlation, sign indicates negative/positive correlations
     * @throws Exception
     */
    public function getSpearmansRank($other, $is_sample = false)
    {
        $Method = new Method\SpearmansRank($this);
        return $Method->getValue($other, $is_sample);
    }

    /**
     * Calculate Spearman's rho
     * Use for non-monotonic, linear intervals or ordinals
     *
     * @param self|float[] $other
     * @return float P value
     * @throws Exception
     */
    public function getPValueOfSpearmansRank($other, $is_sample = false)
    {
        $Method = new Method\SpearmansRank($this);
        return $Method->getP($other, $is_sample);
    }

    /**
     * Pearson product-moment correlation
     * Use for monotonic, linear intervals of samples of the population data
     *
     * @param self|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float [-1,1] where 0 indicates no correlation. Sign indicates positive/negative correlation.
     * @throws Exception
     */
    public function getPearsonsR($other, $is_sample = false)
    {
        $Method = new Method\PearsonsR($this);
        return $Method->getValue($other, $is_sample);
    }

    /**
     * Get the P-value for a Pearson product-moment correlation
     *
     * @param self|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float P value
     */
    public function getPValueOfPearsonsR($other, $is_sample = false)
    {
        $Method = new Method\PearsonsR($this);
        return $Method->getP($other, $is_sample);
    }

    /**
     * Get the distance correlation
     * Use for non-monotonic, non-linear intervals or ordinals
     *
     * @param self|float[] $other
     * @return float [-1,1] where 0 indicates no correlation. Sign has no meaning.
     * @throws Exception
     */
    public function getDistanceCorrelation($other)
    {
        $Method = new Method\DistanceCorrelation($this);
        return $Method->getValue($other);
    }

    /**
     * Get the distance correlation
     * Use for non-monotonic, non-linear intervals or ordinals
     *
     * @param self|float[] $other
     * @return float [-1,1] where 0 indicates no correlation. Sign has no meaning.
     * @throws Exception
     */
    public function getPValueOfDistanceCorrelation($other)
    {
        $Method = new Method\DistanceCorrelation($this);
        return $Method->getP($other);
    }

    /**
     * Run the Chi-squared Goodness of Fit test
     *
     * @param string[] $categories list of categories matching the scores
     * @param float[] $weights Relative weights to apply to the scores.
     * @return float Chi-squared critical value
     */
    public function getChiSquaredGoodnessOfFit(array $categories, array $weights = null)
    {
        $Method = new Method\PearsonsChiSquaredGoodnessOfFit($this);
        return $Method->getValue($categories, $weights);
    }

    /**
     * Get the P value of a Chi-squared Goodness of Fit test
     *
     * @param string[] $categories list of categories matching the scores
     * @param float[] $weights Relative weights to apply to the scores.
     * @return float P value
     */
    public function getPValueOfChiSquaredGoodnessOfFit(array $categories, array $weights = null)
    {
        $Method = new Method\PearsonsChiSquaredGoodnessOfFit($this);
        return $Method->getP($categories, $weights);
    }

    /**
     * ANOVA (ANalysis Of VAriance)
     *
     * @param self|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float [-1,1] where 0 indicates no correlation. Sign indicates positive/negative correlation.
     */
    public function getAnova($other, $is_sample = false)
    {
        $Method = new Method\Anova($this);
        return $Method->getValue($other, $is_sample);
    }

    /**
     * Get the P-value for ANOVA (ANalysis Of VAriance)
     *
     * @param self|float[] $other
     * @param boolean $is_sample true for Sample, false if entire Population
     * @return float P value
     */
    public function getPValueOfAnova($other, $is_sample = false)
    {
        $Method = new Method\Anova($this);
        return $Method->getP($other, $is_sample);
    }

}
