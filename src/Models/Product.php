<?php

namespace App\Models;

class Product
{
    public function __construct(private string $code, private string $name, private string $price)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): string
    {
        return $this->price;
    }
}
