# TestAPI Laravel Docker

Laravel 12 project running with PHP 8.2 FPM, MySQL 8, Redis and Nginx.

## Запуск 

```
cd TestAPI
docker compose up -d --build
docker exec testapi_app php artisan migrate --seed
```

Проверка работоспособности

```
docker exec testapi_app php artisan test
```

## Контейнеры

- App: `testapi_app`
- Nginx: `testapi_nginx`, exposed on `http://localhost:8080`
- MySQL: `testapi_mysql`, exposed on `localhost:3306`
- Redis: `testapi_redis`, exposed on `localhost:6379`

Database credentials:

- Database: `testapi`
- User: `testapi`
- Password: `secret`

## Примеры запросов

```
Список слотов:
curl --location 'http://localhost:8080/slots/availability

Создание холда для слота #1:
curl -i -X POST "http://localhost:8080/slots/1/hold" \
  -H "Accept: application/json" \
  -H "Idempotency-Key: 018fd7db-8fc5-7de6-8d4d-43cf3a9671f9"
    
Подтверждение холда #1:

curl -i -X POST "http://localhost:8080/holds/1/confirm" \
  -H "Accept: application/json"
  
Отмена холда #1:

curl -i -X DELETE "http://localhost:8080/holds/1" \
  -H "Accept: application/json"
  
```