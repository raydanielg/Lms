<?php

namespace App\Bitwise;

class Bitwise
{
    /**
     * @return array
     */
    public function getList()
    {
        $class = new \ReflectionClass($this);
        return $class->getConstants();
    }

    /**
     * @param string|array|integer $keys
     * @return integer|null
     */
    public function getValue($keys)
    {
        $list = $this->getList();
        $keysList = array_keys($list);
        $value = null;

        if (is_array($keys) && count($keys)) {
            $value = [];

            foreach ($keys as $key) {
                $value[] = array_search(strtoupper($key), $keysList);
            }

            asort($value);
            $value = array_values($value);

            $bitsLength = $value[count($value) - 1] + 1;
            $bits = array_fill(0, $bitsLength, 0);

            for ($i = 0; $i <= $bitsLength; $i++) {
                if (in_array($i, $value)) {
                    $bits[$i] = 1;
                }
            }

            $value = base_convert(implode('', array_reverse($bits)), 2, 10);
        } else if (is_string($keys) && $keys !== '') {
            $value = $list[strtoupper($keys)];
        }

        return $value ? (int)$value : null;
    }

    public function getName($value)
    {
        $list = $this->getList();
        $keys = array_keys($list);
        $bin = base_convert($value, 10, 2);
        $binary = str_split($bin);
        $result = [];

        if (!empty($binary) && is_array($binary)) {
            $binary = array_reverse($binary);
        }

        foreach ($binary as $index => $bit) {
            if ($bit == 1 and !empty($keys[$index])) {
                $result[] = strtolower($keys[$index]);
            }
        }

        return count($result) === 1 ? $result[0] : $result;
    }

    public function hasValue($value, $source)
    {
        return ($source & $value) === $value;
    }

    public function hasName($name, $source)
    {
        $value = $this->getValue($name);
        return $this->hasValue($value, $source);
    }
}
