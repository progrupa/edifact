<?php

namespace EDI\Tests;

use EDI\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageUnwrap()
    {
        $p = $this->givenParser();
        $string = "LOC+9+VNSGN'LOC+11+ITGOA'MEA+WT++KGM:9040'";

        $test = $p->unwrapString($string);

        $expected = array("LOC+9+VNSGN'","LOC+11+ITGOA'","MEA+WT++KGM:9040'");
        $this->assertEquals($expected, $test);
    }

    public function testParseSimple()
    {
        $p = $this->givenParser();
        $array = array("LOC+9+VNSGN'","LOC+11+ITGOA'","MEA+WT++KGM:9040'");
        $expected = [["LOC","9","VNSGN"],["LOC","11","ITGOA"],["MEA","WT","",["KGM","9040"]]];

        $p->parse($array);

        $result = $p->getParsedData();
        $this->assertEquals($expected, $result);
    }

    public function testEscapedSegment()
    {
        $p = $this->givenParser();
        $string = "EQD+CX??DU12?+3456+2?:0'";
        $expected = [["EQD","CX?DU12+3456","2:0"]];

        $p->parse($string);

        $result = $p->getParsedData();
        $this->assertEquals($expected, $result);
    }

    public function testNotEscapedSegment()
    {
        $p = $this->givenParser();
        $string = "EQD+CX?DU12?+3456+2?:0'";
        $expected = [["EQD","CX?DU12+3456","2:0"]];

        $p->parse($string);

        $experror = "There's a character not escaped with ? in the data; string CX?DU12?+3456";
        $this->assertEquals($expected, $p->getParsedData());
        $this->assertContains($experror, $p->errors());
    }

    public function testNotTerminatedSegment()
    {
        $p = $this->givenParser();
        $string = "LOC+9+VNSGN\nLOC+11+ITGOA'";

        $p->parse($string);

        $error = "Segment not ended correctly at line 1=>LOC+9+VNSGN";
        $this->assertContains($error, $p->errors());
    }

    public function testNoErrors()
    {
        $p = $this->givenParser();
        $string = "LOC+9+VNSGN'\nLOC+11+ITGOA'";
        $p->parse($string);
        $this->assertEmpty($p->errors());
    }

    /**
     * @return Parser
     */
    protected function givenParser()
    {
        $p = new Parser();

        return $p;
    }
}
?>
