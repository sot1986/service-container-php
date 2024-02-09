<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\TestController;
use App\Exceptions\ContainerException;
use App\Exceptions\NotFoundException;
use App\Services\EmailService;
use App\Services\InvoiceService;
use App\Services\PaymentGatewayService;
use App\Services\SalesTaxService;

class App
{
    public static Container $container;

    public function __construct()
    {
        self::$container = new Container();

        self::$container->set(EmailService::class, function () {
            return new EmailService([
                'smtp' => 'smtp.example.com'
            ]);
        });

        self::$container->singleton(Request::class, function () {
            return new Request();
        });
    }

    public function run()
    {
        try {
            $controller = self::$container->get(TestController::class);
            return $controller->index();
        } catch (NotFoundException $e) {
            echo $e->getMessage();
            echo "<br />";
            echo "404 Not Found";
            echo "<br />";
        } catch (ContainerException $e) {
            echo "<br />";
            echo $e->getMessage();
            echo "<br />";
            echo "500 Internal Server Error";
        } catch (\Error $e) {
            echo "<br />";
            echo $e->getMessage();
            echo "<br />";
        } catch (\Throwable $e) {
            echo "<br />";
            echo $e->getMessage();
            echo "<br />";
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
