<?php

class DistanceCorrelationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers       \Correlator\Correlator->getSpearmansRank
     * @dataProvider provider_getValue
     */
    public function test_getValue($expected_dcorr, $expected_p, $source, $factor)
    {
        $object = new \Correlator\Correlator($source);
        $dcorr = $object->getDistanceCorrelation($factor, true);
        $this->assertEquals($expected_dcorr, $dcorr, '', 1E-13);

        $p = $object->getPValueOfDistanceCorrelation($factor, true);
        $this->assertEquals($expected_p, $p, '', 1E-13);
    }

    public function provider_getValue()
    {
        return [
            [
                0.8909126743510708,
                0.1888296935507856,
                [6, 9, 11, 20],
                [3, 5, 10, 12],
            ],
        ];
    }

}
