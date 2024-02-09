<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Request;

class InvoiceService
{
    public function __construct(
        protected SalesTaxService $salesTaxService,
        protected EmailService $emailService,
        protected PaymentGatewayService $paymentGatewayService,
        protected Request $request
    ) {
        echo "InvoiceService created. <br />";
        $this->request->count = 10;
        $this->request->echoCount();
    }

    public function createInvoice(
        float $amount,
        array $customer
    ): bool {
        // 1. Calculate the sales tax
        $tax = $this->salesTaxService->calculateSalesTax($amount);

        // 2. process invoice
        if (!$this->paymentGatewayService->charge($amount, $tax, $customer)) {
            return false;
        }

        // 3. Send invoice to customer
        $this->emailService->sendEmail($customer['email'], 'Invoice', "You have been invoiced for $amount");

        echo "Invoice created. <br />";

        $this->request->echoUrl();

        $this->request->echoCount();

        return true;
    }
}
