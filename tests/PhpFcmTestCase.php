<?php
namespace paragraph1\phpFCM\Tests;

class PhpFcmTestCase extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }
}