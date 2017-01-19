<?php

use ACME\Entity\Customer;
use ACME\Entity\Product;

/**
 * (Practical PHP Refactoring: Replace Nested Conditionals with Guard Clauses)
 * @see https://dzone.com/articles/practical-php-refactoring-24
 * 
 * Class Test1
 */
class TestOriginal extends PHPUnit_Framework_TestCase {

    public function imperativeCanCustomerBuyProducts($customer, $products) {
        $result = [];
        foreach ($products as $product) {
            $validateAge = $customer['age'] >= $product['age'];
            $validateType = $customer['type'] == $product['type'];

            $validation = $validateAge && $validateType;

            $reason = 'ok';
            if (!$validateAge) { $reason = 'age'; }
            if (!$validateType) { $reason = 'type'; }

            $result[$product['title']] = ['reason' => $reason, 'valid' => $validation];
        }
        return $result;
    }

    /**
     * @param Customer $customer
     * @param Product[] $products
     * @return array
     */
    public function declarativeCanCustomerBuyProducts($customer, $products) {
        $rules = [
//            'x' => {$product => $customer->getAge() >= $product->getAge()},
            
            'age'  => function (Product $product) use ($customer) {
                return $customer->getAge() >= $product->getAge();
            },
            'type' => function (Product $product) use ($customer) {
                return $customer->getType() == $product->getType();
            },
            'complex' => function(Product $product) use ($customer) {
                return strpos($customer->getName(), $product->getTitle()) != -1;
            }
        ];

        // short circuting
        $validation = function ($product) use ($rules) {
            foreach ($rules as $name => $rule) {
                if (!$rule($product)) return ['reason' => $name, 'valid' => false];
            }
            return ['reason' => 'ok', 'valid' => true];
        };
        $validation = function ($product) use ($rules) {
            $result = [];
            $valid = true;
            foreach ($rules as $name => $rule) {
                $ruleValue = $rule($product);
                $result[$name] = $ruleValue;
                $valid = $valid && $ruleValue;
            }
            return ['cansell'=> $valid, 'rules' => $result];
        };

        $values = array_map($validation, $products);
        $keys = array_map(function($p) { /** @var Product $p */ return $p->getTitle(); }, $products);
        return array_combine($keys, $values);
    }

    public function testMe() {
        $customer = (new Customer('Borna'))
            ->setAge(10)
            ->setType(1)
        ;

        $products = [
            (new Product('book 1 Borna'))->setAge(19)->setType(1),
            (new Product('book 2'))->setAge(9)->setType(1),
        ];

//        $imperative = $this->imperativeCanCustomerBuyProducts($customer, $products);
        $declarative = $this->declarativeCanCustomerBuyProducts($customer, $products);

//        var_dump($imperative);
        var_dump($declarative);
//        $this->assertTrue((bool) $imperative, 'Nope imperative');
//        $this->assertTrue((bool) $declarative, 'Nope declarative');
    }

}
