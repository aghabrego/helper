<?php

use Tests\TestCase;
use Weirdo\Helper\BaseClass;

class HelperArrayTest extends TestCase
{
    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      'start_date' => '2021-10-18'
     *      'final_date' => '2021-10-24'
     *   ]
     */
    public function testFinalStartDateOfOneWeek()
    {
        $base = new BaseClass;

        $result = $base->getFinalStartDateOfOneWeek('22 October 2021');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);

        $result = $base->getFinalStartDateOfOneWeek('2021-10-22');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);

        $result = $base->getFinalStartDateOfOneWeek('+1 day');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);

        $result = $base->getFinalStartDateOfOneWeek('+1 week');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);

        $result = $base->getFinalStartDateOfOneWeek('+1 week 2 days 4 hours 2 seconds');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);

        $result = $base->getFinalStartDateOfOneWeek('now');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      'start_date' => '2021-10-11'
     *      'final_date' => '2021-10-17'
     *   ]
     */
    public function testScheduleDate()
    {
        $base = new BaseClass;
        $result = $base->getScheduleDate('22 October 2021');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      'start_date' => '2021-10-25'
     *      'final_date' => '2021-10-31'
     *   ]
     */
    public function testActiveSchedulingDate()
    {
        $base = new BaseClass;
        $result = $base->getActiveSchedulingDate('22 October 2021');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('final_date', $result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      0 => "A"
     *      1 => "C"
     *  ]
     */
    public function testValues()
    {
        $base = new BaseClass;
        $values = [
            '1' => 'A',
            '3' => 'C',
        ];
        $result = $base->getValues($values);
        $this->assertIsArray($result);
    }

    public function testArrayFirst()
    {
        $base = new BaseClass;
        $values = ['1', '5', 'true', '4', 3];

        $result = $base->arrayFirst($values, true);
        $this->assertNotNull($result);

        $result = $base->arrayFirst($values, 'true');
        $this->assertNotNull($result);

        $result = $base->arrayFirst($values, '3');
        $this->assertNotNull($result);

        $result = $base->arrayFirst($values, 3);
        $this->assertNotNull($result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     * 1
     *
     * @return void
     */
    public function testArrayFirstIndex()
    {
        $base = new BaseClass;
        $array = ['Settings', "MatchController"];
        $index = $base->arrayFirstIndex($array, 'MatchController');
        $this->assertNotEquals($index, -1);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:6 [
     *      0 => "Settings"
     *      1 => "MatchController"
     *      2 => false
     *      3 => "Home"
     *      4 => true
     *      5 => "Si"
     *  ]
     */
    public function testArrayInsertWithoutDoesNotExist()
    {
        $base = new BaseClass;
        $array = ['Settings', "MatchController", false, 'No', 'otro mundo', 'Home', true];
        $array = $base->arrayInsertWithoutDoesNotExist($array, [false, 'Si', 'No', 'no', 'Home', 'yes', 'otro mundo']);
        $this->assertIsArray($array);
        $this->assertEquals(in_array("no", $array, true), false);
        $this->assertEquals(in_array("No", $array, true), true);
        $array2 = [
            "RUC" => "111513-1-380165",
            "RUC/CIP" => "001",
            "DV" => "56",
        ];
        $array2 = $base->arrayInsertWithoutDoesNotExist($array2, [
            "RUC" => "111513-1-380164",
            "RUC/CIP" => "001",
        ]);
        $this->assertArrayHasKey("RUC0", $array2);

        $array3 = [
            "RUC" => "111513-1-380165",
            "RUC/CIP" => "001",
            "DV" => "56",
        ];
        $array3 = $base->arrayInsertWithoutDoesNotExist($array3, [
            "RUC" => "111513-1-380165",
            "RUC" => "111513-1-380166",
            "RUC1" => "111513-1-380164",
            "RUC/CIP" => "001",
        ]);
        $this->assertArrayHasKey("RUC0", $array3);
        $this->assertArrayHasKey("RUC1", $array3);

        $array4 = ['Settings', "MatchController", false, 'otro mundo', 'Home', true];
        $array4 = $base->arrayInsertWithoutDoesNotExist($array4, "no");
        $array4 = $base->arrayInsertWithoutDoesNotExist($array4, "yes");
        $array4 = $base->arrayInsertWithoutDoesNotExist($array4, true);
        $array4 = $base->arrayInsertWithoutDoesNotExist($array4, "hola");
        $this->assertIsArray($array4);
        $this->assertEquals(in_array("hola", $array4, true), true);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      0 => array:2 [
     *          "id" => 1
     *          "name" => "Ángel Hidalgo"
     *      ]
     *      1 => array:2 [
     *          "id" => 2
     *          "name" => "Jose Hidalgo"
     *      ]
     *  ]
     */
    public function testArrayOnly()
    {
        $base = new BaseClass;
        $values = [
            [
                'id' => 1,
                'grupo_a' => 'ah',
                'name' => 'Ángel Hidalgo',
            ],
            [
                'id' => 2,
                'grupo_a' => 'jh',
                'name' => 'Jose Hidalgo',
            ],
        ];

        $result = $base->arrayOnly($values, ['name', 'id']);
        $this->assertIsArray($result);

        $valuesOb = [
            [
                (object)[
                    'id' => 1,
                    'grupo_a' => 'ah',
                    'name' => 'Ángel Hidalgo',
                ]
            ]
        ];
        $result = $base->arrayOnly($valuesOb, ['name', 'id']);
        $this->assertIsArray($result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      0 => "nombre"
     *      1 => "celular"
     *  ]
     */
    public function testArrayValueOnly()
    {
        $base = new BaseClass;
        $values = ["id", "nombre", "celular", "email"];

        $result = $base->arrayValueOnly($values, "celular");
        $this->assertIsArray($result);
        $this->assertContains('celular', $result);

        $result = $base->arrayValueOnly($values, ["nombre", "celular"]);
        $this->assertIsArray($result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:1 [
     *      1 => array:3 [
     *          "nombre" => "jose"
     *          "segundo_nombre" => "gabriel"
     *          "apellido" => "hidalgo"
     *      ]
     *  ]
     * 
     *  array:5 [
     *      0 => 20
     *      3 => 100
     *      4 => 10
     *      6 => 15
     *      7 => 15
     *  ]
     */
    public function testValidArray()
    {
        $base = new BaseClass;
        $values = [
            ['nombre' => 'angel', 'segundo_nombre' => null, 'apellido' => 'hidalgo'],
            ['nombre' => 'jose', 'segundo_nombre' => 'gabriel', 'apellido' => 'hidalgo']
        ];
        $result = $base->validArray($values, 'segundo_nombre');
        $this->assertIsArray($result);

        $values = [20, 0, null, 100, 10, null, 15, 15];
        $result = $base->validArray($values);
        $this->assertIsArray($result);
    }

    public function testSksort()
    {
        $base = new BaseClass;
        $array = [0 => [
            0 => ['id' => 1878, 'name' => 'isabel(90)'],
            1 => ['id' => 1877, 'name' => 'isabel(100)'],
            2 => ['id' => 1876, 'name' => 'isabel(5)'],
            3 => ['id' => 1875, 'name' => 'florencia(200)'],
            4 => ['id' => 1874, 'name' => 'isabel(40)'],
            5 => ['id' => 1873, 'name' => 'isabel(30)'],
        ]];
        $base->sksort($array, 'name', true, false, SORT_NUMERIC);
        $this->assertIsArray($array);

        $array2 = [];
        $base->sksort($array2, 'name', true, false, SORT_NUMERIC);
        $this->assertIsArray($array2);
    }

    public function testKsort()
    {
        $base = new BaseClass;
        $array = [
            0 => [
                "name" => "cedula_adelantes",
                "value" => 99.77481,
            ],
            1 => [
                "name" => "firmas",
                "value" => 0.006500661,
            ],
            2 => [
                "name" => "talonario",
                "value" => 0.04616601,
            ],
            3 => [
                "name" => "fichas",
                "value" => 0.030120643,
            ],
            4 => [
                "name" => "carta_de_trabajos",
                "value" => 0.13672048,
            ],
            5 => [
                "name" => "cedula_atras",
                "value" => 0.0056763394,
            ],
          ]
          ;
        $base->sksort($array, 'value', true, false, SORT_NUMERIC);
        $this->assertIsArray($array);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:1 [
     *      0 => 2
     *  ]
     * 
     *  [
     *      "id" => 2,
     *      "tipo_pago_banco_id" => 1,
     *      "visible" => true,
     *      "aprobado" => null,
     *      "tipo_pago_id" => 4,
     *      "cuenta_proyecto_banco_id" => 2,
     *      "banco_id" => "1",
     *      "monto_cheque" => 150,
     *      "fecha" => "2021-10-25",
     *      "file" => "",
     *      "comentarios" => "",
     *  ]
     * 
     *  array:1 [
     *      0 => 6
     *  ]
     */
    public function testArrayFirstPerKey()
    {
        $base = new BaseClass;

        $values = [5, 2, 3];
        $result = $base->arrayFirstPerKey($values, 1);
        $this->assertIsArray($result);

        $valuesCh = [
            [
                "id" => 1,
                "tipo_pago_banco_id" => 1,
                "visible" => true,
                "aprobado" => null,
                "tipo_pago_id" => 1,
                "cuenta_proyecto_banco_id" => 2,
                "banco_id" => "0",
                "monto_cheque" => 150,
                "fecha" => "2021-10-25",
                "comentarios" => "",
            ],
            [
                "id" => 2,
                "tipo_pago_banco_id" => 1,
                "visible" => true,
                "aprobado" => null,
                "tipo_pago_id" => 4,
                "cuenta_proyecto_banco_id" => 2,
                "banco_id" => "1",
                "monto_cheque" => 150,
                "fecha" => "2021-10-25",
                "file" => "",
                "comentarios" => "",
            ],
        ];
        $result = $base->arrayFirstPerKey($valuesCh, ['banco_id', 'id', 'file']);
        $this->assertIsArray($result);

        $values = [10, 6, 3];
        $result = $base->arrayFirstPerKey($values, [1, 2]); // error
        $this->assertIsArray($result);

        $result = $base->arrayFirstPerKey($valuesCh, 'banco_id');
        $this->assertIsArray($result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      100 => array:2 [
     *          0 => array:2 [
     *              "vivienda_id" => 100
     *              "casa" => "A-01"
     *          ]
     *          1 => array:2 [
     *              "vivienda_id" => 100
     *              "casa" => "A-03"
     *          ]
     *      ]
     *      101 => array:1 [
     *          0 => array:2 [
     *              "vivienda_id" => 101
     *              "casa" => "A-02"
     *          ]
     *      ]
     *  ]
     */
    public function testArrayGroupBy()
    {
        $base = new BaseClass;
        $values = [
            [
                'vivienda_id' => 100,
                'casa' => 'A-01',
            ],
            [
                'vivienda_id' => 101,
                'casa' => 'A-02',
            ],
            [
                'vivienda_id' => 100,
                'casa' => 'A-03',
            ]
        ];
        $result = $base->arrayGroupBy($values, 'vivienda_id');
        $this->assertIsArray($result);
    }

    public function testArrayFirstIndexWithString()
    {
        $base = new BaseClass;
        $array = ['Settings', "MatchController", "MatchController", "Home"];
        $index = $base->arrayFirstIndexWith($array, 'MatchController');
        $this->assertEquals($index, 1);
    }

    public function testArrayFirstWith()
    {
        $base = new BaseClass;

        $request = [
            "column" => "id",
            "operator" => "between",
            "query_1" => "1",
            "query_2" => "5",
        ];
        // Error
        $result = $base->arrayFirstWith(
            $request,
            [
                "column" => "id",
                "query_1" => "1",
            ]
        );
        $this->assertNull($result);

        $result = $base->arrayFirstWith(
            $request,
            [
                "query_1" => "1",
            ]
        );
        $this->assertNotNull($result);
    }

    public function testArrayFirstWithMatriz()
    {
        $request = [
            0 => [
                "column" => "id",
                "operator" => "between",
                "query_1" => "1",
                "query_2" => "5",
            ],
            1 => [
                "column" => "modelo_proyecto_id",
                "operator" => "equal_to",
                "query_1" => "6",
            ],
        ];
        $base = new BaseClass;
        $result = $base->arrayFirstWith(
            $request,
            [
                "column" => "modelo_proyecto_id",
                "query_1" => "6",
            ]
        );
        $this->assertIsArray($result);
    }

    public function testArrayFirstWithNull()
    {
        $request = [
            0 => [
                "column" => "modelo_proyecto_id",
                "operator" => "equal_to",
                "query_1" => "6",
            ],
            1 => [
                "column" => null,
                "operator" => null,
                "query_1" => null,
                "query_2" => null,
            ],
        ];
        $base = new BaseClass;
        $result = $base->arrayFirstWith(
            $request,
            [
                "column" => [],
                "operator" => []
            ]
        );
        $this->assertIsArray($result);
    }

    public function testArrayFirstIndexWithArray()
    {
        $base = new BaseClass;
        $array = [
            [
                "column" => "tipo_pago_id",
                "operator" => "equal_to",
                "query_1" => "1",
            ],
            [
                "column" => "pago_id",
                "operator" => "equal_to",
                "query_1" => "315",
            ],
        ];
        $index = $base->arrayFirstIndexWith($array, ["query_1" => "315"]);
        $this->assertNotEquals($index, -1);
    }

    public function testArrayFirstIndexWithArrayMult()
    {
        $base = new BaseClass;
        $array = [[174, 213, 171], [119, 205, 161]];
        $index = $base->arrayFirstIndexWith($array, [119, 205, 161]);
        $this->assertNotEquals($index, -1);
    }

    public function testPanamaID()
    {
        $base = new BaseClass;
        $matches = $base->panamaID('9-734-1000', 3, 4, '-');
        $this->assertCount(4, $matches);

        $id = '8-888-8888';
        $delimite = '';
        $isDelimite = $base->findStringOccurrence($id, '-');
        if ($isDelimite !== false) {
            $delimite = '-';
        }
        $matches = $base->panamaID($id, 4, 6, $delimite);
        $this->assertCount(4, $matches);
    }

    public function testFindFirstMatch()
    {
        $base = new BaseClass;
        $array = ['Settings', "MatchController"];
        $result = $base->findFirstMatch($array, "/Controller/i");
        $this->assertNotNull($result);
    }

    public function testGetAllAssociatedValues()
    {
        $base = new BaseClass;
        $array = [
            [
                "column" => "tipo_pago_id",
                "operator" => "equal_to",
                "query_1" => "1",
            ],
            [
                "column" => "pago_id",
                "operator" => "equal_to",
                "query_1" => "315",
            ],
        ];
        $result = $base->getAllAssociatedValues($array, ["operator" => "equal_to"]);
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testGetAllAssociatedValuesString()
    {
        $base = new BaseClass;
        $array = ['Settings', "MatchController", "Settings"];
        $result = $base->getAllAssociatedValues($array, 'Settings');
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testfirstAppearanceIndexWithAdvisor()
    {
        $array = [
            [
                "column" => "tipo_pago_id",
                "operator" => "equal_to",
                "query_1" => "14",
            ],
            [
                "column" => "pago_id",
                "operator" => "equal_to",
                "query_1" => "315,20",
            ],
        ];
        $base = new BaseClass;
        $result = $base->getAllAssociatedValues($array, ["query_1" => "/,/i"], true);
        $this->assertIsArray($result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  ejemplo 1:
     *  array:1 [
     *      0 => 2
     *  ]
     * 
     *  ejemplo 2:
     *  array:1 [
     *      0 => 1
     *      1 => 3
     *  ]
     *
     *  ejemplo 3:
     *  array:2 [
     *      0 => 0
     *      1 => 2
     *  ]
     * 
     *  ejemplo 4:
     *  array:3 [
     *      0 => "email"
     *      1 => "celular"
     *      2 => "created"
     *  ]
     *  
     *  ejemplo 5:
     *  array:1 [
     *      0 => 1
     *  ]
     * 
     */
    public function testAllAssociatedKeyValues()
    {
        $base = new BaseClass;

        // Ejemplo 1
        $values = ['aghabrego@gmail.com', 'ahidalgo@weirdolabs.me', 'angel09-08@hotmail.com', 'criptoy13@gmail.com'];
        $result = $base->getAllAssociatedKeyValues($values, 'angel09-08@hotmail.com');
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        // Ejemplo 2
        $values = ['aghabrego@gmail.com', 'ahidalgo@weirdolabs.me', 'angel09-08@hotmail.com', 'criptoy13@gmail.com'];
        $result = $base->getAllAssociatedKeyValues($values, ['criptoy13@gmail.com', 'ahidalgo@weirdolabs.me']);
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        // Ejemplo 3
        $values = [
            [
                'created' => '2018-06-10',
                'nombre' => 'Angel Hidalgo',
                'celular' => '62141900',
                'email' => 'aghabrego@gmail.com',
                'only' => [
                    'test' => 1,
                ],
            ],
            [
                'created' => '2018-06-10',
                'nombre' => 'Angel Hidalgo',
                'celular' => '62141994',
                'email' => 'angel09-08@hotmail.com',
                'only' => [
                    'test' => 2,
                ],
            ],
            [
                'created' => '2018-06-10',
                'nombre' => 'Angel Hidalgo',
                'celular' => '62141900',
                'email' => 'aghabrego@gmail.com',
                'only' => [
                    'test' => 3,
                ],
            ]
        ];
        $result = $base->getAllAssociatedKeyValues($values, ['email' => 'aghabrego@gmail.com', 'celular' => '62141900']);
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        $result = $base->getAllAssociatedKeyValues($values, ['only.test' => 2, 'celular.s' => '62141994']);
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        // Ejemplo 4
        $values = [
            'created' => '2018-06-10',
            'nombre' => 'Angel Hidalgo',
            'celular' => '62141900',
            'email' => 'aghabrego@gmail.com',
        ];
        $result = $base->getAllAssociatedKeyValues($values, ['email' => 'aghabrego@gmail.com', 'celular' => '62141900', 'created' => '2018-06-10']);
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        // Ejemplo 5
        $values = [10, 20, 15];
        $result = $base->getAllAssociatedKeyValues($values, 20);
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        // Ejemplo 6
        $values = [
            'created' => '2018-06-10',
            'nombre' => 'Angel Hidalgo',
            'celular' => '62141900',
            'email' => 'aghabrego@gmail.com',
        ];
        $result = $base->getAllAssociatedKeyValues($values, '62141900');
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);

        // Ejemplo 7
        $values = [[20, 5, 7], [30, 23, 10], [15, 16, 8], [17, 9]];
        $result = $base->getAllAssociatedKeyValues($values, 15);
        $this->assertNotEquals($result, -1);
        $this->assertIsArray($result);
    }

    public function testArrayFlattenWithOnly()
    {
        $base = new BaseClass;
        $array = [
            [
                "column" => "tipo_pago_id",
                "operator" => "equal_to",
                "query_1" => "1",
            ],
            [
                "column" => "pago_id",
                "operator" => "equal_to",
                "query_1" => "315",
            ],
        ];
        $result = $base->arrayFlattenWithOnly($array, ['column']);
        $this->assertIsArray($result);
        $this->assertEquals($result, ['tipo_pago_id', 'pago_id']);
    }

    public function testArrayExcept()
    {
        $base = new BaseClass;
        $values = [
            "public/sale/2022-02/JVfLl9qDy6YvxcfH5KU4HAyYl3m9mh110F2CBusx.jpg",
            "public/sale/2022-02/ZJoCjPgKAgXqCbvW9WsechfECanEsjhNhuBPheBr.jpg",
            "public/sale/2022-02/uHq9DkOpIbVyglJgxrZ5YpAB17RYLGHrivV2M42y.jpg",
            "public/sale/2022-02/MyLaXak8lSM4BcyQGI4QixYlNqBLfZr6MKq4JM00.png",
            "public/sale/2022-02/PFJOGmj5Bh4pfczZ7xysMb2AVhtAMpiYYHr5Ufaq.png",
            "public/sale/2022-02/erEnQ3ptl20hnxwIRU4oRUbkcJKwXb89syl2F2Le.jpg"
        ];
        $result = $base->arrayExcept($values, "public/sale/2022-02/uHq9DkOpIbVyglJgxrZ5YpAB17RYLGHrivV2M42y.jpg");
        $this->assertIsArray($result);
        $this->assertNotContains("public/sale/2022-02/uHq9DkOpIbVyglJgxrZ5YpAB17RYLGHrivV2M42y.jpg", $result);
    }

    public function testArrayExceptWith()
    {
        $base = new BaseClass;
        $values = [
            [
                "id" => 4030,
                "tipo_pago_banco_id" => 1,
                "visible" => true,
                "aprobado" => true,
                "tipo_pago_id" => 4,
                "cuenta_proyecto_banco_id" => 8,
                "banco_id" => 359,
                "monto_cheque" => "2701.36",
                "fecha" => "2022-08-23",
                "file" => "",
                "comentarios" => "Pago",
            ],
            [
                "id" => 0,
                "tipo_pago_banco_id" => 1,
                "visible" => true,
                "aprobado" => false,
                "tipo_pago_id" => 4,
                "cuenta_proyecto_banco_id" => 8,
                "banco_id" => "359",
                "monto_cheque" => 1,
                "fecha" => "2022-08-24",
                "file" => "",
                "comentarios" => "pago prueba",
            ],
        ];
        $result = $base->arrayExcept($values, ['aprobado' => false]);
        $this->assertIsArray($result);
        $id = $result[0]['id'];
        $this->assertEquals(4030, $id);
    }

    public function test_arrayExceptWithExpression()
    {
        $base = new BaseClass;
        $datos = [".", "..", "01- ENERO"];
        $result = $base->arrayExcept($datos, ["/\./i"], true);
        $this->assertIsArray($result);
    }

    public function test_getFirstFileExtensionByMimeType()
    {
        $base = new BaseClass;
        $mimeType = "audio/x-wav";
        $test = $base->getFirstFileExtensionByMimeType($mimeType);
        $this->assertNotNull($test);
        $this->assertEquals($test, 'wav');
    }

    public function test_getFileExtensionByMimeType()
    {
        $base = new BaseClass;
        $mimeType = "audio/x-";
        $test = $base->getFileExtensionByMimeType($mimeType);
        $this->assertIsArray($test);
        $this->assertContains('aac', $test);
        $this->assertContains('aif', $test);
        $this->assertContains('caf', $test);
        $this->assertContains('flac', $test);
        $this->assertContains('mka', $test);
        $this->assertContains('m3u', $test);
        $this->assertContains('wma', $test);
        $this->assertContains('wax', $test);
        $this->assertContains('ram', $test);
        $this->assertContains('rmp', $test);
        $this->assertContains('wav', $test);
    }
}
