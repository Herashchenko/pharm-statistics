# Farm Statistics

Тестове завдання. Стек: PHP 8.2, Yii2 Basic, MongoDB, Elasticsearch.

## Розгортання

### 1. Піднімаємо контейнери

```bash
docker compose up -d --build
```

### 2. Встановлюємо Yii2 Basic

```bash
# Створюємо проект у тимчасову папку (бо робоча директорія не порожня)
docker compose exec php composer create-project --prefer-dist yiisoft/yii2-app-basic /tmp/yii2-basic

# Копіюємо файли в робочу директорію
docker compose exec php bash -c "cp -rn /tmp/yii2-basic/. /app/ && rm -rf /tmp/yii2-basic"
```

### 3. Встановлюємо пакети MongoDB та Elasticsearch

```bash
docker compose exec php composer require yiisoft/yii2-mongodb
docker compose exec php composer require yiisoft/yii2-elasticsearch
```

### 4. Конфігурація підключень

В `config/web.php` та `config/console.php` додати в масив `'components'`:

```php
'mongodb' => [
    'class' => \yii\mongodb\Connection::class,
    'dsn' => 'mongodb://mongodb:27017/farm_statistics',
],
'elasticsearch' => [
    'class' => \yii\elasticsearch\Connection::class,
    'nodes' => [
        ['http_address' => 'elasticsearch:9200'],
    ],
],
```

Хости `mongodb` та `elasticsearch` — це імена сервісів з `docker compose.yml`. Docker DNS резолвить їх автоматично всередині мережі.

### 5. Перевірка

```bash
curl http://localhost:8080                                # Yii2 welcome page
curl http://localhost:9200                                # Elasticsearch
docker compose exec mongodb mongosh --eval "db.stats()"  # MongoDB
```

## Сервіси

| Сервіс        | Порт  | Призначення                  |
|---------------|-------|------------------------------|
| nginx         | 8080  | Веб-сервер                   |
| php-fpm       | 9000  | PHP (внутрішній)             |
| mongodb       | 27017 | База даних                   |
| elasticsearch | 9200  | Пошуковий двигун             |
