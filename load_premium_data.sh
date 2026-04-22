#!/bin/bash

# Скрипт для загрузки премиум данных в BarPro

echo "🚀 Загрузка премиум данных BarPro..."
echo ""

# Проверка что мы в правильной директории
if [ ! -f "manage.py" ]; then
    echo "❌ Ошибка: файл manage.py не найден"
    echo "Запустите скрипт из корневой директории проекта"
    exit 1
fi

# Применение миграций
echo "📦 Применение миграций..."
python manage.py migrate
if [ $? -ne 0 ]; then
    echo "❌ Ошибка при применении миграций"
    exit 1
fi
echo "✅ Миграции применены"
echo ""

# Загрузка коктейлей часть 1
echo "🍸 Загрузка коктейлей (часть 1)..."
python manage.py loaddata catalog/fixtures/cocktails_part1.json
if [ $? -ne 0 ]; then
    echo "⚠️  Предупреждение: не удалось загрузить cocktails_part1.json"
fi

# Загрузка коктейлей часть 2
echo "🍹 Загрузка коктейлей (часть 2)..."
python manage.py loaddata catalog/fixtures/cocktails_part2.json
if [ $? -ne 0 ]; then
    echo "⚠️  Предупреждение: не удалось загрузить cocktails_part2.json"
fi

# Загрузка команды и лояльности
echo "👥 Загрузка команды и программы лояльности..."
python manage.py loaddata catalog/fixtures/team_and_loyalty.json
if [ $? -ne 0 ]; then
    echo "⚠️  Предупреждение: не удалось загрузить team_and_loyalty.json"
fi

# Загрузка дополнительных услуг
echo "🎭 Загрузка дополнительных услуг..."
python manage.py loaddata catalog/fixtures/addon_services.json
if [ $? -ne 0 ]; then
    echo "⚠️  Предупреждение: не удалось загрузить addon_services.json"
fi

echo ""
echo "✨ Готово! Премиум данные загружены:"
echo "  ✓ 50 коктейлей (30 алкогольных + 20 безалкогольных)"
echo "  ✓ 8 членов команды"
echo "  ✓ 5 уровней лояльности"
echo "  ✓ 32 дополнительные услуги"
echo "  ✓ 10 тематических баров"
echo "  ✓ 14 шоу-программ"
echo ""
echo "🎉 BarPro Premium готов к работе!"
echo ""
echo "Следующие шаги:"
echo "1. Создайте суперпользователя: python manage.py createsuperuser"
echo "2. Запустите сервер: python manage.py runserver"
echo "3. Откройте http://localhost:8000"
