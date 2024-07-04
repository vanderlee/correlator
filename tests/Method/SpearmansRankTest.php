<?php

class SpearmansRankTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers       \Correlator\Correlator->getSpearmansRank
     * @dataProvider provider_getValue_sample
     */
    public function test_getValue_sample($expected_rho, $expected_p, $source, $factor)
    {
        $object = new \Correlator\Correlator($source);
        $rho = $object->getSpearmansRank($factor, true);
        $this->assertEquals($expected_rho, $rho, '', 1E-13);

        $p = $object->getPValueOfSpearmansRank($factor, true);
        $this->assertEquals($expected_p, $p, '', 1E-13);
    }

    public function provider_getValue_sample()
    {
        return [
            [
                -0.9,
                0.037386073468498648,
                [4, 3, -2, 7, 11],
                [1, 5, 3, 0, -2],
            ],
        ];
    }

}
