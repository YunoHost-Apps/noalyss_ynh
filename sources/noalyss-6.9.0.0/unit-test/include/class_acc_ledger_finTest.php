<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-08 at 14:20:36.
 * @backupGlobals enabled
 */
class Acc_Ledger_FinTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Acc_Ledger_Fin
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        include 'global.php';
        $this->object=new Acc_Ledger_Fin($g_connection,1);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Acc_Ledger_Fin::verify
     * @todo   Implement testVerify().
     * @expectedException Exception
     */
    public function testVerify()
    {
       
       $this->object->verify(null);
    }

    /**
     * @covers Acc_Ledger_Fin::input
     * @todo   Implement testInput().
     */
    public function testInput()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Acc_Ledger_Fin::confirm
     * @todo   Implement testConfirm().
     */
    public function testConfirm()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Acc_Ledger_Fin::insert
     * @todo   Implement testInsert().
     */
    public function testInsert()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Acc_Ledger_Fin::show_ledger
     * @todo   Implement testShow_ledger().
     */
    public function testShow_ledger()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Acc_Ledger_Fin::get_bank_name
     * @todo   Implement testGet_bank_name().
     */
    public function testGet_bank_name()
    {
       $name=$this->object->get_bank_name();
       var_export($name);
       if (strpos ($name,NOTFOUND) !=0 ) 
               $this->assertTrue(FALSE);
    }

    /**
     * @covers Acc_Ledger_Fin::get_bank
     * @todo   Implement testGet_bank().
     */
    public function testGet_bank()
    {
       $this->assertEquals(27,$this->object->get_bank());
    }

    /**
     * @covers Acc_Ledger_Fin::numb_operation
     * @todo   Implement testNumb_operation().
     */
    public function testNumb_operation()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Acc_Ledger_Fin::insert_quant_fin
     * @todo   Implement testInsert_quant_fin().
     */
    public function testInsert_quant_fin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
