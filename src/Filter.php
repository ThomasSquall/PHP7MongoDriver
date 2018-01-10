<?php

namespace MongoDriver;

use Exception;

class Filter
{
    const IS_EQUAL = '$eq';
    const IS_GREATER_THAN = '$gt';
    const IS_GREATER_THAN_OR_EQUAL = '$gte';
    const IS_IN_ARRAY = '$in';
    const IS_LESS_THAN = '$lt';
    const IS_LESS_THAN_OR_EQUAL = '$lte';
    const IS_NOT_EQUAL = '$ne';
    const IS_NOT_IN_ARRAY = '$nin';
    const IS_IN_RANGE = '$range';

    private $availableOperators =
    [
        'IS_EQUAL' => self::IS_EQUAL,
        'IS_GREATER_THAN' => self::IS_GREATER_THAN,
        'IS_GREATER_THAN_OR_EQUAL' => self::IS_GREATER_THAN_OR_EQUAL,
        'IS_IN_ARRAY' => self::IS_IN_ARRAY,
        'IS_LESS_THAN' => self::IS_LESS_THAN,
        'IS_LESS_THAN_OR_EQUAL' => self::IS_LESS_THAN_OR_EQUAL,
        'IS_NOT_EQUAL' => self::IS_NOT_EQUAL,
        'IS_NOT_IN_ARRAY' => self::IS_NOT_IN_ARRAY,
        'IS_IN_RANGE' => self::IS_IN_RANGE
    ];

    private $field;
    private $value;
    private $operator;

    /**
     * Filter constructor.
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @throws Exception
     */
    public function __construct($field, $value, $operator = self::IS_EQUAL)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;

        if (!in_array($this->operator, $this->availableOperators))
        {
            throw new Exception("Operator $this->operator is not available!");
        }

        switch ($this->operator)
        {
            case self::IS_IN_ARRAY:
            case self::IS_NOT_IN_ARRAY:
            case self::IS_IN_RANGE:
                if (!is_array($this->value))
                {
                    throw new Exception
                    (
                        "The operator " .
                        array_search($this->operator, $this->availableOperators) .
                        " accepts only arrays as given value. " .
                        ucfirst(gettype($this->value)) .
                        " provided."
                    );
                }

                if ($this->operator === self::IS_IN_RANGE)
                {
                    $count = count($this->value);

                    if ($count != 2)
                    {
                        throw new Exception
                        (
                            "The operator " .
                            array_search($this->operator, $this->availableOperators) .
                            " accepts only 2 values arrays as given value. " .
                            $count .
                            " values provided."
                        );
                    }
                }

                break;
            default:
                if (is_array($this->value) || is_object($this->value))
                {
                    throw new Exception
                    (
                        "The operator " .
                        array_search($this->operator, $this->availableOperators) .
                        " accepts only primitive types as given value. " .
                        ucfirst(gettype($this->value)).
                        " provided."
                    );
                }

                break;
        }
    }

    /**
     * Returns the filter array.
     * @return array
     */
    public function getFilter()
    {
        switch ($this->operator)
        {
            case self::IS_IN_RANGE:
                $result =
                [
                    $this->field =>
                    [
                        self::IS_GREATER_THAN_OR_EQUAL => $this->value[0],
                        self::IS_LESS_THAN_OR_EQUAL => $this->value[1]
                    ]
                ];

                break;
            default:
                $result = [$this->field => [$this->operator => $this->value]];

                break;
        }

        return $result;
    }
}