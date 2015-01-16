<?php

namespace Micro\tests;


class HelloTest extends \PHPUnit_Framework_TestCase
{
    public function up()
    {
        //
    }

    public function down()
    {
        //
    }

    public function testHello()
    {
        $this->assertEquals( '200 Ok', '200 Ok' );
    }
}