import os
import sys
import requests
from bs4 import BeautifulSoup
from pathlib import Path
import time
from django.core.files import File

# Настройка Django
sys.path.insert(0, str(Path(__file__).parent))
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'config.settings')

import django
django.setup()

from catalog.models import Cocktail

# Настройки
BASE_URL = "https://ru.inshaker.com"
COLLECTION_URL = "https://bar-company.ru/category/cocktails/"
DOWNLOAD_IMAGES = True
SAVE_DIR = Path("media/inshaker_cocktails")

def get_soup(url):
    """Получить BeautifulSoup объект"""
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language': 'ru-RU,ru;q=0.9,en;q=0.8',
    }
    try:
        response = requests.get(url, headers=headers, timeout=15)
        response.raise_for_status()
        return BeautifulSoup(response.text, 'html.parser')
    except Exception as e:
        print(f"❌ Ошибка загрузки {url}: {e}")
        return None

def download_image(url, filename):
    """Скачать изображение"""
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

def parse_cocktail_detail(url):
    """Распарсить страницу коктейля"""
    try:
        soup = get_soup(url)
        if not soup:
            return None
        
        # Название
        name_tag = soup.find('h1', class_='collection-product__title')
        name = name_tag.text.strip() if name_tag else "Unknown"
        
        # Изображение (высокого качества)
        image_url = None
        img = soup.find('img', class_='collection-product__image')
        if img and img.get('src'):
            image_url = img['src']
            # Если относительный URL
            if image_url.startswith('/'):
                image_url = BASE_URL + image_url
        
        # Ингредиенты
        ingredients = []
        ingredients_list = soup.find('ul', class_='ingredients__list')
        if ingredients_list:
            for li in ingredients_list.find_all('li', class_='ingredient'):
                name_span = li.find('span', class_='ingredient__name')
                amount_span = li.find('span', class_='ingredient__amount')
                
                ingredient_text = ""
                if amount_span:
                    ingredient_text += amount_span.text.strip() + " "
                if name_span:
                    ingredient_text += name_span.text.strip()
                
                if ingredient_text:
                    ingredients.append(ingredient_text)
        
        # Описание/приготовление
        description = ""
        desc_tag = soup.find('div', class_='collection-product__description')
        if desc_tag:
            description = desc_tag.text.strip()
        
        # Категория/теги
        tags = []
        tag_links = soup.find_all('a', class_='tag')
        for tag in tag_links:
            tags.append(tag.text.strip())
        
        # Определяем тип (алкогольный/безалкогольный)
        cocktail_type = "alco"
        if any(word in name.lower() for word in ['безалкогольный', 'mocktail', 'virgin']):
            cocktail_type = "non_alco"
        
        # Крепость (примерно)
        strength = ""
        if cocktail_type == "alco":
            strength = "15-25%"
        
        return {
            'name': name,
            'ingredients': '\n'.join(ingredients) if ingredients else "Состав не указан",
            'description': description,
            'image_url': image_url,
            'tags': ', '.join(tags),
            'strength': strength,
            'type': cocktail_type
        }
        
    except Exception as e:
        print(f"❌ Ошибка при парсинге {url}: {e}")
        return None

def import_cocktails():
    """Основная функция импорта"""
    print("🍸 Начинаем импорт коктейлей с InShaker...")
    print(f"📥 Коллекция: {COLLECTION_URL}")
    
    soup = get_soup(COLLECTION_URL)
    if not soup:
        print("❌ Не удалось загрузить страницу коллекции")
        return
    
    # Поиск ссылок на коктейли
    cocktail_links = []
    
    # Ищем все карточки коктейлей
    cocktail_cards = soup.find_all('a', class_='collection-product')
    
    for card in cocktail_cards:
        href = card.get('href')
        if href:
            full_url = BASE_URL + href if href.startswith('/') else href
            if full_url not in cocktail_links:
                cocktail_links.append(full_url)
    
    print(f"✅ Найдено {len(cocktail_links)} коктейлей")
    
    imported_count = 0
    skipped_count = 0
    
    # Ограничение для теста (уберите [:20] для импорта всех)
    for i, url in enumerate(cocktail_links[:20], 1):
        print(f"\n[{i}/{len(cocktail_links)}] Обработка: {url}")
        
        data = parse_cocktail_detail(url)
        if not data:
            print(f"  ⏭️ Пропущен (не удалось распарсить)")
            skipped_count += 1
            continue
        
        # Проверка на дубликаты
        if Cocktail.objects.filter(name__iexact=data['name']).exists():
            print(f"  ⏭️ Уже существует: {data['name']}")
            skipped_count += 1
            continue
        
        # Скачиваем изображение
        image_file = None
        if DOWNLOAD_IMAGES and data['image_url']:
            filename = f"cocktail_{i}_{data['name'][:30].replace(' ', '_')}.jpg"
            image_path = download_image(data['image_url'], filename)
            if image_path:
                image_file = open(image_path, 'rb')
        
        # Создаём коктейль в БД
        try:
            cocktail = Cocktail.objects.create(
                name=data['name'],
                composition=data['ingredients'] + ('\n\n' + data['description'] if data['description'] else ''),
                strength=data['strength'],
                type=data['type'],
                price=450,
                is_active=True,
                order=i
            )
            
            if image_file:
                cocktail.image.save(filename, File(image_file), save=True)
                image_file.close()
            
            print(f"  ✅ Импортирован: {data['name']}")
            imported_count += 1
            
        except Exception as e:
            print(f"  ❌ Ошибка сохранения: {e}")
            if image_file:
                image_file.close()
            skipped_count += 1
        
        time.sleep(1)  # Пауза чтобы не забанили
    
    # Итоги
    print("\n" + "="*50)
    print(f"🎉 Импорт завершён!")
    print(f"✅ Импортировано: {imported_count}")
    print(f"⏭️ Пропущено: {skipped_count}")
    print(f"📁 Фото сохранены в: {SAVE_DIR}")
    print("="*50)

if __name__ == '__main__':
    import_cocktails()