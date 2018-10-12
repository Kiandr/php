<?php

namespace REW\Test;

use \Format;

class FormatTest extends \Codeception\Test\Unit
{

    /**
     * @var \REWTest\UnitTester
     */
    protected $tester;

    /**
     * @covers Format::slugify
     * @dataProvider provideSlugifyData
     */
    public function testFormatSlugify($input, $expected)
    {
        $slug = Format::slugify($input);
        $this->assertEquals($expected, $slug);
    }

    /**
     * Provide slugify input/output
     */
    public function provideSlugifyData()
    {
        return [
            ['Hello World!', 'hello-world'],
        ['Hello World ', 'hello-world'],
        ['Hello  World', 'hello-world']
        ];
    }
}
