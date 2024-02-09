<?php

declare(strict_types=1);

namespace App\Services;

class PaymentGatewayService
{
    public function __construct()
    {
        echo "PaymentgatewayService. <br />";
    }

    public function charge(float $amount, float $tax, array $customer): bool
    {
        echo "Processing payment of $amount for customer " . $customer['name'] . " with tax of $tax. <br />";

        return true;
    }
}
