<?php

use ACME\Entity\Customer;
use ACME\Entity\Product;

class TestStuff extends PHPUnit_Framework_TestCase {

    // horrible contrived code
    public function canCustomerBuyProducts_0($customer, $products) {
        $result = [];
        foreach ($products as $product) {
            if ($customer['age'] < $product['age']) {
                $result[$product['title']] = ['reason' => 'age', 'valid' => false];
                continue;
            }
            if ($customer['type'] != $product['type']) {
                $result[$product['title']] = ['reason' => 'type', 'valid' => false];
                continue;
            }

            $result[$product['title']] = ['reason' => 'ok', 'valid' => true];

        }
        return $result;
    }

    // slightly better horrible contrived code
    public function canCustomerBuyProducts_1($customer, $products) {
        $result = [];
        foreach ($products as $product) {
            $validateAge = $customer['age'] >= $product['age'];
            $validateType = $customer['type'] == $product['type'];

            if (!$validateAge) {
                $result[$product['title']] = ['reason' => 'age', 'valid' => false];
                continue;
            }
            if (!$validateType) {
                $result[$product['title']] = ['reason' => 'type', 'valid' => false];
                continue;
            }

            $result[$product['title']] = ['reason' => 'ok', 'valid' => true];

        }
        return $result;
    }

    // moving conditional statements to functions
    public function canCustomerBuyProducts_2($customer, $products) {
        $validateAge = function($product) use ($customer) {
            return $customer['age'] >= $product['age'];
        };

        $validateType = function($product) use ($customer) {
            return $customer['type'] == $product['type'];
        };

        $result = [];
        foreach ($products as $product) {
            if (!$validateAge($product)) {
                $result[$product['title']] = ['reason' => 'age', 'valid' => false];
                continue;
            }
            if (!$validateType($product)) {
                $result[$product['title']] = ['reason' => 'type', 'valid' => false];
                continue;
            }

            $result[$product['title']] = ['reason' => 'ok', 'valid' => true];

        }
        return $result;
    }

    // whoa, array of predicates and mapping over array - short circuiting
    public function canCustomerBuyProducts_3($customer, $products) {
        $validations = [
            'age' => function($product) use ($customer) {
                return $customer['age'] >= $product['age'];
            },
            'type' => function($product) use ($customer) {
                return $customer['type'] == $product['type'];
            }
        ];

        // short circuiting
        $validation = function ($product) use ($validations) {
            foreach ($validations as $name => $validation) {
                if (!$validation($product)) return ['reason' => $name, 'valid' => false];
            }
            return ['reason' => 'ok', 'valid' => true];
        };

        $result = array_map($validation, $products);

        return $result;
    }

    // include all reasons why product isn't available
    public function canCustomerBuyProducts_4($customer, $products) {
        $validations = [
            'age' => function($product) use ($customer) {
                return $customer['age'] >= $product['age'];
            },
            'type' => function($product) use ($customer) {
                return $customer['type'] == $product['type'];
            }
        ];

        $validation = function ($product) use ($validations) {
            $result = [];
            $valid = true;
            foreach ($validations as $name => $validation) {
                $ruleValue = $validation($product);
                $result[$name] = $ruleValue;
                $valid = $valid && $ruleValue;
            }
            return ['cansell'=> $valid, 'rules' => $result];
        };

        $result = array_map($validation, $products);

        return $result;
    }

    // yes, we could do that in old version
    public function canCustomerBuyProducts_5($customer, $products) {
        $result = [];
        foreach ($products as $product) {
            $validateAge = $customer['age'] >= $product['age'];
//            $validateType = $customer['type'] == $product['type'];
//            $validateSomething = true;

//            $canSell = $validateAge && $validateType && $validateSomething;
            $canSell = $validateAge;

            $reasons = [
                'age' => $validateAge,
//                'type' => $validateType,
//                'something' => $validateSomething,
            ];

            $result[$product['title']] = [
                'cansell' => $canSell,
                'reasons' => $reasons
            ];

        }
        return $result;
    }

    // but extending functional example is easier
    public function canCustomerBuyProducts_6($customer, $products) {
        $validations = [
            'age' => function($product) use ($customer) {
                return $customer['age'] >= $product['age'];
            },
            'type' => function($product) use ($customer) {
                return $customer['type'] == $product['type'];
            },
            'something' => function($product) use ($customer) {
                return true;
            },

        ];

        $validation = function ($product) use ($validations) {
            $result = [];
            $valid = true;
            foreach ($validations as $name => $validation) {
                $ruleValue = $validation($product);
                $result[$name] = $ruleValue;
                $valid = $valid && $ruleValue;
            }
            return ['cansell'=> $valid, 'rules' => $result];
        };

        $result = array_map($validation, $products);

        return $result;
    }

    public function validateAge ($product, $customer) {
        return $customer['age'] >= $product['age'];
    }

    // what if I want to check just one validation?
    public function canCustomerBuyProducts_7($customer, $products) {
        $validations = [
            'age' => function($product) use ($customer) {
                return $customer['age'] >= $product['age'];
            },
            'type' => function($product) use ($customer) {
                return $customer['type'] == $product['type'];
            }
        ];

        $validate = function($name) use ($validations) {
            $fn = $validations[$name];
            return function($product) use ($name, $fn) {
                return [
                    $product['title'] => ['reason' => $name, 'valid' => $fn($product)]
                ];
            };
        };

        $result = array_map($validate('age'), $products);

        return $result;
    }

    // completely removing foreach and if statements with array_reduce
    // not that this should be done this way, just - it can be done :)
    public function canCustomerBuyProducts_8($customer, $products) {
        $validations = [
            'age' => function($product) use ($customer) {
                return $customer['age'] >= $product['age'];
            },
            'type' => function($product) use ($customer) {
                return $customer['type'] == $product['type'];
            }
        ];

        $validation = function($p) use ($validations) {
            return array_reduce(array_keys($validations), function ($carry, $key) use ($p, $validations) {
                $fn = $validations[$key];
                $success = $fn($p);
                return array_merge(
                    $carry,
                    [
                        'success' => $carry['success'] && $success,
                        'reasons' => array_merge($carry['reasons'], [$key => $success])
                    ]
                );
            }, ['success' => true, 'reasons' => []]);
        };

        $result = array_map($validation, $products);

        return $result;
    }

    public function testThat() {
        $customer = [
            'age'  => 12,
            'type' => 1,
        ];

        $products = [
            [
                'id'    => 1,
                'title' => 'book 1',
                'age'   => 18,
                'type'  => 1,
            ],
            [
                'id'    => 2,
                'title' => 'book 2',
                'age'   => 12,
                'type'  => 1,
            ],
        ];

        $result = $this->canCustomerBuyProducts_8($customer, $products);

        var_dump($result);
    }

}
