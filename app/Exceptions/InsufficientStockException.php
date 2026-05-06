<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public string $productName;
    public float $requested;
    public float $available;

    public function __construct(string $productName, float $requested, float $available, string $message = '')
    {
        $this->productName = $productName;
        $this->requested = $requested;
        $this->available = $available;

        $message = $message !== ''
            ? $message
            : "Insufficient stock for {$productName}. Requested {$requested}, available {$available}.";

        parent::__construct($message);
    }
}
