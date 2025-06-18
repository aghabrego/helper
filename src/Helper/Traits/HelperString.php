<?php

namespace Weirdo\Helper\Traits;

use NumberFormatter;
use Illuminate\Support\Str;
use Weirdo\Helper\Traits\Helper;
use Luecano\NumeroALetras\NumeroALetras;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait HelperString
{
    use Helper;

    /**
     * @param string $string
     * @param string|null $delimiterToIgnore
     * @param string $specialCharacters
     * @return string
     */
    public function cleanSpecialCharacters($string, $delimiterToIgnore = null, $specialCharacters = '')
    {
        $string = trim($string);
        // Normaliza caracteres mal codificados a UTF-8
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
        }
        // Convierte caracteres Unicode/acento a ASCII
        if (class_exists('Transliterator')) {
            $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
        } else {
            $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        }
        $string = $this->strReplaceDeep(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä', 'Ã'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A'),
            $string,
            $delimiterToIgnore
        );
        $string = $this->strReplaceDeep(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ẽ'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'E'),
            $string,
            $delimiterToIgnore
        );
        $string = $this->strReplaceDeep(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ĩ'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I'),
            $string,
            $delimiterToIgnore
        );
        $string = $this->strReplaceDeep(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Õ'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O'),
            $string,
            $delimiterToIgnore
        );
        $string = $this->strReplaceDeep(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ũ'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'U'),
            $string,
            $delimiterToIgnore
        );
        $string = $this->strReplaceDeep(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $string,
            $delimiterToIgnore
        );

        return trim(
            $this->strReplaceDeep(
                array(
                    '\\', '¨', 'º', '-', '~',
                    '#', '@', '|', '!', '"',
                    '·', '$', '%', '&', '/',
                    '(', ')', '?', "'", '¡',
                    '¿', '[', '^', '<code>', ']',
                    '+', '}', '{', '¨', '´',
                    '>', '< ', ';', ',', ':', '*', ' ',
                    '.',
                ),
                $specialCharacters,
                $string,
                $delimiterToIgnore
            )
        );
    }

    /**
     * @param string $string
     * @param string|null $delimiterToIgnore
     * @return string
     */
    public function pregReplaceString($string, $delimiterToIgnore = null)
    {
        return preg_replace("/[\r\n\s+|\n\s+|\r\s+]+/", $delimiterToIgnore, trim($string));
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param integer $limit
     * @param boolean $order
     * @return string
     */
    public function getFirstSubstr(string $string, string $delimiter = "-", int $limit = 1, bool $order = false)
    {
        $string = $this->cleanSpecialCharacters($string, $delimiter, " ");
        $index = $this->getIndexFirstOccurrence($string, $delimiter, $limit);
        if (!$order) {
            return $this->getSubstr($string, $index === -1 ? strlen($string) : $index, strlen($string));
        }

        return $this->getSubstr($string, 0, $index === -1 ? strlen($string) : $index - 1);
    }

    /**
     * @param string $string
     * @param integer $start
     * @param integer $length
     * @return string|false
     */
    public function getSubstr(string $string, int $start, int $length = null)
    {
        $subString = substr($string, $start, $length);
        if (empty($subString)) {
            return $string;
        }

        return $subString;
    }

    /**
     * Get the letter where the counter is equal to the last index.
     * @param array $cells
     * @param int $last
     * @return string
     */
    public static function getTheLargestCell($cells, $last)
    {
        $count = 0;
        $column = '';
        foreach ($cells as $key => $cell) {
            if ($count === $last) {
                $column = $key;
            }

            ++$count;
        }

        return $column;
    }

    /**
     * @param string $filters
     * @return string
     */
    public function getUniqueValuesForQuery($filters)
    {
        $aFilters = explode(',', $filters);
        $current = [];
        if (isset($aFilters) && count($aFilters) > 0) {
            foreach ($aFilters as $filter) {
                $result = array_first(
                    $current,
                    function ($value) use ($filter) {
                        return $value === $filter;
                    }
                );

                if (is_null($result)) {
                    $current[] = $filter;
                }
            }

            return implode(',', $current);
        }

        return '*';
    }

    /**
     * @param string|string[] $search
     * @param string|string[] $replace
     * @param string|string[] $subject
     * @param string|null $delimiterToIgnore
     * @return string|string[]|null
     */
    public function strReplaceDeep($search, $replace, $subject, $delimiterToIgnore = null)
    {
        if (is_array($subject)) {
            foreach ($subject as &$oneSubject) {
                $oneSubject = $this->strReplaceDeep($search, $replace, $oneSubject, $delimiterToIgnore);
            }
            unset($oneSubject);

            return $subject;
        } else {
            $search = $this->arrayValueExcept($search, $delimiterToIgnore);

            return str_replace($search, $replace, $subject);
        }
    }

    /**
     * @param float $money
     * @param integer $name
     * @param integer $decimal
     * @return string
     */
    public function getMoneyFormat($money = 0, $format = NumberFormatter::CURRENCY, $local = 'es_US', int $decimal = 2)
    {
        if ($this->filterVar($money, FILTER_VALIDATE_INT) === false) {
            $money = !empty($money) ? (string) $money : '0';
        }
        if (!is_numeric($money)) {
            $money = '0';
        }

        $numberFormatter = new NumberFormatter($local, $format);
        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimal);

        return $numberFormatter->format($money);
    }

    /**
     * @param float $number
     * @param integer $format
     * @return string
     */
    public function convertAmountinDigittoWords($number, $format = NumberFormatter::SPELLOUT, $local = 'es_US')
    {
        if ($this->filterVar($number, FILTER_VALIDATE_INT) === false) {
            $number = !empty($number) ? (string) $number : '0';
        }
        if (!is_numeric($number)) {
            $number = '0';
        }

        $classNumber = new NumberFormatter($local, $format);

        return $classNumber->format($number);
    }

    /**
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public function extractString(string $value, string $delimiter = ' ')
    {
        return $this->pregReplaceString(preg_replace('/[^aA-zZ]+/', $delimiter, $value), $delimiter);
    }

    /**
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public function extractNumbersInString(string $value, string $delimiter = ' ')
    {
        return trim(preg_replace('/[^0-9]+/', $delimiter, $value));
    }

    /**
     * @param string|string[] $haystack
     * @param string $needle
     * @param int $offset
     * @return mixed
     */
    public function findStringOccurrence($haystack, $needle, $offset = 0)
    {
        if (is_array($haystack)) {
            foreach ($haystack as $key => $value) {
                $offset = $this->findStringOccurrence($value, $needle, $offset);
                if ($offset !== false) {
                    return [$key => $offset];
                }
            }

            return false;
        }

        return strpos($haystack, $needle, $offset);
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function strRandom($id, $length = 30)
    {
        return str_random($length) . uniqid($id);
    }

    /**
     * @param array $select
     * @param string $table
     * @return string
     */
    public function createSelect(array $select, string $table)
    {
        return implode(
            ',',
            array_map(
                function ($value) use ($table) {
                    return "{$table}.{$value}";
                },
                $select
            )
        );
    }

    /**
     * @param mixed $item
     * @param string
     */
    public function getFullName($item)
    {
        $full_name = "{$item->primer_nombre} {$item->segundo_nombre} {$item->primer_apellido} {$item->segundo_apellido}";

        return preg_replace("/[\r\n\s+|\n\s+|\r\s+]+/", ' ', trim($full_name));
    }

    /**
     * @param string $value
     * @param int $position
     * @return string
     */
    public function getWordFromTextWith($value, $position = -1)
    {
        $arr = str_split($value);

        return is_array($arr) && isset($arr[$position]) ? $arr[$position] : null;
    }

    /**
     * @param array $items
     * @param string $delimiter
     * @return string
     */
    public function implodeArray($items, $delimiter = '-')
    {
        if (!is_array($items)) {
            return null;
        }

        $text = "";
        foreach ($items as $index => $item) {
            if (is_array($item)) {
                $text .= $this->implodeArray($item, $delimiter);
            } else {
                $text .= $item;
            }

            if ($index < (count($items) - 1) && !empty($item)) {
                $text .= $delimiter;
            }
        }

        return $text;
    }

    /**
     * @param string $id
     * @param string $oldFormat
     * @param string $newFormat
     * @return string|null
     */
    public function convertCertificateToText($id, $oldFormat = '', $newFormat = '-')
    {
        $text = '';
        $id = $this->panamaIDFormat($id, $oldFormat, $newFormat);
        if (is_null($id)) {
            return null;
        }

        $parts = explode('-', $id);
        foreach ($parts as $key => $part) {
            $text .= $this->convertAmountinDigittoWords($part);
            if ($key < (count($parts) - 1)) {
                $text .= $newFormat;
            }
        }

        return $text;
    }

    /**
     * @param float $money
     * @param int $make 0 OR 1
     * @return string
     */
    public function formatToDollars($money, $make = 0)
    {
        if (!in_array($make, [0, 1])) {
            return null;
        }

        $textMony = null;
        $newFormatMoney = (new NumeroALetras())->toMoney($money, 2, 'DÓLARES', 'CENTAVOS');
        if (!preg_match('/centavos\\b/i', mb_strtolower($newFormatMoney))) {
            $textMony = "{$newFormatMoney} CON CERO CENTAVOS";
        }
        $textMony = ($make === 0) ? mb_strtolower(($textMony ?? $newFormatMoney)) : mb_strtoupper(($textMony ?? $newFormatMoney));

        return $textMony;
    }

    /**
     * @param string $ext
     * @return string
     */
    public function getAppConfigMimeType($ext = 'pdf')
    {
        $arrMimeTypes = $this->getAppConfig();
        if (!is_array($arrMimeTypes)) {
            return null;
        }

        return $this->arrayFirstIndex($arrMimeTypes['file_types'], $ext);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getClientOriginalExtension($path)
    {
        $test = explode('.', $path);

        return array_pop($test);
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param int $delimiterCount
     * @param bool $before
     * @return string
     */
    public function getFirstSubstringDelimiter(string $string, string $delimiter = '/', int $delimiterCount = 1, bool $before = true)
    {
        $index = $this->getIndexFirstOccurrence($string, $delimiter, $delimiterCount);
        if ($before === false) {
            $subString = $this->getSubstr($string, $index, strlen($string));
        } else {
            $subString = $this->getSubstr($string, 0, $index - 1);
        }
        $verify = substr_count($subString, $delimiter);
        if ($verify > 0) {
            return $this->getFirstSubstringDelimiter($subString, $delimiter, $verify, !$before);
        }

        return $subString;
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    public function after(string $subject, string $search)
    {
        return Str::after($subject, $search);
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    public function afterLast(string $subject, string $search)
    {
        return Str::afterLast($subject, $search);
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public function before(string $subject, string $search)
    {
        return Str::before($subject, $search);
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public function beforeLast(string $subject, string $search)
    {
        return Str::beforeLast($subject, $search);
    }

    /**
     * @param string $string
     * @return string
     */
    public function encryptText(string $string)
    {
        $config = $this->getAppConfig('helper');

        $encryptMethod = "AES-256-CBC";
        $secretKey = isset($config['private_key_encrypt']) ? $config['private_key_encrypt'] : 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
        $secretIv = isset($config['secret_key_encrypt']) ? $config['secret_key_encrypt'] : '5fgf5HJ5g27'; // user define secret key
        $passphrase = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', $secretIv), 0, 16); // sha256 is hash_hmac_algo
        $output = openssl_encrypt($string, $encryptMethod, $passphrase, OPENSSL_RAW_DATA, $iv);
        $output = base64_encode($output);

        return $output;
    }

    /**
     * @param string $string
     * @return string
     */
    public function decryptText(string $string)
    {
        $config = $this->getAppConfig('helper');

        $encryptMethod = "AES-256-CBC";
        $secretKey = isset($config['private_key_encrypt']) ? $config['private_key_encrypt'] : 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
        $secretIv = isset($config['secret_key_encrypt']) ? $config['secret_key_encrypt'] : '5fgf5HJ5g27'; // user define secret key
        $passphrase = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', $secretIv), 0, 16); // sha256 is hash_hmac_algo
        $output = openssl_decrypt(base64_decode($string), $encryptMethod, $passphrase, OPENSSL_RAW_DATA, $iv);

        return $output;
    }

    /**
     * @param string $string
     * @param string $action
     * @return string
     */
    public function encryptDecrypt($string, $action = 'encrypt')
    {
        if ($action == 'encrypt') {
            $output = $this->encryptText($string);
        } elseif ($action == 'decrypt') {
            $output = $this->decryptText($string);
        }

        return $output;
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     * @return string
     */
    public function upper(string $value)
    {
        return Str::upper($value);
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param  string  $string
     * @return string
     */
    public function ucfirst(string $value)
    {
        return Str::ucfirst($value);
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     * @return string
     */
    public function lower(string $value)
    {
        return Str::lower($value);
    }

    /**
     * Make a string's first character lower-case.
     *
     * @param  string  $string
     * @return string
     */
    public function lofirst(string $value)
    {
        return $this->lower(Str::substr($value, 0, 1)) . Str::substr($value, 1);
    }

    /**
     * @param string $id
     * @param string $delimiter
     * @return string|null
     */
    public function panamaIDFormat($id, $delimiter = '', $newFormat = '-')
    {
        $tomo = (strlen($id) >= 4 && strlen($id) <= 9) ? 3 : 4;
        $matches = $this->panamaID($id, $tomo, 6, $delimiter);
        if (is_array($matches) && isset($matches[0])) {
            array_splice($matches, 0, 1);
            if (count($matches) === 0) {
                return null;
            }
            if (preg_match("/^PE|E|N$/", $matches[0])) {
                array_insert($matches, 0, "0");
            }
            if (preg_match("/^(1[0123]?|[23456789])?$/", $matches[0])) {
                array_insert($matches, 1, "");
            }
            if (preg_match("/^(1[0123]?|[23456789])(AV|PI)$/", $matches[0])) {
                preg_match("/(\d+)(\w+)/", $matches[0], $tmp);

                array_splice($matches, 0, 1);
                array_insert($matches, 0, $tmp[1]);
                array_insert($matches, 1, $tmp[2]);
            }
        }

        return is_array($matches) && count($matches) >= 3 ? $this->implodeArray($matches, $newFormat) : null;
    }

    /**
     * @param string $match
     * @return string
     */
    public function firstFileFormatsGroup($match = "/^jpg/i")
    {
        $types = $this->getAppConfigMimeKeys();
        if (!is_array($types)) {
            return null;
        }

        return array_first($types, function ($type) use ($match) {
            return preg_match($match, $type);
        });
    }

    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    public function camel(string $value)
    {
        return Str::camel($value);
    }

    /**
     * Pad a string to a certain length with another string
     * 
     * @param string $value
     * @param int $length
     */
    public function strPad(string $value, $length = 3)
    {
        $paddedNumber = str_pad($value, $length, '0', STR_PAD_LEFT);

        return $paddedNumber;
    }
}
