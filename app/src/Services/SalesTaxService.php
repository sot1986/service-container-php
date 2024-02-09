<?php

declare(strict_types=1);

namespace App\Services;

class SalesTaxService
{
    public function __construct()
    {
        echo "SalesTaxService. <br />";
    }

    public function calculateSalesTax(float $amount): float
    {
        return $amount * 0.10;
    }
}
