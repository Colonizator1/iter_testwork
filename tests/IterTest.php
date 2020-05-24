<?php

namespace Iter\Tests;

use PHPUnit\Framework\TestCase;
use Iter\LogsRepository;

class IterTest extends TestCase
{

    public function testRepository()
    {
        $getFilePath = function ($db) {
            return __DIR__ . "/fixtures/{$db}";
        };

        $repo = new LogsRepository($getFilePath("db"));
        //Clean test Db file
        $this->assertEquals(0, file_put_contents($getFilePath("db"), ''));

        for ($i = 1; $i <= 30; $i++) {
            $repo->save([$i,'$i.0.$i.0', "28.10.2020 10:{$i}", 'Success', "Add some text with random {$i}", "1.{$i}"]);
        }
        $this->assertEquals(30, $repo->getLogsCount());
        $this->assertEquals(30, count($repo->all()));

        $expectedLog = $repo->getLogsByPage(2)[5][4];
        $someLog = $repo->all()[10][4];

        $this->assertEquals('Add some text with random 16', $expectedLog);
        $this->assertEquals('Add some text with random 11', $someLog);

        $testLog = ['1','1.0.0.0', '12.10.2020 10:30', 'Wait', 'Some text', '1.0'];
        $repo->save($testLog);
        $body = file_get_contents($getFilePath("db"));
        $this->assertStringContainsString('Some text', $body);

        $testWithSemicolon = ['2','2.0.0.0', '10.10.2020 10:30', 'Debug', 'Some text with; semicolon', '1.1'];
        $repo->save($testWithSemicolon);
        $body2 = file_get_contents($getFilePath("db"));
        $this->assertStringContainsString('Some text with%3B semicolon', $body2);

        //Clean test Db file
        $this->assertEquals(0, file_put_contents($getFilePath("db"), ''));
    }
}
