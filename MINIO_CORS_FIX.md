# Виправлення CORS для MinIO на Production

## Проблема
CORS помилка при завантаженні файлів через Livewire до MinIO S3:
```
Access to XMLHttpRequest at 'https://s3-kafedra.phfk.college/...' from origin 'https://kafedra.phfk.college' 
has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present
```

## Причини
1. **MINIO_API_CORS_ALLOW_ORIGIN** в docker-compose працює тільки для MinIO Console API, НЕ для S3 API
2. **AWS_ENDPOINT** в prod.env був налаштований неправильно (зовнішній URL замість внутрішнього)
3. CORS policy має бути налаштована безпосередньо на bucket через MinIO Client (mc)

## Рішення

### 1. Створено файли
- `init-minio.sh` - скрипт ініціалізації MinIO з налаштуванням CORS
- Оновлено `docker-compose.prod.yml` - додано сервіс `minio-init`
- Виправлено `prod.env` - змінено AWS_ENDPOINT на внутрішній URL

### 2. Інструкції для розгортання на сервері

#### Крок 1: Завантажте зміни на сервер
```bash
# На локальній машині
git add init-minio.sh docker-compose.prod.yml prod.env
git commit -m "Fix: MinIO CORS configuration for production"
git push origin master

# На сервері
cd /path/to/project
git pull origin master
```

#### Крок 2: Оновіть .env файл на сервері
```bash
# На сервері
cp prod.env .env
```

#### Крок 3: Перезапустіть контейнери
```bash
# Зупиніть контейнери
docker-compose -f docker-compose.prod.yml down

# Видаліть старий MinIO контейнер (якщо потрібно)
docker rm -f project-management-minio

# Запустіть контейнери заново
docker-compose -f docker-compose.prod.yml up -d

# Перевірте логи minio-init
docker logs project-management-minio-init
```

#### Крок 4: Перевірка
```bash
# Перевірте що всі контейнери запущені
docker ps

# Перевірте логи MinIO
docker logs project-management-minio

# Перевірте логи app контейнера
docker logs project-management-app
```

## Що було змінено

### prod.env
```diff
- AWS_ENDPOINT=https://s3-kafedra.phfk.college
+ AWS_ENDPOINT=http://minio:9000

- # AWS_TEMPORARY_URL_ENDPOINT=https://s3-kafedra.phfk.college
+ # Livewire temporary uploads - має бути публічний URL без bucket
+ LIVEWIRE_S3_ENDPOINT=https://s3-kafedra.phfk.college
```

**Пояснення:**
- `AWS_ENDPOINT=http://minio:9000` - внутрішній URL для Laravel → MinIO операцій (через Docker network)
- `AWS_URL=https://s3-kafedra.phfk.college/local` - публічний URL для генерації постійних посилань на файли
- `LIVEWIRE_S3_ENDPOINT=https://s3-kafedra.phfk.college` - публічний URL для Livewire pre-signed URLs (браузер клієнта завантажує файли напряму)

### docker-compose.prod.yml
Додано новий сервіс `minio-init`:
```yaml
minio-init:
    image: minio/mc:latest
    container_name: project-management-minio-init
    env_file:
        - .env
    volumes:
        - ./init-minio.sh:/init-minio.sh:ro
    entrypoint: /bin/sh /init-minio.sh
    networks:
        - project-management-network
    depends_on:
        minio:
            condition: service_healthy
    restart: on-failure
```

### config/livewire.php
Оновлено конфігурацію temporary file uploads:
```php
's3' => [
    'bucket' => env('AWS_BUCKET'),
    'endpoint' => env('LIVEWIRE_S3_ENDPOINT', env('AWS_ENDPOINT')),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'options' => [],
],
```

**Важливо:** Livewire генерує pre-signed URLs для прямого завантаження файлів з браузера клієнта до MinIO. Тому endpoint має бути публічним URL, а не внутрішнім Docker URL.

### init-minio.sh
Автоматично налаштовує:
- Створення bucket `local`
- CORS policy для всіх origins
- Дозволені методи: GET, HEAD, PUT, POST, DELETE
- Дозволені headers: всі (*)
- Public read доступ до bucket

## Архітектура URL і Потоки даних

### Схема комунікації:
```
┌─────────────────┐
│  Браузер        │
│  Клієнта        │
└────┬───────┬────┘
     │       │
     │       └─────────────────────────────────┐
     │                                         │
     ▼                                         ▼
┌──────────────────────────┐    ┌──────────────────────────┐
│ https://kafedra.         │    │ https://s3-kafedra.      │
│   phfk.college           │    │   phfk.college           │
│                          │    │                          │
│ (Laravel + Nginx)        │    │ (MinIO S3)               │
└──────┬───────────────────┘    └───────┬──────────────────┘
       │                                │
       │ cloudflared tunnel             │ cloudflared tunnel
       ▼                                ▼
┌──────────────────────────┐    ┌──────────────────────────┐
│ 192.168.1.104:8080       │    │ 192.168.1.104:9005       │
│                          │    │                          │
│ Docker Nginx             │    │ Docker MinIO             │
└──────┬───────────────────┘    └───────┬──────────────────┘
       │                                │
       │ Docker network                 │
       ▼                                │
┌──────────────────────────┐            │
│ app:9000                 │────────────┘
│ (Laravel PHP-FPM)        │  http://minio:9000
└──────────────────────────┘
```

### URL конфігурації:
1. **AWS_ENDPOINT** = `http://minio:9000`
   - Використовується Laravel для внутрішніх операцій (читання/запис файлів)
   - Недоступний з браузера клієнта

2. **AWS_URL** = `https://s3-kafedra.phfk.college/local`
   - Використовується для генерації постійних публічних посилань
   - Доступний з браузера через cloudflared

3. **LIVEWIRE_S3_ENDPOINT** = `https://s3-kafedra.phfk.college`
   - Використовується Livewire для генерації pre-signed URLs
   - Браузер робить PUT запити напряму до MinIO через цей URL

## Cloudflared Tunnel конфігурація
Переконайтеся що в `/etc/cloudflared/config.yml` на сервері є:
```yaml
# Основний сайт
- hostname: kafedra.phfk.college
  service: http://192.168.1.104:8080

# MinIO S3
- hostname: s3-kafedra.phfk.college
  service: http://192.168.1.104:9005  # MINIO_PORT=9005
```

## Тестування
1. Відкрийте https://kafedra.phfk.college
2. Спробуйте завантажити файл через Livewire
3. Перевірте в DevTools що немає CORS помилок
4. Файл має успішно завантажитися

## Troubleshooting

### CORS помилка досі присутня
```bash
# Перевірте логи minio-init
docker logs project-management-minio-init

# Вручну виконайте налаштування CORS
docker exec -it project-management-minio mc alias set myminio http://localhost:9000 minioadmin YOUR_PASSWORD
docker exec -it project-management-minio mc cors set --help
```

### MinIO не запускається
```bash
# Перевірте volumes
docker volume ls | grep minio

# Видаліть volume якщо потрібно (УВАГА: видалить всі файли!)
docker volume rm project-management-minio
```

### Файли не завантажуються
```bash
# Перевірте Laravel логи
docker exec -it project-management-app tail -f storage/logs/laravel.log

# Перевірте чи правильно налаштований AWS_ENDPOINT
docker exec -it project-management-app php artisan tinker
>>> config('filesystems.disks.s3.endpoint')
```
