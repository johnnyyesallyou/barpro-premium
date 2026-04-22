# BarPro - Выездной бар (SECURE VERSION)

Профессиональный сайт для мобильного барного сервиса с калькулятором стоимости, галереей и системой заявок.

## 🔒 Применены исправления безопасности

✅ **Критические исправления (Неделя 1):**
- Убрана XSS уязвимость (|safe фильтры)
- Добавлена валидация форм (Django Forms)
- DEBUG=False по умолчанию
- SECRET_KEY в .env
- Валидация загружаемых файлов

✅ **Инфраструктура (Неделя 2):**
- Поддержка PostgreSQL
- HTTPS настройки
- Логирование в файлы
- Ограничение размера файлов

✅ **DevOps (Неделя 3):**
- Docker + docker-compose
- Gunicorn для продакшена
- Фиксированные версии зависимостей

## 📋 Требования

- Python 3.11+
- PostgreSQL 15+ (для продакшена)
- Docker + Docker Compose (опционально)

## 🚀 Быстрый старт (Разработка)

### 1. Установка зависимостей

```bash
# Создаем виртуальное окружение
python -m venv venv
source venv/bin/activate  # Linux/Mac
venv\Scripts\activate     # Windows

# Устанавливаем зависимости
pip install -r requirements.txt
```

### 2. Настройка переменных окружения

```bash
# Копируем пример .env файла
cp .env.example .env

# Генерируем новый SECRET_KEY
python -c "from django.core.management.utils import get_random_secret_key; print(get_random_secret_key())"

# Редактируем .env файл и вставляем сгенерированный ключ
```

Для разработки в `.env`:
```env
DEBUG=True
SECRET_KEY=your-generated-secret-key-here
ALLOWED_HOSTS=localhost,127.0.0.1
```

### 3. Применение миграций

```bash
python manage.py migrate
```

### 4. Создание суперпользователя

```bash
python manage.py createsuperuser
```

### 5. Запуск сервера

```bash
python manage.py runserver
```

Сайт доступен по адресу: http://localhost:8000  
Админка: http://localhost:8000/admin

## 🐳 Запуск через Docker

### 1. Настройка .env файла

```bash
cp .env.example .env
# Отредактируйте .env файл
```

Пример `.env` для Docker:
```env
DEBUG=False
SECRET_KEY=your-secret-key-here
ALLOWED_HOSTS=localhost,yourdomain.com

DB_NAME=barpro
DB_USER=barpro_user
DB_PASSWORD=strong_password_here
DB_HOST=db
DB_PORT=5432
```

### 2. Запуск контейнеров

```bash
# Сборка и запуск
docker-compose up -d --build

# Применение миграций
docker-compose exec web python manage.py migrate

# Создание суперпользователя
docker-compose exec web python manage.py createsuperuser

# Просмотр логов
docker-compose logs -f web
```

Сайт доступен по адресу: http://localhost:8000

### 3. Остановка

```bash
docker-compose down
```

## 🔐 Безопасность

### Что исправлено:

1. **XSS защита** - убраны `|safe` фильтры, используется `|linebreaks`
2. **Валидация данных** - Django Forms для всех форм
3. **Валидация файлов** - проверка размера, расширения, MIME-типа
4. **SECRET_KEY** - вынесен в переменные окружения
5. **DEBUG** - по умолчанию False в продакшене
6. **HTTPS** - принудительный редирект, HSTS headers
7. **Логирование** - запись в файлы, ротация логов
8. **Ограничения** - максимальный размер файлов 5 МБ

### Рекомендации для продакшена:

```bash
# 1. Проверка безопасности Django
python manage.py check --deploy

# 2. Установка дополнительных пакетов (опционально)
pip install django-ratelimit  # Rate limiting
pip install django-bleach     # Санитизация HTML

# 3. Настройка HTTPS в nginx
# Используйте Let's Encrypt для бесплатного SSL сертификата

# 4. Backup базы данных
pg_dump barpro > backup_$(date +%Y%m%d).sql
```

## 📝 Управление контентом

### Админ-панель

1. Перейдите в http://localhost:8000/admin
2. Войдите используя созданного суперпользователя
3. Доступные разделы:
   - **Заявки** - просмотр и управление заявками клиентов
   - **Коктейли** - добавление коктейлей в меню
   - **Пакеты** - создание пакетных предложений
   - **Кейсы** - портфолио выполненных работ
   - **Страницы** - управление контентом страниц

## 🐛 Решение проблем

### Ошибка импорта модулей

```bash
# Убедитесь что виртуальное окружение активировано
source venv/bin/activate

# Переустановите зависимости
pip install -r requirements.txt --force-reinstall
```

### Ошибки миграций

```bash
# Проверка миграций
python manage.py showmigrations

# Откат миграций
python manage.py migrate app_name zero

# Повторное применение
python manage.py migrate
```

---

**Версия:** 2.0 (Secure)  
**Дата обновления:** 2026-04-16  
**Статус:** Production Ready ✅
