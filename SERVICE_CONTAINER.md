# Build a PHP-DI Service container

1. Install via composer PSR-11: Container Interface
2. 
   `composer require psr/container`

   Look on the interface what ask
   
   `Psr\Container\ContainerInterface`.
   
   There is no referce on how to implement set method

3. Start on implementing the has method
   Define the entries  

   ```php
    private array $entries = [];

    public function has($id)
    {
        return isset($this->entries[$id]);
    }
   ```

4. Create set method
   ```php
    public function set(string $id, callable $value)
    {
        $this->entries[$id] = $value;
    }
   ```

5. Start implementing the get method
   1. Throw exception if not found in entries
   2. Call the entry if it was found, passing the current container instance as argument (in order to allow recursive resolution)

6. Add the container to the App class as a static property
   ```php
    declare(strict_types=1);

    namespace App\Core;

    class App
    {
        public static Container $container;

        public function __construct()
        {
            static::$container = new Container();
        }

        public function run()
        {
            //
        }
    }
    ```

7. Start registering int the app container all the dependecies
   ```php
    public function __construct()
    {
        static::$container = new Container();

        static::$container->set(InvoiceService::class, function (Container $container) {
            return new InvoiceService(
                $container->get(SalesTaxService::class),
                $container->get(PaymentGatewayService::class),
                $container->get(EmailService::class),
            );
        });

        static::$container->set(SalesTaxService::class, function (Container $container) {
            return new SalesTaxService();
        });

        static::$container->set(PaymentGatewayService::class, function (Container $container) {
            return new PaymentGatewayService();
        });

        static::$container->set(EmailService::class, function (Container $container) {
            return new EmailService();
        });
    }
   ```
   We could avoid to register base dependencies as first improvement.
   Let's test.

8. Create a Test controller and launch from app.
   ```php
    public function __construct()
    {
        echo "TestController created";
    }

    public function index()
    {
        /** @var \App\Services\InvoiceService $service */
        $service = App::$container->get(InvoiceService::class);

        return $service->createInvoice(100, ['email' => 'matteo@email.com', 'name' => 'Matteo']);
    }
   ```

9. Start with Autowiring
   This allows to resolve classes without specific bindings.

   Revert logic in get
   ```php
    public function get($id)
        {
            if ($this->has($id)) {
                $entry = $this->entries[$id];

                return $entry($this); // passing the container instance as argument.

            }


            throw $this->resolve($id);
        }
   ```

10. Create a recursive resolver
    ```php
    public function resolve(string $id)
    {
        // 1. Inspect the class
        $class = new \ReflectionClass($id);

        // 2. Inspect the constructor
        $constructor = $class->getConstructor();

        if (!$constructor) {
            return new $id();
        }

        // 3. Inspect constructor parameters
        $parameters = $constructor->getParameters();

        if (empty($parameters)) {
            return new $id();
        }

        // 4. If the constructor parameter is a class, try resolve it using container
        $dependecies = array_map(
            function (\ReflectionParameter $param) use ($id) {
                $name = $param->getName();
                $type = $param->getType();

                if (!$type) {
                    throw new ContainerExeption("Unable to resolve dependency for class $id");
                }

                if ($type instanceof \ReflectionUnionType) {
                    throw new ContainerExeption("Unable to resolve dependency for class $id");
                }

                if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                    $type = $type->getName();

                    return $this->get($type);
                }

                throw new ContainerExeption("Unable to resolve dependency for class $id");
            },
            $parameters
        );

        return $class->newInstanceArgs($dependecies);
    }
    ```

