import os
import sys
import requests
import time
from pathlib import Path
from io import BytesIO
from django.core.files import File

# Настройка Django
sys.path.insert(0, str(Path(__file__).parent))
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'config.settings')

import django
django.setup()

from catalog.models import Cocktail

# Настройки
API_BASE = "https://www.thecocktaildb.com/api/json/v1/1"
DOWNLOAD_IMAGES = True
SAVE_DIR = Path("media/cocktaildb")

def download_image(url, filename):
    """Скачать изображение коктейля"""
    try:
        response = requests.get(url, timeout=15)
        response.raise_for_status()
        
        SAVE_DIR.mkdir(parents=True, exist_ok=True)
        filepath = SAVE_DIR / filename
        
        with open(filepath, 'wb') as f:
            f.write(response.content)
        
        return filepath
    except Exception as e:
        print(f"⚠️ Не удалось скачать фото: {e}")
        return None

def parse_ingredients(drink):
    """Собрать ингредиенты в строку"""
    ingredients = []
    for i in range(1, 16):  # API возвращает до 15 ингредиентов
        ingredient = drink.get(f'strIngredient{i}')
        measure = drink.get(f'strMeasure{i}')
        if ingredient and ingredient.strip():
            if measure and measure.strip():
                ingredients.append(f"{measure.strip()} {ingredient.strip()}")
            else:
                ingredients.append(ingredient.strip())
    return '\n'.join(ingredients) if ingredients else "Состав не указан"

def import_cocktails(category="Ordinary_Drink", limit=200):
    """Импорт коктейлей из TheCocktailDB"""
    print(f"🍸 Импорт коктейлей из TheCocktailDB...")
    print(f"📂 Категория: {category} | Лимит: {limit}")
    
    # 1. Получаем список коктейлей
    url = f"{API_BASE}/filter.php?c={category}"
    try:
        response = requests.get(url, timeout=15)
        response.raise_for_status()
        data = response.json()
    except Exception as e:
        print(f"❌ Ошибка загрузки списка: {e}")
        return
    
    drinks = data.get('drinks', [])[:limit]
    print(f"✅ Найдено {len(drinks)} коктейлей")
    
    imported = 0
    skipped = 0
    
    for i, drink in enumerate(drinks, 1):
        name = drink['strDrink']
        
        # Пропускаем дубликаты
        if Cocktail.objects.filter(name__iexact=name).exists():
            print(f"[{i}/{len(drinks)}] ⏭️ Уже есть: {name}")
            skipped += 1
            continue
        
        # Получаем детали коктейля
        detail_url = f"{API_BASE}/lookup.php?i={drink['idDrink']}"
        try:
            detail = requests.get(detail_url, timeout=15).json()
            d = detail['drinks'][0]
        except:
            print(f"[{i}/{len(drinks)}] ❌ Не удалось загрузить детали: {name}")
            continue
        
        # Парсим данные
        composition = parse_ingredients(d)
        image_url = d.get('strDrinkThumb')
        alcoholic = d.get('strAlcoholic', '').lower()
        
        # Определяем тип и крепость
        is_alco = 'alcoholic' in alcoholic
        strength = "12-20%" if is_alco else ""
        cocktail_type = "alco" if is_alco else "non_alco"
        
        # Скачиваем фото
        image_file = None
        if DOWNLOAD_IMAGES and image_url:
            filename = f"cocktail_{i}_{name[:30].replace(' ', '_')}.jpg"
            image_path = download_image(image_url, filename)
            if image_path:
                image_file = open(image_path, 'rb')
        
        # Создаём запись в БД
        try:
            cocktail = Cocktail.objects.create(
                name=name,
                composition=composition,
                strength=strength,
                type=cocktail_type,
                price=450,  # Можно изменить
                is_active=True,
                order=i
            )
            
            if image_file:
                cocktail.image.save(filename, File(image_file), save=True)
                image_file.close()
            
            print(f"[{i}/{len(drinks)}] ✅ {name}")
            imported += 1
            
        except Exception as e:
            print(f"[{i}/{len(drinks)}] ❌ Ошибка сохранения: {e}")
            if image_file:
                image_file.close()
            skipped += 1
        
        time.sleep(0.5)  # Небольшая пауза
    
    # Итоги
    print("\n" + "="*50)
    print(f"🎉 Импорт завершён!")
    print(f"✅ Импортировано: {imported}")
    print(f"⏭️  Пропущено: {skipped}")
    print(f"📁 Фото сохранены в: {SAVE_DIR}")
    print("="*50)

if __name__ == '__main__':
    # Категории: Ordinary_Drink, Cocoa, Coffee, Milk / Egg / Float, 
    #            Other / Unknown, Punch / Party Drink, Soft Drink
    import_cocktails(category="Ordinary_Drink", limit=900)