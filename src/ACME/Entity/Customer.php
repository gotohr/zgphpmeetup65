<?php

namespace ACME\Entity;

class Customer {
    /** @var int    */ private $age;
    /** @var int    */ private $type;
    /** @var string */ private $name;

    /**
     * Customer constructor.
     * @param string|null $name
     */
    public function __construct($name = null) {
        $this->setName($name);
    }

    /**
     * @return int
     */
    public function getAge() {
        return $this->age;
    }

    /**
     * @param int $age
     * @return Customer
     */
    public function setAge($age) {
        $this->age = $age;
        return $this;
    }

    /**
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Customer
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Customer
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

}