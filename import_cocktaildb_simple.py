import os, sys, requests, time
from pathlib import Path
from django.core.files import File

sys.path.insert(0, str(Path(__file__).parent))
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'config.settings')
import django
django.setup()

from catalog.models import Cocktail

API = "https://www.thecocktaildb.com/api/json/v1/1"
SAVE_DIR = Path("media/cocktaildb")
SAVE_DIR.mkdir(exist_ok=True)

def download_image(url, name):
    try:
        r = requests.get(url, timeout=10)
        path = SAVE_DIR / f"{name}.jpg"
        with open(path, 'wb') as f: f.write(r.content)
        return path
    except: return None

# Импорт популярных коктейлей
for drink_name in [
    "Mojito", "Negroni", "Old Fashioned", "Margarita", "Espresso Martini",
    "Manhattan", "Daiquiri", "Whiskey Sour", "Cosmopolitan", "Aperol Spritz",
    "Moscow Mule", "Piña Colada", "Bloody Mary", "Martini", "Gin Tonic",
    "Long Island", "Mai Tai", "Tom Collins", "French 75", "Dark 'n' Stormy"
]:
    # Поиск по названию
    resp = requests.get(f"{API}/search.php?s={drink_name}").json()
    if not resp.get('drinks'): continue
    
    d = resp['drinks'][0]
    name = d['strDrink']
    
    if Cocktail.objects.filter(name__iexact=name).exists():
        print(f"⏭️ Уже есть: {name}")
        continue
    
    # Ингредиенты
    ingredients = []
    for i in range(1, 16):
        ing = d.get(f'strIngredient{i}')
        meas = d.get(f'strMeasure{i}')
        if ing:
            ingredients.append(f"{meas or ''} {ing}".strip())
    
    # Фото
    img_path = None
    if d.get('strDrinkThumb'):
        img_path = download_image(d['strDrinkThumb'], name.replace(' ', '_'))
    
    # Создаём
    cocktail = Cocktail.objects.create(
        name=name,
        composition='\n'.join(ingredients),
        strength="15-25%" if d.get('strAlcoholic') == 'Alcoholic' else "",
        type="alco" if d.get('strAlcoholic') == 'Alcoholic' else "non_alco",
        price=450,
        is_active=True
    )
    
    if img_path:
        with open(img_path, 'rb') as f:
            cocktail.image.save(f"{name}.jpg", File(f), save=True)
    
    print(f"✅ {name}")
    time.sleep(0.5)

print("\n🎉 Готово! Проверьте админку: /admin/catalog/cocktail/")