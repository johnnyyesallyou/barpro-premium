@echo off
REM Скрипт для загрузки премиум данных BarPro (Windows)

echo.
echo ============================================
echo   BarPro Premium - Загрузка данных
echo ============================================
echo.

REM Проверка manage.py
if not exist manage.py (
    echo [ERROR] Файл manage.py не найден
    echo Запустите скрипт из корневой директории проекта
    pause
    exit /b 1
)

REM Применение миграций
echo [1/5] Применение миграций...
python manage.py migrate
if errorlevel 1 (
    echo [ERROR] Ошибка при применении миграций
    pause
    exit /b 1
)
echo [OK] Миграции применены
echo.

REM Загрузка коктейлей часть 1
echo [2/5] Загрузка коктейлей (часть 1: 25 алкогольных)...
python manage.py loaddata catalog/fixtures/cocktails_part1.json
if errorlevel 1 echo [WARNING] Не удалось загрузить cocktails_part1.json

REM Загрузка коктейлей часть 2
echo [3/5] Загрузка коктейлей (часть 2: 5 алко + 20 безалко)...
python manage.py loaddata catalog/fixtures/cocktails_part2.json
if errorlevel 1 echo [WARNING] Не удалось загрузить cocktails_part2.json

REM Загрузка команды и лояльности
echo [4/5] Загрузка команды и программы лояльности...
python manage.py loaddata catalog/fixtures/team_and_loyalty.json
if errorlevel 1 echo [WARNING] Не удалось загрузить team_and_loyalty.json

REM Загрузка дополнительных услуг
echo [5/5] Загрузка дополнительных услуг...
python manage.py loaddata catalog/fixtures/addon_services.json
if errorlevel 1 echo [WARNING] Не удалось загрузить addon_services.json

echo.
echo ============================================
echo   Готово! Премиум данные загружены:
echo ============================================
echo   * 50 коктейлей (30 алко + 20 безалко)
echo   * 8 членов команды
echo   * 5 уровней лояльности
echo   * 32 дополнительные услуги
echo   * 10 тематических баров
echo   * 14 шоу-программ
echo.
echo BarPro Premium готов к работе!
echo.
echo Следующие шаги:
echo 1. python manage.py createsuperuser
echo 2. python manage.py runserver
echo 3. Откройте http://localhost:8000
echo.
pause
