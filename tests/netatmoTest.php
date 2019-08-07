<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class netatmoControllerTest extends TestCase
{
    public function testgetToken()
    {
        $token = methodTest::getResponse('NetatmoController', 'getToken');

        $arrayFromJson = methodTest::checkJson($token);

        methodTest::checkArray($arrayFromJson, ['status','message']);

        $first=$arrayFromJson['status'];
        $second=$arrayFromJson['message'];
        $this->assertInternalType("int", $first);

        methodTest::checkArray($second,['access_token','refresh_token','scope','expires_in','expire_in']);
        $this->assertInternalType("string", $second['access_token']);
        $this->assertInternalType("string", $second['refresh_token']);
        $this->assertInternalType("array", $second['scope']);
        $this->assertInternalType("int", $second['expires_in']);
        $this->assertInternalType("int", $second['expire_in']);
    }

    public function testgetUser()
    {
        $response = methodTest::getResponse('NetatmoController', 'getUser');

        $arrayFromJson = methodTest::checkJson($response);

        methodTest::checkArray($arrayFromJson,['status','message']);

        $this->assertInternalType("int", $arrayFromJson['status']);

        methodTest::checkArray($arrayFromJson['message'],['body','status','time_exec','time_server']);

        $this->assertSame($arrayFromJson['message']['status'], 'ok');

        methodTest::checkArray($arrayFromJson['message']['body'],['_id','mail','administrative']);
    }

    public function testgetMeasure()
    {
        $response = methodTest::getResponse('NetatmoController', 'getMeasure');

        $arrayFromJson = methodTest::checkJson($response);

        methodTest::checkArray($arrayFromJson,['status','message']);

    }


}