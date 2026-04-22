FROM python:3.11-slim

# Устанавливаем переменные окружения
ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1

WORKDIR /app

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    libpq-dev \
    gcc \
    && rm -rf /var/lib/apt/lists/*

# Копируем requirements и устанавливаем Python зависимости
COPY requirements.txt .
RUN pip install --upgrade pip && \
    pip install -r requirements.txt

# Копируем проект
COPY . .

# Создаем директории
RUN mkdir -p logs staticfiles media

# Собираем статику
RUN python manage.py collectstatic --noinput || true

# Копируем и настраиваем entrypoint
COPY entrypoint.sh /app/entrypoint.sh
RUN chmod +x /app/entrypoint.sh

# Создаем пользователя для запуска приложения
RUN useradd -m -u 1000 appuser && \
    chown -R appuser:appuser /app
USER appuser

EXPOSE 8000

# Команда запуска: выполняем миграции, затем запускаем Gunicorn
CMD ["/app/entrypoint.sh"]
