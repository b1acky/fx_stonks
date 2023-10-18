## Установка

```
cp .env.example .env

# заполнить .env

composer install

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
```

## Команды

```
# загрузить данные с coindesk
php bin/console app:currency-rates-import coindesk

# загрузить данные с ECB
php bin/console app:currency-rates-import ecb

# калькулятор
php bin/console app:currency-exchange-calc BTC JPY 0.01
```

## Запустить в браузере

С помощью Symfony CLI https://symfony.com/doc/current/setup/symfony_server.html

```
symfony server:start
```

Открыть в браузере http://127.0.0.1:8000/

## Тесты

```
# Создать тестовую базу
APP_ENV=test php bin/console doctrine:database:create
APP_ENV=test php bin/console doctrine:migrations:migrate -n 

# Запустить все тесты
php bin/phpunit
```

## Добавить новый источник данных

* Создать новый класс в неймспейсе `App\Stonks\Parser`:
```php
<?php

namespace App\Stonks\Parser;

use App\Entity\ExchangeRate;
use App\Stonks\Collection\ExchangeRatesCollection;

class NextParser extends AbstractParser
{
    public function parse(string $data): ExchangeRatesCollection
    {
        $collection = new ExchangeRatesCollection;
        
        // обработать $data
        // $rate = new ExchangeRate;
        // $rate->setBaseCurrency('');
        // <...>
        // $collection->add($rate);

        return $collection;
    }
}
```
* Добавить конфиг:
```yaml
  # config/services.yaml
  App\Stonks\Parser\NextParser:
    arguments:
      - '%env(NEXT_URL)%'
      - '%env(NEXT_TIMEOUT)%'
    tags:
      - { name: 'parser.source', key: 'next' }
```
* Добавить env-переменные
```
# .env
# .env.example
NEXT_URL=https://next/
NEXT_TIMEOUT=60
```
* Проверить
```
php bin/console app:currency-rates-import next
```