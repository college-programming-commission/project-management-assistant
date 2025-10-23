# 🚀 Deployment Checklist - MinIO CORS Fix

## Швидкий старт

### На локальній машині:
```bash
# 1. Commit & push зміни
git add .
git commit -m "Fix: MinIO CORS and Livewire S3 configuration"
git push origin master
```

### На production сервері:
```bash
# 2. Завантажити зміни
cd /path/to/project
git pull origin master

# 3. Оновити .env файл
cp prod.env .env

# 4. Зробити init-minio.sh виконуваним
chmod +x init-minio.sh

# 5. Перезапустити контейнери
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d

# 6. Перевірити логи
docker logs project-management-minio-init
docker logs project-management-app
docker logs project-management-minio

# 7. Перевірити статус контейнерів
docker ps
```

## Змінені файли:

### ✅ Створені нові файли:
- `init-minio.sh` - скрипт автоматичного налаштування CORS для MinIO
- `MINIO_CORS_FIX.md` - докладна документація
- `DEPLOYMENT_CHECKLIST.md` - цей файл

### ✅ Оновлені файли:
- `docker-compose.prod.yml` - додано сервіс `minio-init`
- `config/livewire.php` - виправлено S3 endpoint для temporary uploads
- `prod.env` - додано `LIVEWIRE_S3_ENDPOINT` і виправлено `AWS_ENDPOINT`
- `.env` (локальний) - додано `LIVEWIRE_S3_ENDPOINT` для розробки

## Критичні зміни в prod.env:

```env
# Laravel використовує внутрішній Docker URL
AWS_ENDPOINT=http://minio:9000

# Публічний URL для постійних посилань
AWS_URL=https://s3-kafedra.phfk.college/local

# Публічний URL для Livewire temporary uploads
LIVEWIRE_S3_ENDPOINT=https://s3-kafedra.phfk.college
```

## Перевірка після деплою:

1. **Перевірити MinIO initialization:**
   ```bash
   docker logs project-management-minio-init
   ```
   Має показати:
   ```
   ✅ MinIO CORS configuration completed successfully!
   Bucket: local
   CORS: Enabled for all origins (*)
   Public read: Enabled
   ```

2. **Перевірити CORS policy:**
   ```bash
   curl -I https://s3-kafedra.phfk.college/local/
   ```

3. **Тест завантаження файлу:**
   - Відкрити https://kafedra.phfk.college
   - Спробувати завантажити файл через Livewire
   - Перевірити DevTools → Network → немає CORS помилок

## Troubleshooting:

### ❌ minio-init зависає на "Waiting for MinIO to be ready":
```bash
# Перевірити MinIO контейнер
docker logs project-management-minio

# Перевірити мережу
docker exec project-management-minio-init ping -c 3 minio

# Вручну виконати налаштування
docker exec -it project-management-minio mc alias set myminio http://localhost:9000 minioadmin YOUR_PASSWORD
docker exec -it project-management-minio mc mb myminio/local --ignore-existing
docker exec -it project-management-minio mc cors set /tmp/cors.json myminio/local
```

### ❌ CORS помилка все ще є:
```bash
# Перезапустити minio-init
docker restart project-management-minio-init
docker logs -f project-management-minio-init
```

### ❌ Файли не завантажуються:
```bash
# Перевірити Laravel filesystem config
docker exec -it project-management-app php artisan tinker
>>> config('filesystems.disks.s3')
>>> config('livewire.temporary_file_upload.s3')
```

### ❌ MinIO недоступний:
```bash
# Перевірити MinIO health
curl http://192.168.1.104:9005/minio/health/live

# Перевірити cloudflared tunnel
curl https://s3-kafedra.phfk.college/minio/health/live
```

## Rollback план:

Якщо щось пішло не так:
```bash
# 1. Повернутися до попередньої версії
git log --oneline -5
git checkout <previous-commit>

# 2. Перезапустити контейнери
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d

# 3. Або відновити старі env змінні
# AWS_ENDPOINT=https://s3-kafedra.phfk.college
```

## 📞 Підтримка:

Детальна документація: `MINIO_CORS_FIX.md`

Архітектура системи і URL потоки детально описані в розділі "Архітектура URL і Потоки даних" документації.
