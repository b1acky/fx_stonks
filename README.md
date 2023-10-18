## Установка

```
composer install

cp .env.example .env

# заполнить .env
# создать БД

php bin/console doctrine:migrations:migrate
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

```
symfony server:start
```

Открыть в браузере http://127.0.0.1:8000/

## Тесты

```
# Создать тестовую базу :(
# Запустить все тесты

php bin/phpunit
```

