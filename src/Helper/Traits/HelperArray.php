<?php

namespace Weirdo\Helper\Traits;

use Weirdo\Helper\Traits\Helper;
use Illuminate\Support\Collection;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait HelperArray
{
    use Helper;

    /**
     * Obtener la fecha de inicio final una semana
     * @param string $date
     * @return array
     */
    public function getFinalStartDateOfOneWeek($date)
    {
        $dayStart = "Monday";
        $dayEnd = "Sunday";
        $srtDate = strtotime($date);
        $startDate = date('Y-m-d', strtotime('last ' . $dayStart, $srtDate));
        $finalDate = date('Y-m-d', strtotime('next ' . $dayEnd, $srtDate));

        if (date("l", $srtDate) == $dayStart) {
            $startDate = date("Y-m-d", $srtDate);
        }
        if (date("l", $srtDate) == $dayEnd) {
            $finalDate = date("Y-m-d", $srtDate);
        }

        return array(
            "start_date" => $startDate,
            "final_date" => $finalDate
        );
    }

    /**
     * Obtener la fecha de inicio final de una semana atrás
     * @param string $date
     * @return array
     */
    public function getScheduleDate($date)
    {
        $strDate = strtotime("{$date} -1 week");
        $currentDate = date('Y-m-d', $strDate);

        return $this->getFinalStartDateOfOneWeek($currentDate);
    }

    /**
     * Obtener la fecha de inicio final de una semana adelante
     * @param string $date
     * @return array
     */
    public function getActiveSchedulingDate($date)
    {
        $strDate = strtotime("{$date} 1 week");
        $currentDate = date('Y-m-d', $strDate);

        return $this->getFinalStartDateOfOneWeek($currentDate);
    }

    /**
     * Obtiene los valores con claves numéricas secuenciales.
     * @param array $values
     * @return array
     */
    public function getValues(array $values)
    {
        $tmps = [];
        foreach ($values as $value) {
            $tmps[] = $value;
        }

        return $tmps;
    }

    /**
     * Encuentra la primera ocurrencia del valor dado de un arreglo.
     * @param array $array
     * @param string|int $value
     * @param boolean $strict
     * @return mixed
     */
    public function arrayFirst(array $array, $value, $strict = true)
    {
        if (is_numeric($value)) {
            $value = $this->filterVar($value, FILTER_VALIDATE_INT);
        }
        if (!is_null($this->filterVar($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
            $value = $this->filterVar($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return array_first($array, function ($current, $key) use ($value, $strict) {
            if (($strict === true) && (is_numeric($current))) {
                $current = $this->filterVar($current, FILTER_VALIDATE_INT);
            } elseif (($strict === true) && ($this->filterVar($current, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true || $this->filterVar($current, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === false)) {
                $current = $this->filterVar($current, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }

            return $strict === true ? ($value === $current) : ($value == $current);
        });
    }

    /**
     * Encuentra la primera ocurrencia del valor dado de un arreglo y devuelve la llave.
     * @param array $array
     * @param string|int $value
     * @return mixed
     */
    public function arrayFirstIndex(array $array, $value)
    {
        while ($currentValue = current($array)) {
            if (isset($currentValue) && $currentValue === $value) {
                return key($array);
            }
            next($array);
        }

        return -1;
    }

    /**
     * @param array $array
     * @return void
     */
    private function optimizeVerifiedArrayKeys(array &$array)
    {
        $keys = array_keys($array);
        foreach ($array as $keyPa => $t1value) {
            if (!is_array($t1value)) {
                $array[$keyPa] = $t1value;
            } else {
                foreach ($t1value as $keyCh => $t2value) {
                    $result = $this->arrayFirst($keys, $keyCh);
                    if (!empty($result)) {
                        $array["{$keyCh}{$keyPa}"] = $t2value;
                    } else {
                        $array[$keyCh] = $t2value;
                    }
                }
                unset($array[$keyPa]);
            }
        }
    }

    /**
     * Inserta en el arreglo verificando el valor
     * @param array $array
     * @param array|string|int $value
     * @return array
     */
    public function arrayInsertWithoutDoesNotExist(array $array, $value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $rv) {
                $search = $this->filterVar($key, FILTER_VALIDATE_INT) !== false ? $rv : [$key => $rv];
                $result = $this->arrayFirstWith($array, $search);
                if (is_null($result)) {
                    array_push($array, $search);
                }
            }
            $this->optimizeVerifiedArrayKeys($array);
        } else {
            $result = $this->arrayFirst($array, $value);
            if (is_null($result)) {
                array_push($array, $value);
            }
        }

        return $array;
    }

    /**
     * Obtiene un subconjunto de los elementos de la matriz dada usando las llaves.
     * @param array $datos
     * @param array $header
     * @return array
     */
    public function arrayOnly(array $datos = [], array $header = [])
    {
        if (count($header) == 0) {
            $header = ['id', 'display_name'];
        }

        $temp = [];
        foreach ($datos as $data) {
            if (!is_array($data)) {
                $data = (array) $data;
            }

            $currentData = current($data);
            $onlyData = array_only($data, $header);
            if (gettype($currentData) === "object" && count($onlyData) === 0) {
                $temp[] = $this->arrayOnly($data, $header);
            } elseif (gettype($currentData) === "array" && count($onlyData) === 0) {
                $temp[] = $this->arrayOnly($data, $header);
            } else {
                $temp[] = $onlyData;
            }
        }

        return $temp;
    }

    /**
     * Obtiene valores de un arreglo según las llaves dadas.
     * @param string[] $array
     * @param string|int|array $value
     * @param boolean $includeKeys
     * @return array
     */
    public function arrayFirstPerKey(array $array, $value, $includeKeys = true)
    {
        $value = (array) $value;
        $result = [];
        $sw = false;

        while (($currentValue = current($array)) && ($sw === false)) {
            if (!is_array($currentValue) && in_array(key($array), $value) === true) {
                $result = (array) $currentValue;
                $sw = true;
            } elseif (is_array($currentValue) && array_has($currentValue, $value) === true) {
                $sw = true;
                $result = $currentValue;
            }

            next($array);
        }

        if (!$includeKeys) {
            return $this->getValues($result);
        }

        return $result;
    }

    /**
     * @param array $array
     * @param array $keys
     * @return array
     */
    private function __onlyValuesWith(array $array, array $keys)
    {
        $tmpKeys = [];
        foreach ($keys as $indexPa => $key) {
            $parts = explode('.', $key);
            $firstIndex = 0;
            $tmpKeys[$indexPa][$parts[$firstIndex]] = null;
            while ($firstIndex < 2) {
                $result = $this->getAllAssociatedKeyValues($tmpKeys[$indexPa], [$parts[$firstIndex] => []]);
                if (!empty($result)) {
                    $tmpKeys[$indexPa][$result[0]] = [
                        $parts[$firstIndex] => array_get($array, $key),
                    ];
                }
                $firstIndex++;
            }
        }

        return $tmpKeys;
    }

    /**
     * @param mixed $current
     * @param mixed $key
     * @param mixed $value
     * @param boolean $with_expression
     * @return array
     */
    private function _arrayFirstColumn($current, $key, $value, $with_expression = false)
    {
        if (gettype($current) === 'object') {
            $current = (array) $current;
        }

        $result = [];
        // LLaves de los valores a buscar
        /** @var string[]|integer[] */
        $keys = is_array($value) ? array_keys($value) : [$key];
        // Verificamos que las llaves sean padre.hijo => test
        /** @var bool */
        $withSubquery = $this->verifyKeysOneADelimiter($keys, '.');
        // Verificamos que las llaves sean del tipo int
        /** @var bool */
        $typeInt = $this->verifyOnlyOneType($keys, 'integer');
        if ($withSubquery === true) {
            $result = array_first($this->__onlyValuesWith($current, $keys));
        } elseif (is_array($current) && $typeInt === false) { // String
            // Obtenemos un subconjunto de los elementos de la matriz dada.
            $result = array_only($current, $keys);
        } else {
            if (is_array($current)) {
                $result = $current;
                $keys = array_keys($current);
            } else {
                $result[$key] = $current;
            }
        }

        $index = 0;
        $sw = false;
        $found = false;
        $newResult = [];
        $newValue = [];
        while ($index < count($keys)) {
            $newKey = $keys[$index];
            $newValue = isset($result[$newKey]) ? $result[$newKey] : [];
            if ($with_expression === false) {
                if (is_array($value) && !is_array(current($value)) && $typeInt === false && $withSubquery === false) {
                    $found = in_array($newValue, array_column([$value], $newKey), true);
                    $newResult[$newKey] = $found;
                } elseif (is_string($value) || is_numeric($value)) {
                    $found = ($newValue === $value);
                    $newResult[$newKey] = $found;
                } elseif ($withSubquery === true) {
                    $found = array_where($result, function ($current, $currentKey) use ($value, $keys) {
                        $only = array_first($this->__onlyValuesWith($value, $keys));
                        $test = $this->getAllAssociatedKeyValues($current, $only[$currentKey]);

                        return $test !== -1;
                    });
                    $newResult[$newKey] = !empty($found);
                } else {
                    $newResult[$newKey] = in_array($newValue, $value, true);
                }
            } else {
                $currentValue = is_array($value) ? $value[$newKey] : $value;
                $result = $this->findFirstMatch(is_array($newValue) ? $newValue : [$newValue], $currentValue);
                $newResult[$newKey] = !is_null($result);
            }

            $index++;
        }

        $countIgnore = !is_array($value) ? 0 : count($value) - 1;

        $accessibilityKeys = [];
        $index = count($keys) - 1;
        $countPa = 0;
        while ($index >= 0 && ($sw === false)) {
            $currentValue = current($newResult);

            if ($currentValue === true && $sw === false) {
                $accessibilityKeys[] = key($newResult);
                if ($countIgnore === $countPa) {
                    $sw = true;
                }

                $countPa++;
            }

            $index--;
            next($newResult);
        }

        if ($sw === false) {
            $this->unsetArray(true, $accessibilityKeys);
        }

        return [
            'sw' => $sw,
            'keys' => $accessibilityKeys,
        ];
    }

    /**
     * Encuentra la primera ocurrencia de los valores dado de un arreglo.
     * @param array $array
     * @param string|int|array $value
     * @param boolean $with_expression
     * @return mixed
     */
    public function arrayFirstWith(array $array, $value, $with_expression = false)
    {
        if (is_array($value)) {
            return array_first($array, function ($current, $key) use ($value, $with_expression) {
                /** @var array */
                $result = $this->_arrayFirstColumn($current, $key, $value, $with_expression);

                return $result['sw'];
            });
        }

        return $this->arrayFirst($array, $value);
    }

    /**
     * Agrega los arreglos en una matriz usando una llave dada.
     * @param array $arr
     * @param mixed $key
     * @return array
     */
    public static function arrayGroupBy(array $arr, $key): array
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
            trigger_error('arrayGroupBy(): The key should be a string, an integer, a float, or a function', E_USER_ERROR);
        }

        $isFunction = !is_string($key) && is_callable($key);
        $grouped = [];
        foreach ($arr as $value) {
            $groupKey = null;
            if ($isFunction) {
                $groupKey = $key($value);
            } elseif (is_object($value)) {
                $groupKey = $value->{$key};
            } else {
                $groupKey = $value[$key];
            }
            $grouped[$groupKey][] = $value;
        }

        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $groupKey => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$groupKey] = call_user_func_array('HelperArray::arrayGroupBy', $params);
            }
        }

        return $grouped;
    }

    /**
     * Obtiene los valores de un arreglo indicándolos
     * @param array $array
     * @param array|string $search
     * @param boolean $includeKeys
     * @return array
     */
    public function arrayValueOnly($array, $search, $includeKeys = false)
    {
        $values = array_where(
            $array,
            function ($value, $index) use ($search) {
                $sw = false;

                if (is_array($search)) {
                    $i = 0;
                    while ($i < count($search) && $sw === false) {
                        if ($search[$i] === $value) {
                            $sw = true;
                        }
                        $i++;
                    }
                } elseif ($search === $value) {
                    $sw = true;
                }

                return $sw;
            }
        );

        if ($includeKeys === true) {
            return $values;
        }

        return $this->getValues($values);
    }

    /**
     * @param array $request
     * @param array|string|int $key
     * @return array
     */
    public function validArray($request, $key = [])
    {
        return array_filter(
            $request,
            function ($rValue) use ($key) {
                if (!empty($key) && is_array($rValue)) {
                    $only = array_only($rValue, $key);
                    $result = array_where(
                        $only,
                        function ($value, $index) {
                            return !empty($value) && !is_null($value);
                        }
                    );

                    return $result && count($result) > 0;
                }

                return !empty($rValue) && !is_null($rValue);
            }
        );
    }

    /**
     * Sort an array by submatrix key keeping the keys at the top level.
     * @param array $array
     * @param string $subkey
     * @param boolean $sort_descending
     * @param boolean $keep_keys_in_sub
     * @param int $sort_flags
     * @return void
     */
    public function sksort(&$array, $subkey = "id", $sort_descending = false, $keep_keys_in_sub = false, $sort_flags = SORT_NATURAL)
    {
        if ($sort_descending === false && $keep_keys_in_sub === true) {
            throw new \Exception("The `` Keep_Keys_sub``````` `is not required if the parameter` `sort_descending``` it is false.", 1);
        }
        $temp_array = $array;
        if ($this->itIsAMatrix(array_first($array, null, [])) === false) {
            $temp_array = [$array];
        }

        foreach ($temp_array as &$value) {
            $sort = array();
            foreach ($value as $index => $val) {
                if (isset($val[$subkey]) && $sort_flags !== SORT_NUMERIC) {
                    $sort[$index] = $val[$subkey];
                } elseif (isset($val[$subkey]) && $sort_flags === SORT_NUMERIC) {
                    $numeric = is_numeric($val[$subkey]) ? $val[$subkey] : $this->extractNumbersInString($val[$subkey]);
                    $sort[$index] = !empty($numeric) ? $numeric : $val[$subkey];
                }
            }

            asort($sort, $sort_flags);

            $keys = array_keys($sort);
            $newValue = array();
            foreach ($keys as $index) {
                if ($keep_keys_in_sub) {
                    $newValue[$index] = $value[$index];
                } else {
                    $newValue[] = $value[$index];
                }
            }

            if ($sort_descending) {
                $value = array_reverse($newValue, $keep_keys_in_sub);
            } else {
                $value = $newValue;
            }
        }

        if ($this->itIsAMatrix(array_first($array, null, [])) === false) {
            $array = array_first($temp_array);
        } else {
            $array = $temp_array;
        }
    }

    /**
     * @param array $array
     * @return array
     */
    public function getUniqueValues(array $array)
    {
        $temp = [];
        $current = current($array);
        if (is_array($current) && count($current) === 0) {
            return $temp;
        }
        if (is_null($current)) {
            return $temp;
        }

        $index = 0;
        while ($index < count($array)) {
            $result = array_search($array[$index], $temp);
            if ($result === false) {
                $temp[] = $array[$index];
            }
            $index++;
        }

        return $temp;
    }

    /**
     * @param array $datos
     * @param array $headers
     * @param bool $collapse
     * @return array
     */
    public function arrayOnlyData($datos, $headers = [], $collapse = false)
    {
        $temp = [];
        $countHeaders = count($headers);
        if (is_null($datos)) {
            return [];
        }

        foreach ($datos as $data) {
            $data = is_array($data) ? $data : (array) $data;
            $countData = count($data);
            if ($countData === $countHeaders) {
                $temp[] = array_combine($headers, $data);
            } else {
                $residuo = $countHeaders - $countData;
                $last = $countHeaders - $residuo;
                $result = array_slice($headers, 0, $last);
                $temp[] = array_combine($result, $data);
            }
        }

        if ($collapse && count($temp) > 0) {
            return array_collapse($temp);
        }

        return $temp;
    }

    /**
     * @param array $datos
     * @param mixed $header
     * @return array
     */
    public function mapArrayData(array $datos, $header = null)
    {
        return array_map(
            function ($val) use ($header) {
                if (is_null($header)) {
                    $header = ['id', 'display_name'];
                }

                return array_combine($header, $val);
            },
            $datos
        );
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     * @param array $array
     * @param int $depth
     * @return array
     */
    public function arrayReduce($array, $depth = INF)
    {
        return array_reduce($array, function ($result, $item) use ($depth) {
            $item = $item instanceof Collection ? $item->all() : $item;
            if (!is_array($item)) {
                return array_merge($result, [$item]);
            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));
            } elseif ($depth === 2) {
                if (is_array(current($item))) {
                    return array_merge($result, $this->arrayOnly($item, array_keys($item)));
                } else {
                    return array_merge($result, $this->arrayOnly([$item], array_keys($item)));
                }
            } else {
                return array_merge($result, $this->arrayReduce($item, $depth - 1));
            }
        }, []);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     * @param array $array
     * @return array
     */
    public function arrayFlatten($array, $depth = INF)
    {
        $result = array();
        if (!is_array($array)) {
            return $result;
        }

        foreach ($array as $item) {
            $item = $item instanceof Collection ? $item->all() : $item;
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1 ? array_values($item) : $this->arrayFlatten($item, $depth - 1);
                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $values
     * @param array $keys
     * @return array
     */
    public function arrayFlattenWithOnly($values, $keys = [])
    {
        if (is_null($values)) {
            return [];
        }

        $temps = array();
        $tempOnly = $this->arrayOnly(
            $values,
            $keys
        );
        foreach ($tempOnly as $value) {
            $temps[] = array_flatten($value);
        }

        return array_collapse($temps);
    }

    /**
     * @param array $values
     * @param mixed $value
     * @param boolean $strict
     * @return boolean
     */
    public function arrayInWith($values, $value, $strict = true)
    {
        foreach ($values as $currentKey => $currentValue) {
            $bool = $strict ? $value === $currentKey : $value == $currentKey;
            if ($bool) {
                return true;
            }

            if (is_array($value)) {
                $bool = $this->arrayInWith($value, $currentKey, $strict);
                if ($bool) {
                    return true;
                }
            }

            if (is_array($currentValue)) {
                $bool = $this->arrayInWith($currentValue, $value, $strict);
            } else {
                $bool = $strict ? $value === $currentValue : $value == $currentValue;
            }

            if ($bool) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $values
     * @param array $value
     * @param array $keys
     * @return array
     */
    public function arrayValuesExceptWithKey(array $values, array $value, $keys = [])
    {
        if (is_null($values) || count($values) === 0) {
            return [];
        }
        if (count($keys) > 0) {
            $values = $this->arrayOnly(
                $values,
                $keys
            );
            if (is_array($value)) {
                $value = $this->arrayOnly(
                    $value,
                    $keys
                );
            }
        }

        $tempValues = [];
        if (is_array($value) && count($keys) > 0) {
            foreach ($values as $originValue) {
                // verificamos si ya el proximo elemento a insertar no exista.
                if ($this->arrayFirstWith($tempValues, $originValue)) {
                    continue;
                }

                if (is_array($originValue) && count($originValue) > 0) {
                    $tempArray = [];
                    $resultValue = null;
                    $tempArray = array_first($value, function ($currentValue, $key) use (&$resultValue, $values) {
                        $result = empty($this->arrayFirstWith($values, $currentValue));
                        if ($result) {
                            $resultValue = $currentValue;
                        }

                        return $result === true;
                    });

                    if (!empty($resultValue) && empty($this->arrayFirstWith($tempValues, $resultValue)) === true) {
                        array_push($tempValues, $resultValue);
                    }
                }
            }
        }

        return $tempValues;
    }

    /**
     * Accepted patterns:
     * - Regular (provincia-libro-tomo). Ej: 1-1234-12345
     * - Panameño nacido en el extranjero (PE-libro-tomo). Ej: PE-1234-12345
     * - Extranjero con cédula (E-libro-tomo). Ej: E-1234-123456
     * - Naturalizado (N-libro-tomo). Ej: N-1234-12345
     * - Panameños nacidos antes de la vigencia (provinciaAV-libro-tomo). Ej: 1AV-1234-12345
     * - Población indigena (provinciaPI-libro-tomo). Ej: 1PI-1234-12345
     *
     * @param string $id
     * @param int $tomo
     * @param int $folio
     * @param string $delimiter
     * @return array
     */
    public function panamaID($id, $tomo = 4, $folio = 6, $delimiter = '')
    {
        $onlyFirst = "^(?:PE|E|N|[23456789]|[23456789](?:A|P)?|1[0123]?|1[0123]?(?:A|P)?)$|^(?:PE|E|N|[23456789]|[23456789](?:AV|PI)?|1[0123]?|1[0123]?(?:AV|PI)?)$delimiter?$";
        $bookAndTome = "^(?:PE|E|N|[23456789](?:AV|PI)?|1[0123]?(?:AV|PI)?)$delimiter(?:\d{1,$tomo})$delimiter?$";
        $fullId = "^(PE|E|N|[23456789](?:AV|PI)?|1[0123]?(?:AV|PI)?)$delimiter(\d{1,$tomo})$delimiter(\d{1,$folio})";

        preg_match("/^P$|{$onlyFirst}|{$bookAndTome}|{$fullId}/i", $id, $matches);

        return $matches;
    }

    /**
     * @param string $match
     * @return array
     */
    public function getFileFormatsGroup($match = "/^image/i")
    {
        $types = $this->getAppConfigMimeKeys();
        if (!is_array($types)) {
            return null;
        }
        if (empty($match)) {
            return $this->getValues($types);
        }

        $newTypes = array_filter($types, function ($type) use ($match) {
            try {
                return preg_match($match, $type);
            } catch (\Exception $e) {
                return false;
            }
        });

        return $this->getValues($newTypes);
    }

    /**
     * @param string $match
     * @return array
     */
    public function getFileExtensionGroup($match = "/^csv/i")
    {
        $types = $this->getAppConfigFileTypes();
        if (!is_array($types)) {
            return null;
        }
        if (empty($match)) {
            return $this->getValues($types);
        }

        $newTypes = array_filter($types, function ($type) use ($match) {
            return preg_match($match, $type);
        });

        return $this->getValues($newTypes);
    }

    /**
     * @param array $values
     * @param string $match
     * @return mixed
     */
    public function findFirstMatch($values, $match = "/Controller/i")
    {
        $value = null;
        $sw = false;
        $index = 0;
        while ($index < count($values) && $sw === false) {
            if (isset($values[$index]) && preg_match($match, $values[$index])) {
                $value = $values[$index];
                $sw = true;
            }
            $index++;
        }


        return $value;
    }

    /**
     * @param array $values
     * @param array|string $value
     * @param boolean $with_expression
     * @return mixed
     */
    public function arrayFirstIndexWith($values, $value, $with_expression = false)
    {
        while ($currentValue = current($values)) {
            /** @var array */
            $result = $this->_arrayFirstColumn($currentValue, key($values), $value, $with_expression);
            if ($result['sw'] === true) {
                return key($values);
            }

            next($values);
        }

        return -1;
    }

    /**
     * @param array $values
     * @param array|string $value
     * @param boolean $with_expression
     * @return mixed
     */
    public function getAllAssociatedValues($values, $value, $with_expression = false)
    {
        return array_where($values, function ($currentValue, $index) use ($value, $with_expression) {
            /** @var array */
            $result = $this->_arrayFirstColumn($currentValue, $index, $value, $with_expression);

            return $result['sw'];
        });
    }

    /**
     * @param array $values
     * @param array|string $value
     * @param boolean $with_expression
     * @return mixed
     */
    public function getAllAssociatedKeyValues($values, $value, $with_expression = false)
    {
        /** @var boolean */
        $isAccessible = $this->itIsAMatrix($values);
        if ($isAccessible === false) {
            /** @var array */
            $result = $this->_arrayFirstColumn($values, 0, $value, $with_expression);

            return $result['sw'] === true ? $result['keys'] : -1;
        }

        $keys = [];
        while ($currentValue = current($values)) {
            $key = key($values);
            /** @var array */
            $result = $this->_arrayFirstColumn($currentValue, $key, $value, $with_expression);
            if ($result['sw'] === true) {
                $keys[] = $key;
            }

            next($values);
        }

        return count($keys) > 0 ? $keys : -1;
    }

    /**
     * @param array $values
     * @param array|string $except
     * @param bool $with_expression = false
     * @return array
     */
    public function arrayExcept(array $values, $except, bool $with_expression = false)
    {
        $tmp = [];
        foreach ($values as $value) {
            /** @var array $value */
            $value = (array)$value;
            /** @var mixed */
            $found = $this->arrayFirstWith($value, $except, $with_expression);
            if (!is_null($found)) {
                continue;
            }

            array_push($tmp, $value);
        }

        return !$this->itIsAMatrix($values) ? array_flatten($tmp) : $tmp;
    }

    /**
     * @param string $match
     * @return string
     */
    public function getFirstFileExtensionByMimeType(string $match)
    {
        $config = $this->getAppConfig('helper');
        if (!is_array($config)) {
            return null;
        }

        $match = preg_quote($match, '/');

        $types = $config['file_types'];

        $newTypes = array_first($types, function ($ext, $type) use ($match) {
            if (preg_match("/$match/i", $type)) {
                return true;
            }

            return preg_match("/$match/i", $ext);
        });

        return $newTypes;
    }

    /**
     * @param string $match
     * @return array
     */
    public function getFileExtensionByMimeType(string $match)
    {
        $config = $this->getAppConfig('helper');
        if (!is_array($config)) {
            return null;
        }

        $match = preg_quote($match, '/');

        $types = $config['file_types'];

        $newTypes = array_where($types, function ($ext, $type) use ($match) {
            if (preg_match("/$match/i", $type)) {
                return true;
            }

            return preg_match("/$match/i", $ext);
        });

        return $this->getValues($newTypes);
    }
}
