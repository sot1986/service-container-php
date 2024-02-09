<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\InvoiceService;

class TestController
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected Request $request,
    ) {
        $this->request->count = $request->count + 5;
    }

    public function index()
    {
        $check = $this->invoiceService->createInvoice(100, [
            'name' => 'Matteo',
            'email' => 'matteo@email.it'
        ]);

        $this->request->echoUrl();

        $this->request->echoCount();

        if ($check) {
            echo 'Invoice created successfully <br />';
        } else {
            echo 'Invoice failed <br />';
        }

        return true;
    }
}
