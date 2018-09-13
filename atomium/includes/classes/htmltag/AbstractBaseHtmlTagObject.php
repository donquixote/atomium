<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag;

/**
 * Class AbstractBaseHtmlTagObject
 */
abstract class AbstractBaseHtmlTagObject
{
    /**
     * Normalize a value.
     *
     * @param mixed $values
     *  The value to normalize.
     *
     * @return array
     *   The value normalized.
     */
    protected function normalizeValue($values)
    {
        return array_unique(
            array_reduce(
                array_map(
                    array($this, 'ensureArray'),
                    $this->ensureFlatArray($this->ensureArray($values))
                ),
                'array_merge',
                []
            )
        );
    }

    /**
     * Transform a multidimensional array into a flat array.
     *
     * @param mixed[] $array
     *   The input array.
     *
     * @return mixed[]
     *   The array with only one dimension.
     */
    protected function ensureFlatArray(array $array)
    {
        return array_reduce(
            $array,
            function ($carry, $item) {
                return is_array($item) ?
                    array_merge($carry, $this->ensureFlatArray($item)) :
                    array_merge($carry, [$item]);
            },
            []
        );
    }

    /**
     * Make sure the value is an array.
     *
     * @param mixed $value
     *   The input value.
     *
     * @return array
     *   The input value in an array.
     */
    protected function ensureArray($value)
    {
        $return = null;

        switch (gettype($value)) {
            case 'array':
                return $value;
            case 'string':
                $return = $value;
                break;
            case 'integer':
            case 'double':
                $return = (string) $value;
                break;
            case 'object':
                if (method_exists($value, '__toString')) {
                    $return = (string) $value;
                }
                break;
            case 'boolean':
                if (true === $value) {
                    $return = '';
                }
                break;
        }

        return null === $return ?
            []:
            explode(' ', preg_replace('!\s+!', ' ', trim(addcslashes($return, '\"'))));
    }
}
