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
