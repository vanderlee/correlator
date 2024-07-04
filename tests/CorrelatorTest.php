<?php

class CorrelatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers \Correlator\Correlator::__construct
     */
    public function test__Constructor()
    {
        $object = new \Correlator\Correlator([]);

        $this->assertInstanceOf('\Correlator\Correlator', $object);
    }

    /**
     * @covers \Correlator\Correlator->getSpearmansRank
     */
    public function test_getSpearmansRank_sample()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $rho = $object->getSpearmansRank([1, 2, 9, 4, 4], true);
        $this->assertEquals(0.66688592885535, $rho, '', 1E-13);
    }

    /**
     * @covers \Correlator\Correlator->getSpearmansRank
     */
    public function test_getSpearmansRank_population()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $rho = $object->getSpearmansRank([1, 2, 9, 4, 4], false);
//		$this->assertEquals(0.6668859288554, $rho, '', 1E-13);
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfSpearmansRank
     */
    public function test_getPValueOfSpearmansRank_sample()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $p = $object->getPValueOfSpearmansRank([1, 2, 9, 4, 4], true);
        $this->assertEquals(0.21889398131323179, $p, '', 1E-13);
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfSpearmansRank
     */
    public function test_getPValueOfSpearmansRank_population()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getPearsonsR
     */
    public function test_getPearsonsR_sample()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $rho = $object->getPearsonsR([1, 2, 9, 4, 4], true);
        $this->assertEquals(0.41039134083406165, $rho, '', 1E-13);
    }

    /**
     * @covers \Correlator\Correlator->getPearsonsR
     */
    public function test_getPearsonsR_population()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $rho = $object->getPearsonsR([1, 2, 9, 4, 4]);
        $this->assertEquals(0.41039134083406, $rho, '', 1E-13);
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfPearsonsR
     */
    public function test_getPValueOfPearsonsR_sample()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $p = $object->getPValueOfPearsonsR([1, 2, 9, 4, 4], true);
        $this->assertEquals(0.49253578170279755, $p, '', 1E-13);
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfPearsonsR
     */
    public function test_getPValueOfPearsonsR_population()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getDistanceCorrelation
     */
    public function test_getDistanceCorrelation()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $dcorr = $object->getDistanceCorrelation([1, 2, 9, 4, 4], true);
        $this->assertEquals(0.762676242417, $dcorr, '', 1E-12);
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfDistanceCorrelation
     */
    public function test_getPValueOfDistanceCorrelation()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getChiSquaredGoodnessOfFit
     */
    public function test_getChiSquaredGoodnessOfFit_unweighted()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getChiSquaredGoodnessOfFit
     */
    public function test_getChiSquaredGoodnessOfFit_weighted()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfChiSquaredGoodnessOfFit
     */
    public function test_getPValueOfChiSquaredGoodnessOfFit_unweighted()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfChiSquaredGoodnessOfFit
     */
    public function test_getPValueOfChiSquaredGoodnessOfFit_weighted()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Correlator\Correlator->getAnova
     */
    public function test_getAnova()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $f = $object->getAnova([1, 2, 9, 4, 4, 4], true);
        $this->assertEquals(0.51136363636364, $f, '', 1E-13);
    }

    /**
     * @covers \Correlator\Correlator->getPValueOfAnova
     */
    public function test_getPValueOfAnova()
    {
        $object = new \Correlator\Correlator([1, 2, 3, 4, 5]);
        $p = $object->getPValueOfAnova([1, 2, 9, 4, 4, 4], true);
        $this->assertEquals(0.49268104880561, $p, '', 1E-13);
    }

}
