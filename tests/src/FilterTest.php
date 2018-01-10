<?php

use MongoDriver\Filter;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorWrongParameters()
    {
        $result = true;

        try { new Filter('field', ['value']); }
        catch (Exception $ex) { $result = false; }

        $this->assertFalse($result);
    }

    public function testConstructorGoodParameters()
    {
        $result = true;

        try { new Filter('field', 'value'); }
        catch (Exception $ex) { $result = false; }

        $this->assertTrue($result);
    }

    public function testGetFilter()
    {
        $filter = new Filter('field', 'value');
        $this->assertEquals(['field' => [Filter::IS_EQUAL => 'value']], $filter->getFilter());
    }
}