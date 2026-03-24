# Pharm Statistics

Тестове завдання. Стек: PHP 8.2, Yii2 Basic, MongoDB, Elasticsearch.

## Розгортання

```bash
git clone https://github.com/Herashchenko/pharm-statistics.git && cd pharm-statistics

# Піднімаємо контейнери
docker compose up -d --build

# Встановлюємо PHP-залежності
docker compose exec php composer install
```

### Перевірка

```bash
curl http://localhost:8080                                # Yii2 welcome page
curl http://localhost:9200                                # Elasticsearch
docker compose exec mongodb mongosh --eval "db.stats()"  # MongoDB
```

## Імпорт даних з Excel

Покладіть `.xls` файл у директорію `data/` та запустіть команду:

```bash
docker compose exec php php yii import /app/data/your-file.xls
```

Команда читає файл чанками, пропускає заголовки і записує дані в колекцію `pharm_statistics` батчами по 500 документів.

Перевірка результату:

```bash
docker compose exec mongodb mongosh pharm_statistics --eval "db.pharm_statistics.countDocuments()"
```

## Сервіси

| Сервіс        | Порт  | Призначення                  |
|---------------|-------|------------------------------|
| nginx         | 8080  | Веб-сервер                   |
| php-fpm       | 9000  | PHP (внутрішній)             |
| mongodb       | 27017 | База даних                   |
| elasticsearch | 9200  | Пошуковий двигун             |
