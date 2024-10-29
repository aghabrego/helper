<?php

namespace Weirdo\Helper\Traits;

use finfo;
use DOMXPath;
use Exception;
use DOMDocument;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use Weirdo\Helper\Support\AudioInfo;
use libphonenumber\PhoneNumberFormat;
use Illuminate\Support\Facades\Config;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait Helper
{
    /**
     * @param string $name
     * @param mixed $params
     * @return mixed
     */
    public static function reflectionMethod($name)
    {
        return (new ReflectionMethod(static::class, $name));
    }

    /**
     * Checks if method is exist.
     * @param string $name
     * @param string $class
     * @return bool
     */
    public static function hasMethodExist($name, $class = null)
    {
        if (is_null($class)) {
            $class = static::class;
        }

        return method_exists($class, $name);
    }

    /**
     * @param ReflectionMethod $reflection
     * @param array $params
     * @return mixed
     */
    public static function runInvokeArgs($reflection, array $params)
    {
        if (static::hasMethodExist('invokeArgs', ReflectionMethod::class)) {
            return $reflection->invokeArgs((new static), $params);
        }

        return null;
    }

    /**
     * @param string $config
     * @return mixed
     */
    public function getAppConfig($config = 'helper')
    {
        if (file_exists(base_path() .'/src/config/config.php')) {
            $config = require base_path() .'/src/config/config.php';

            return $config;
        }

        try {
            return Config::get($config);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getAppConfigMimeKeys()
    {
        $config = $this->getAppConfig('helper');

        return array_keys($config['file_types']);
    }

    /**
     * @return string
     */
    public function getAppConfigMimeTypes()
    {
        $config = $this->getAppConfig('helper');

        return implode(',', array_keys($config['file_types']));
    }

    /**
     * @return string
     */
    public function getAppConfigMime()
    {
        $config = $this->getAppConfig('helper');

        return implode(',', $config['file_types']);
    }

    /**
     * @return array
     */
    public function getAppConfigFileTypes()
    {
        $config = $this->getAppConfig('helper');

        return $this->getValues($config['file_types']);
    }

    /**
     * @param mixed $value
     * @param int $format
     * @param array|int $options
     * @return mixed
     */
    public function filterVar($value, $format = FILTER_VALIDATE_BOOLEAN, $options = [])
    {
        return filter_var($value, $format, $options);
    }

    /**
     * @param string $string
     * @param bool $associative
     * @return mixed
     */
    public function getJsonDecode($string, $associative = false)
    {
        // Eliminar del inicio y final la comilla simple.
        $string = trim($string, "'");
        $resultString = json_decode($string, $associative);
        if (is_string($resultString)) {
            return $this->getJsonDecode($resultString, $associative);
        }

        return $resultString;
    }

    /**
     * @param string|array $string
     * @param string $delimiter
     * @param int $limit
     * @return int
     */
    public function getIndexFirstOccurrence($string, string $delimiter = "-", int $limit = 1)
    {
        $count = 1;
        $index = 0;
        $array = $string;
        if (is_string($array)) {
            $array = str_split($string);
        }
        while ($index < count($array) && $count <= $limit) {
            if ($array[$index] === $delimiter) {
                $count++;
            }
            $index++;
        }

        return $count === 1 ? -1 : $index;
    }

    /**
     * @param string|string[] $array
     * @param string $delimiter
     * @return array
     */
    public function arrayValueExcept($array, $delimiter = null)
    {
        if (is_string($array)) {
            return $this->arrayValueExcept(str_split($array), $delimiter);
        }

        $temp = [];
        $index = 0;
        while ($index < count($array)) {
            if ($array[$index] !== $delimiter) {
                $temp[] = $array[$index];
            }
            $index++;
        }

        return $temp;
    }

    /**
     * @param array $array
     * @return boolean
     */
    public function itIsAMatrix(array $array)
    {
        $current = current($array);

        return is_array($current);
    }

    /**
     * @param string $url
     * @param array $params
     * @param bool $prettyRoute
     * @return string
     */
    public function createTheParametersOfTheUrl($url, $params, $prettyRoute = false)
    {
        if (count($params) === 0) {
            return $url;
        }

        $temUrl = '';
        if ($prettyRoute === false) {
            $occurr = $this->getIndexFirstOccurrence($url, '?');
            $temUrl = $occurr === -1 ? "?" : "&";
        }
        $index = 0;
        foreach ($params as $key => $param) {
            $temUrl .= is_string($key) ? "{$key}={$param}" : "{$param}";
            if ($prettyRoute === false && $index < count($params) - 1) {
                $temUrl .= "&";
            } elseif ($prettyRoute === true && $index < count($params) - 1) {
                $temUrl .= "/";
            }
            $index++;
        }

        return "{$url}{$temUrl}";
    }

    /**
     * @param string $route
     * @param string $params
     * @param string $type
     * @param string $protocol
     * @param array $options
     * @return mixed
     */
    public function streamContextCreate(string $route, string $params = null, string $type = 'POST', string $protocol = 'https', array $options = [])
    {
        try {
            $http = [];
            if (in_array($type, ['get', 'GET'])) {
                $params = (array)json_decode((string)$params);
                $route = $this->createTheParametersOfTheUrl($route, $params);
                $http = array_merge(['method'  => 'GET'], $options);
            } elseif (in_array($type, ['post', 'POST'])) {
                $http = [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/json',
                    'content' => $params
                ];
                $http = array_merge($http, $options);
            }

            $context = stream_context_create([$protocol => $http]);

            return file_get_contents($route, false, $context);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $phone
     * @param string|null $code
     * @return object
     */
    public function getPhoneNumberObject(string $phone, string $code = null)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        return $phoneNumberUtil->parse($phone, $code);
    }

    /**
     * @param string $phone
     * @return string
     */
    public function getRegionCodeForNumber(string $phone)
    {
        /** @var \libphonenumber\PhoneNumber $phoneNumberUtil */
        $phoneNumber  = $this->getPhoneNumberObject($phone);
        /** @var \libphonenumber\PhoneNumberUtil $util */
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        /** @var string|null $natinalCode */
        $regionCode = $phoneNumberUtil->getRegionCodeForNumber($phoneNumber);

        return $regionCode;
    }

    /**
     * @param string $phone
     * @param string|null $code
     * @param int $format
     * @return string|null
     */
    public function getPhoneNumberFormat(string $phone, string $code = null, $format = PhoneNumberFormat::E164)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($phone, $code);

        if ($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, $code) === false) {
            return null;
        }

        return $phoneNumberUtil->format($phoneNumberObject, $format);
    }

    /**
     * @param string $phone
     * @param string|null $code
     * @param int $format
     * @return string|null
     */
    public function checkPhonePanama(string $phone, $code = 'PA', $format = PhoneNumberFormat::E164)
    {
        return $this->getPhoneNumberFormat($phone, $code, $format);
    }

    /**
     * @param string|null $phone
     * @param string|null $code
     * @param int $format
     * @return string|null
     */
    public function getValidCellPhoneFormat(string $phone = null, string $code = null, $format = PhoneNumberFormat::NATIONAL)
    {
        if (empty($phone)) {
            return null;
        }

        try {
            $strNumberNacional = $this->getPhoneNumberFormat($phone, $code, $format);

            return !empty($strNumberNacional) ? $this->extractNumbersInString($strNumberNacional, '') : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param string|null $phone
     * @param int $format
     * @return string|null
     */
    public function getValidCellPhoneFormatPanama(string $phone = null, $format = PhoneNumberFormat::NATIONAL)
    {
        return $this->getValidCellPhoneFormat($phone, 'PA', $format);
    }

    /**
     * @param string|null $cell
     * @return string|null
     */
    public function getProperPhoneFormat(string $phone = null)
    {
        /** @var array $config */
        $config = $this->getAppConfig('helper');
        /** @var array $ignore */
        $ignore = array_get($config, 'foreign_numbers', []);
        /** @var string|null $stPhone */
        $stPhone = $this->getValidCellPhoneFormat($phone, 'PA', PhoneNumberFormat::NATIONAL);
        if (empty($stPhone)) {
            while (($numbers = current($ignore)) && (empty($stPhone))) {
                /** @var string $code */
                $code = key($ignore);
                $stnwPhone = $this->getValidCellPhoneFormat($phone, $code, PhoneNumberFormat::NATIONAL);
                foreach ($numbers as $number) {
                    $tmPhone = $this->getValidCellPhoneFormat($number, $code, PhoneNumberFormat::NATIONAL);
                    if ($tmPhone === $stnwPhone) {
                        $stPhone = $tmPhone;
                    }
                }

                next($ignore);
            }
        }

        return $stPhone;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function checkVariable($value)
    {
        return isset($value) && !empty($value) && !is_null($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getAttributeValueConsulted($value)
    {
        $type = gettype($value);
        switch ($type) {
            case 'integer':
                return intval($value);
                break;
            case 'double':
                return floatval($value);
                break;
            case 'string':
                if (is_numeric($value)) {
                    return floatval($value);
                }
                break;
            case 'NULL':
                return 0;
        }

        return $value;
    }

    /**
     * @param object $object
     * @param boolean $assoc
     * @return mixed
     */
    public function convertObjectToMixed($object, $assoc = true)
    {
        return json_decode(json_encode($object), $assoc);
    }

    /**
     * @param string $cadena
     * @param mixed $caracteres
     * @return mixed
     */
    public function checkPattern($cadena, $caracteres)
    {
        if (is_array($caracteres)) {
            $caracteresEncontrados = array();
            foreach ($caracteres as $value) {
                if (strstr($cadena, $value)) {
                    $caracteresEncontrados[] = $value;
                }
            }

            return $caracteresEncontrados;
        } else {
            if (strstr($cadena, $caracteres)) {
                return $caracteres;
            }
        }

        return false;
    }

    /**
     * @param array $headers
     * @return array
     */
    public function createHeaderExportExcel($headers)
    {
        $tmps = [];
        foreach ($headers as $key => $header) {
            if (is_array($header)) {
                $key = array_keys($header);
                $tmps[] = $key[0];
            } else {
                $tmps[] = $header;
            }
        }

        return $tmps;
    }

    /**
     * @param array $paths
     * @param array|string $ext
     * @param boolean $all
     * @param string $match
     * @return boolean
     */
    public function checkFileExtension(array $paths, $exts = ['png', 'jpg', 'jpeg'], bool $all = true, $match = "/^image/i")
    {
        $sw = false;
        $countOccurrence = 0;
        $images = $this->getFileFormatsGroup($match);
        while ($current = current($images)) {
            foreach ($paths as $path) {
                $fileExt = $this->getClientOriginalExtension($path);
                $fileExtType = $this->getAppConfigMimeType($fileExt);
                if (is_array($exts)) {
                    $index = 0;
                    $sw = false;
                    while (($index < count($exts)) && ($sw === false || $all === true)) {
                        $ext = $exts[$index];
                        $extType = $this->getAppConfigMimeType($ext);
                        if ($current === $extType && $fileExtType === $extType) {
                            $countOccurrence++;
                            $sw = true;
                        }
                        $index++;
                    }
                } elseif (is_string($exts)) {
                    $extType = $this->getAppConfigMimeType($exts);
                    if (($current === $extType) && ($fileExtType === $extType)) {
                        $countOccurrence++;
                        $sw = true;
                    }
                }
                if ($all === true && $sw === true && $countOccurrence === count($paths)) {
                    return true;
                }
                if ($all === false && $sw === true) {
                    return true;
                }
            }

            next($images);
        }


        return false;
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @param string $contractsRoute
     * @param string $servicesRoute
     * @return void
     */
    public function registerServices($app, string $contractsRoute, string $servicesRoute)
    {
        $contracts = scan_route($contractsRoute, '\\');
        $services = scan_route($servicesRoute, '\\');

        foreach ($services as $service) {
            $match = Str::afterLast($service, '\\');
            $contract = $this->findFirstMatch($contracts, "/\b{$match}/i");

            if (is_null($contract)) {
                continue;
            }

            $app->bind($contract, $service);
        }
    }

    /**
     * @param string $route
     * @param string $mode
     * @return mixed
     */
    public function getFileDetail(string $route, string $mode = 'r')
    {
        try {
            $file = fopen($route, $mode);

            if (!$file) {
                return false;
            }

            // Recuperar meta-información o de cabecera de punteros a flujos/archivo
            $metaData = stream_get_meta_data($file);

            fclose($file);

            return $metaData;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return string|false
     */
    public function getRealpath(string $path = __DIR__)
    {
        return realpath($path);
    }

    /**
     * @param string $path
     * @param int $levels
     * @return string
     */
    public function getDirname(string $path = __FILE__, int $levels = 1)
    {
        return dirname($path, $levels);
    }

    /**
     * @param string $rootPath
     * @param string $path
     */
    public function getSystemRoute(string $rootPath, string $path)
    {
        $ios = php_uname('s');
        $realpath = $this->getRealpath($rootPath);
        if (preg_match('/window/i', $ios) === 1) {
            $path = str_replace("/", "\\", $path);
        }

        return $this->getRealpath($realpath . "/" . $path);
    }

    /**
     * @return string
     */
    public function createRouteColorsAccordingToSystem()
    {
        $realpath = $this->getRealpath();
        $parentpath = $this->getDirname($realpath, 3);

        return $this->getSystemRoute($parentpath, "/src/config/color-names.json");
    }

    /**
     * @param string|null $match
     * @return array
     */
    public function getColorsNames($match = null)
    {
        $path = $this->createRouteColorsAccordingToSystem();
        $datos = file_get_contents($path);
        $arrDatos = json_decode($datos, true);

        if (!is_null($match)) {
            return $this->getAllAssociatedValues($arrDatos, $match, true);
        }

        return $arrDatos;
    }

    /**
     * @param string|null $match
     * @return array
     */
    public function convertHexToRGB($match = null)
    {
        $colores = $this->getColorsNames($match);
        $arrRGB = array_keys($colores);
        $rgb = [];
        foreach ($arrRGB as $color) {
            list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
            $rgb[] = "rgb({$r}, {$g}, {$b})";
        }

        return $rgb;
    }

    /**
     * Verifica que un arreglo tengo un solo tipo de datos.
     * @param array $array
     * @param string $type
     * @return boolean
     */
    public function verifyOnlyOneType(array $array, string $type = 'integer')
    {
        $sw = true;
        $index = 0;
        while ($index < count($array) && $sw === true) {
            if (!(gettype($array[$index]) === $type)) {
                $sw = false;
            }
            $index++;
        }

        return $sw;
    }

    /**
     * Destruye las variables especificadas.
     * @param boolean $all
     * @param array $array
     * @param string|int|null $key Permite null si se va a eliminar todo el array
     * @return void|boolean
     */
    public function unsetArray(bool $all, array &$array, $key = null)
    {
        if ($all === false && is_null($key)) {
            return false;
        }

        if ($all === true) {
            $index = count($array);
            while ($index >= 0) {
                unset($array[$index]);
                $index--;
            }
        } else {
            unset($array[$key]);
        }

        return true;
    }

    /**
     * @param string $json
     * @return string|string[]|null
     */
    public function jsonFixer($json)
    {
        $patterns = [];
        /** garbage removal */
        $patterns[0] = "/([\s:,\{}\[\]])\s*'([^:,\{}\[\]]*)'\s*([\s:,\{}\[\]])/"; // Find any character except colons, commas, curly and square brackets surrounded or not by spaces preceded and followed by spaces, colons, commas, curly or square brackets...
        $patterns[1] = '/([^\s:,\{}\[\]]*)\{([^\s:,\{}\[\]]*)/'; // Find any left curly brackets surrounded or not by one or more of any character except spaces, colons, commas, curly and square brackets...
        $patterns[2] =  "/([^\s:,\{}\[\]]+)}/"; // Find any right curly brackets preceded by one or more of any character except spaces, colons, commas, curly and square brackets...
        $patterns[3] = "/(}),\s*/"; // JSON.parse() doesn't allow trailing commas
        /** reformatting */
        $patterns[4] = '/([^\s:,\{}\[\]]+\s*)*[^\s:,\{}\[\]]+/'; // Find or not one or more of any character except spaces, colons, commas, curly and square brackets followed by one or more of any character except spaces, colons, commas, curly and square brackets...
        $patterns[5] = '/["\']+([^"\':,\{}\[\]]*)["\']+/'; // Find one or more of quotation marks or/and apostrophes surrounding any character except colons, commas, curly and square brackets...
        $patterns[6] = '/(")([^\s:,\{}\[\]]+)(")(\s+([^\s:,\{}\[\]]+))/'; // Find or not one or more of any character except spaces, colons, commas, curly and square brackets surrounded by quotation marks followed by one or more spaces and  one or more of any character except spaces, colons, commas, curly and square brackets...
        $patterns[7] = "/(')([^\s:,\{}\[\]]+)(')(\s+([^\s:,\{}\[\]]+))/"; // Find or not one or more of any character except spaces, colons, commas, curly and square brackets surrounded by apostrophes followed by one or more spaces and  one or more of any character except spaces, colons, commas, curly and square brackets...
        $patterns[8] = '/(})(")/'; // Find any right curly brackets followed by quotation marks...
        $patterns[9] = '/,\s+(})/'; // Find any comma followed by one or more spaces and a right curly bracket...
        $patterns[10] = '/\s+/'; // Find one or more spaces...
        $patterns[11] = '/^\s+/'; // Find one or more spaces at start of string...

        $replacements = [];
        /** garbage removal */
        $replacements[0] = '$1 "$2" $3'; // ...and put quotation marks surrounded by spaces between them;
        $replacements[1] = '$1 { $2'; // ...and put spaces between them;
        $replacements[2] = '$1 }'; // ...and put a space between them;
        $replacements[3] = '$1'; // ...so, remove trailing commas of any right curly brackets;
        /** reformatting */
        $replacements[4] = '"$0"'; // ...and put quotation marks surrounding them;
        $replacements[5] = '"$1"'; // ...and replace by single quotation marks;
        $replacements[6] = '\\$1$2\\$3$4'; // ...and add back slashes to its quotation marks;
        $replacements[7] = '\\$1$2\\$3$4'; // ...and add back slashes to its apostrophes;
        $replacements[8] = '$1, $2'; // ...and put a comma followed by a space character between them;
        $replacements[9] = ' $1'; // ...and replace by a space followed by a right curly bracket;
        $replacements[10] = ' '; // ...and replace by one space;
        $replacements[11] = ''; // ...and remove it.

        $result = preg_replace($patterns, $replacements, $json);

        return $result;
    }

    /**
     * @param array $array
     * @param string $delimiter
     * @return bool
     */
    public function verifyKeysOneADelimiter(array $array, string $delimiter = '.')
    {
        $sw = true;
        $index = 0;
        while ($index < count($array) && $sw === true) {
            if (strpos($array[$index], $delimiter) === false) {
                $sw = false;
            }
            $index++;
        }

        return $sw;
    }

    /**
     * @param array $request
     * @param string $key
     * @param mixed $default
     */
    public function getValueRequest($request, $key, $default = null)
    {
        $result = array_get($request, $key, null);
        if (is_null($result) && !is_null($default)) {
            $result = array_get($request, $default, null);
        }

        return $result;
    }

    /**
     * @param string $path
     */
    public function getDurationInSeconds(string $path)
    {
        /** @var AudioInfo */
        $file = new AudioInfo($path);
        /** @var array|null */
        $data = $file->getFileInfo();
        if (is_null($data) || !isset($data['playtime_seconds'])) {
            return null;
        }

        return $data['playtime_seconds'];
    }

    /**
     * @param string $html
     * @param string $replace
     * @param string $expression
     * @param string $byId
     * @param int $index
     * @return string|false
     */
    public function replaceHTMLElement(string $html, string $replace, string $expression = "//table/tr/td/div/br", string $byId = 'firma-cliente', int $index = -1)
    {
        error_reporting(false);

        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $entries = $xpath->query($expression);
        if ($entries->length > 0) {
            $temp = new DOMDocument('1.0', 'UTF-8');
            $temp->loadHTML($replace);
            $replacement = $temp->getElementById($byId);
            $firstElement = $entries->item(($index === -1) ? ($entries->length - 1) : $index);
            $img = $firstElement->ownerDocument->importNode($replacement, true);
            $firstElement->parentNode->replaceChild($img, $firstElement);
            $html = $doc->saveHTML();

            return $html;
        }

        return false;
    }

    /**
     * Establecer entidades HTML
     * @param array $datas
     * @return array
     */
    public function setHtmlentities($datas)
    {
        $tab = array();
        foreach ($datas as $key => $data) {
            if (is_array($data)) {
                $values = array();
                foreach ($data as $value) {
                    $values[] = htmlentities($value, ENT_QUOTES, 'UTF-8');
                }
                $tab[] = $values;
            } else {
                $tab[$key] = htmlentities($data, ENT_QUOTES, 'UTF-8');
            }
        }

        return $tab;
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $inputIds
     * @return DOMDocument
     */
    public function sendExternalFormDataHipotecaria(string $url, array $params, array $inputIds)
    {
        /** @var string $path */
        $path = str_replace('?', '', $this->createTheParametersOfTheUrl('', $params));
        /** @var \CurlHandle|false $ch */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: es-es,en'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $path);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /** @var string|bool $data */
        $data = curl_exec($ch);
        curl_close($ch);

        libxml_use_internal_errors(true);

        return $this->modifyFormEntriesHipotecaria($data, $inputIds);
    }

    /**
     * @param string|bool $data
     * @param array $inputIds
     * @return DOMDocument
     */
    public function modifyFormEntriesHipotecaria($data, array $inputIds)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($data);
        $xpath = new DOMXPath($doc);

        // Crear elemento
        foreach ($inputIds as $id => $value) {
            if (isset($value['type'], $value['options']) && $value['type'] === 'select') {
                /** @var array $options */
                $options = $value['options'];
                $nodeA = $doc->getElementById($id);
                foreach ($options as $option) {
                    $nodeB = $doc->createElement("option", $option['text']);
                    $nodeB->setAttribute("value", $option['value']);
                    if ($value['value'] === $option['value']) {
                        $nodeB->setAttribute("selected", "selected");
                    } elseif ($value['value'] === $option['text']) {
                        $nodeB->setAttribute("selected", "selected");
                    }
                    $nodeA->appendChild($nodeB);
                }
            } elseif (isset($value['type']) && !isset($value['options']) && $value['type'] === 'select') {
                $nodeA = $doc->getElementById($id);
                foreach ($nodeA->childNodes as $option) {
                    $node = $xpath->query("//{$option->getNodePath()}/attribute::value");
                    if ($node->item(0)->value === $value['value']) {
                        $option->setAttribute('selected', 'selected');
                    }
                }
            } elseif (isset($value['type']) && $value['type'] === 'radio') {
                $nodeA = $doc->getElementById($id);
                $nodeA->setAttribute('checked', 'checked');
            } else {
                $nodeA = $doc->getElementById($id);
                $nodeA->setAttribute('value', $value['value']);
            }
            $doc->saveXML();
        }

        return $doc;
    }

    /**
     * @param string $twilioUrl
     * @return array
     */
    public function downloadTwilioFile($twilioUrl)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $twilioUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $contents = $this->streamContextCreate($info, null, 'GET'); 
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mineType = $finfo->buffer($contents);

        return [
            'direct_result' => $result,
            'transfer_information' => $info,
            'contents' => $contents,
            'type' => $mineType,
        ];
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logAlert(string $message, array $context = [])
    {
        Log::alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logCritical(string $message, array $context = [])
    {
        Log::critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logDebug(string $message, array $context = [])
    {
        Log::debug($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logEmergency(string $message, array $context = [])
    {
        Log::emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logError(string $message, array $context = [])
    {
        Log::error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logInfo(string $message, array $context = [])
    {
        Log::info($message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logLaravel($level, string $message, array $context = [])
    {
        Log::log($level, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logNotice(string $message, array $context = [])
    {
        Log::notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logWarning(string $message, array $context = [])
    {
        Log::warning($message, $context);
    }
}
