<?php

namespace ACME\Entity;

class Product {
    /** @var string */ private $title;
    /** @var int    */ private $age;
    /** @var int    */ private $type;

    /**
     * Product constructor.
     * @param string|null $title
     */
    public function __construct($title = null) {
        $this->setTitle($title);
    }

    public function __toString() {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Product
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge() {
        return $this->age;
    }

    /**
     * @param int $age
     * @return Product
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
     * @return Product
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

}