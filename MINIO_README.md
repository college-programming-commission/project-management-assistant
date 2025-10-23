# MinIO Configuration

## Архітектура

```
Laravel → http://minio:9000 (внутрішній Docker network)
Браузер → https://s3-kafedra.phfk.college (через cloudflared)
```

## Конфігурація (.env)

```env
# S3 Credentials
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=...
AWS_BUCKET=local

# Endpoints
AWS_ENDPOINT=http://minio:9000      # Внутрішній (швидкий)
AWS_URL=https://s3-kafedra.phfk.college/local  # Публічний

# MinIO
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=...
MINIO_CONSOLE_PORT=9006
MINIO_AUTO_SETUP=true  # Автоматично робить bucket публічним
```

## Cloudflared Routes

```yaml
# S3 API
- hostname: s3-kafedra.phfk.college
  service: http://192.168.1.104:9005

# Console (опціонально)
- hostname: minio-console.phfk.college
  service: http://192.168.1.104:9006
```

## Auto-Setup

`setup-minio.php` автоматично виконується при старті контейнера і:
- Створює bucket `local` (якщо не існує)
- Встановлює публічну bucket policy для всіх файлів
- Тестує доступ

Вимкнути: `MINIO_AUTO_SETUP=false`

## Публічні файли

Всі файли автоматично публічні без pre-signed URLs:
```
https://s3-kafedra.phfk.college/local/avatars/file.jpg
```

Якщо потрібні приватні файли - використовуйте:
```php
Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
```
