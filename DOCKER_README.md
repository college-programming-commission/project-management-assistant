# Docker Deployment Guide

## 🚀 Швидкий старт

```bash
# 1. Клонувати репозиторій
git clone <repository-url>
cd project-management-assistant

# 2. Скопіювати .env файл
cp .env.example .env

# 3. Налаштувати .env (змінити паролі!)
nano .env

# 4. Запустити Docker
docker-compose up -d --build

# 5. Перевірити статус
docker-compose ps
```

## 📋 Вимоги

- Docker Engine 20.10+
- Docker Compose 2.0+
- Мінімум 2GB RAM
- Мінімум 5GB вільного місця на диску

## 🔧 Налаштування

### Обов'язкові змінні .env

```env
# Основні налаштування
APP_NAME="Платформа управління дослідницькою діяльністю"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8080

# База даних PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=laravel
DB_PASSWORD=ЗМІНІТЬ_ЦЕЙПАРОЛЬ_НА_НАДІЙНИЙ

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# MinIO S3 Storage
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=ЗМІНІТЬ_ЦЕЙПАРОЛЬ_НА_НАДІЙНИЙ
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=ЗМІНІТЬ_ЦЕЙПАРОЛЬ_НА_НАДІЙНИЙ
AWS_ENDPOINT=http://minio:9000
AWS_BUCKET=local

# Google OAuth (опціонально)
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## 🔐 Безпека PostgreSQL

### Development (поточна конфігурація)
Порт 5432 опублікований для зручності розробки:
```bash
# Підключення ззовні
psql -h localhost -U laravel -d postgres -p 5432
```

### Production (рекомендації)
1. **Закоментуйте порти в docker-compose.yml:**
```yaml
db:
    # ports:
    #     - '5432:5432'  # Заборонити доступ ззовні
```

2. **Використовуйте надійні паролі:**
```bash
# Генерація надійного пароля
openssl rand -base64 32
```

3. **Обмежте мережу:**
```yaml
networks:
    project-management-network:
        internal: true  # Тільки внутрішній доступ
```

4. **Увімкніть SSL підключення:**
```env
DB_SSL_MODE=require
```

## 🏥 Health Checks

Всі сервіси мають health checks:

```bash
# Перевірити статус всіх сервісів
docker-compose ps

# Переглянути health check конкретного сервісу
docker inspect project-management-app | grep Health -A 10
```

## 📊 Моніторинг

### Перегляд логів
```bash
# Всі сервіси
docker-compose logs -f

# Конкретний сервіс
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Метрики контейнерів
```bash
# Використання ресурсів
docker stats

# Детальна інформація про контейнер
docker inspect project-management-app
```

## 🔄 Оновлення

```bash
# 1. Зупинити контейнери
docker-compose down

# 2. Оновити код
git pull

# 3. Перебудувати образи
docker-compose build --no-cache

# 4. Запустити
docker-compose up -d

# 5. Запустити міграції (якщо потрібно)
docker-compose exec app php artisan migrate --force
```

## 🗄️ Backup & Restore

### PostgreSQL Backup
```bash
# Створити backup
docker-compose exec db pg_dump -U laravel postgres > backup_$(date +%Y%m%d_%H%M%S).sql

# Відновити з backup
docker-compose exec -T db psql -U laravel postgres < backup_20250101_120000.sql
```

### MinIO Backup
```bash
# Backup всього bucket
docker run --rm --net project-management-network \
    -v $(pwd)/minio-backup:/backup \
    minio/mc:latest \
    mc mirror minio/local /backup
```

## 🧹 Очищення

```bash
# Зупинити та видалити контейнери
docker-compose down

# Видалити volumes (УВАГА: видаляє дані!)
docker-compose down -v

# Видалити images
docker rmi project-management-assistant-app:latest

# Повне очищення Docker
docker system prune -a --volumes
```

## 📦 Volumes

Проєкт використовує іменовані volumes:

- `project-management-db-data` - База даних PostgreSQL
- `project-management-public` - Публічні файли (CSS, JS, зображення)
- `project-management-storage` - Laravel storage (uploads, logs)
- `project-management-minio` - MinIO object storage

```bash
# Перегляд volumes
docker volume ls | grep project-management

# Інспекція volume
docker volume inspect project-management-db-data
```

## 🌐 Порти

За замовчуванням:

- **8080** - Nginx (веб-сервер)
- **8081** - Laravel Reverb (WebSocket)
- **5432** - PostgreSQL (тільки development!)
- **9002** - MinIO API
- **9003** - MinIO Console

Зміна портів:
```env
APP_PORT=8080
REVERB_PORT=8081
DB_PORT=5432
MINIO_PORT=9002
MINIO_CONSOLE_PORT=9003
```

## 👤 Адміністратор за замовчуванням

```
Email: it_commission_college@uzhnu.edu.ua
Пароль: 314tHeBest!
```

⚠️ **ВАЖЛИВО:** Змініть пароль після першого входу!

## 🐛 Troubleshooting

### Контейнер не запускається
```bash
# Перегляд детальних логів
docker-compose logs app --tail=100

# Перевірка конфігурації
docker-compose config
```

### База даних не підключається
```bash
# Перевірка health check
docker-compose exec db pg_isready -U laravel

# Тест підключення
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Повільна робота
```bash
# Перевірка ресурсів
docker stats

# Очистка кешу Laravel
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Помилки файлових прав
```bash
# Виправити права
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

## 📚 Корисні команди

```bash
# Запуск artisan команд
docker-compose exec app php artisan <command>

# Запуск composer
docker-compose exec app composer <command>

# Доступ до bash в контейнері
docker-compose exec app bash

# Доступ до PostgreSQL
docker-compose exec db psql -U laravel postgres

# Доступ до Redis CLI
docker-compose exec redis redis-cli
```

## 🔗 Посилання

- **Додаток:** http://localhost:8080
- **MinIO Console:** http://localhost:9003
- **PostgreSQL:** localhost:5432 (якщо порт опублікований)

## 📝 Best Practices

1. ✅ **Завжди використовуйте надійні паролі**
2. ✅ **Регулярно робіть backup бази даних**
3. ✅ **Моніторте логи та метрики**
4. ✅ **Оновлюйте Docker images**
5. ✅ **Обмежуйте доступ до портів в production**
6. ✅ **Використовуйте environment-specific .env файли**
7. ✅ **Налаштуйте автоматичні backup**
8. ✅ **Тестуйте відновлення з backup**

## 🆘 Підтримка

При виникненні проблем:
1. Перегляньте логи: `docker-compose logs -f`
2. Перевірте health checks: `docker-compose ps`
3. Зверніться до команди розробки

---

**Maintainer:** it_commission_college@uzhnu.edu.ua
