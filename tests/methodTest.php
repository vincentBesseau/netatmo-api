<?php


namespace App\Tests;

use PHPUnit\Framework\TestCase;

class methodTest
{
    public static function checkArray(array $arrayToCheck, array $fields) {

        TestCase::assertInternalType('array',$arrayToCheck);
        TestCase::assertEquals(count($fields),count($arrayToCheck));
        foreach ($fields as $field) {
            TestCase::assertArrayHasKey($field, $arrayToCheck);
        }
    }

    public static function checkJson(string $response) {
        TestCase::assertJson($response);
        $arrayFromJson = json_decode($response, true);

        self::checkArray($arrayFromJson,['status','message']);

        return $arrayFromJson;
    }

    public static function getResponse(string $class, string $method, array $options = []) {
        return file_get_contents(__DIR__."/mock/$method-$class.json");
    }
}