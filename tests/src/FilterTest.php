<?php

use MongoDriver\Filter;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorWrongOperator()
    {
        $result = true;

        try { new Filter('field', ['value'], 'NotExistingOperator'); }
        catch (Exception $ex) { $result = false; }

        $this->assertFalse($result);
    }

    public function testConstructorWrongParameterArray()
    {
        $result = true;

        try { new Filter('field', ['value']); }
        catch (Exception $ex) { $result = false; }

        $this->assertFalse($result);
    }

    public function testConstructorWrongParameterRangeCount()
    {
        $result = true;

        try { new Filter('field', ['value'], Filter::IS_IN_RANGE); }
        catch (Exception $ex) { $result = false; }

        $this->assertFalse($result);
    }

    public function testConstructorWrongParameterValue()
    {
        $result = true;

        try { new Filter('field', 'value', Filter::IS_IN_ARRAY); }
        catch (Exception $ex) { $result = false; }

        $this->assertFalse($result);
    }

    public function testConstructorGoodParameterArray()
    {
        $result = true;

        try { new Filter('field', ['value'], Filter::IS_IN_ARRAY); }
        catch (Exception $ex) { $result = false; }

        $this->assertTrue($result);
    }

    public function testConstructorGoodParameterRangeCount()
    {
        $result = true;

        try { new Filter('field', ['value1', 'value2'], Filter::IS_IN_RANGE); }
        catch (Exception $ex) { $result = false; }

        $this->assertTrue($result);
    }

    public function testConstructorGoodParameterValue()
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

    public function testGetFilterInRange()
    {
        $filter = new Filter('field', ['value1', 'value2'], Filter::IS_IN_RANGE);
        $this->assertEquals
        (
            [
                'field' =>
                    [
                        Filter::IS_GREATER_THAN_OR_EQUAL => 'value1',
                        Filter::IS_LESS_THAN_OR_EQUAL => 'value2'
                    ]
            ],
            $filter->getFilter()
        );
    }
}