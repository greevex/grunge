# Grunge framework (PHP5.4+)
Grunge framework is a lightweight framework to build console applications on PHP5.4+

Published under MIT license.

## Simple application
```php
<?php
class my_app
extends \grunge\system\core\consoleApplication
{
    public function handle()
    {
        $name = $this->request->getString('name');
        $this->output->writeLn("Hello {$name}!");
    }
}

$my_app = new my_app();
$my_app->run();
````

## Highlights
This framework is written on PHP5.4.4 and all constructions that
was included in framework was tested on speed, memory usage etc.