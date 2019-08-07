<?php


namespace App\Tests;

use PHPUnit\Framework\TestCase;

class WeatherApiTest extends TestCase
{
    public function testgetStationsData()
    {
        $response = methodTest::getResponse('WeatherApiController', 'getStationsData');

        $arrayFromJson = methodTest::checkJson($response);

        methodTest::checkArray($arrayFromJson,['status','message']);

        $this->assertInternalType("int", $arrayFromJson['status']);

        $this->assertSame($arrayFromJson['status'],200);

        methodTest::checkArray($arrayFromJson['message'],['body','status','time_exec','time_server']);

        $this->assertSame($arrayFromJson['message']['status'],'ok');

        methodTest::checkArray($arrayFromJson['message']['body'],['devices','user']);
    }
}