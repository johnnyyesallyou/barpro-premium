# calculator/models.py
from django.db import models

class CalculatorSettings(models.Model):
    bartender_price = models.PositiveIntegerField("Цена часа бармена (₽)", default=3000)
    cocktail_base_price = models.PositiveIntegerField("Средняя цена коктейля (₽)", default=450)
    min_guests = models.PositiveIntegerField("Мин. гостей", default=10)
    max_guests = models.PositiveIntegerField("Макс. гостей", default=500)
    
    def __str__(self):
        return "Настройки калькулятора"
    
    class Meta:
        verbose_name = "Настройки калькулятора"

# calculator/logic.py (Логика расчета из БД)
from .models import CalculatorSettings

def calc_cost(data: dict) -> float:
    try:
        settings = CalculatorSettings.objects.first()
    except:
        settings = type('obj', (object,), {'bartender_price': 3000, 'cocktail_base_price': 450})() # Fallback

    guests = int(data.get('guests', 10))
    hours = int(data.get('hours', 3))
    cocktails_per_person = int(data.get('cocktails_per_person', 2))
    addons = data.get('addons', []) # Список ID доп услуг
    
    # Базовый расчет
    cost = (hours * settings.bartender_price) + \
           (guests * cocktails_per_person * settings.cocktail_base_price)

    # Доп услуги (в ТЗ фиксированная сумма, берем из БД)
    # Здесь упрощенно, т.к. нужно доставать цены из AddonService
    from catalog.models import AddonService
    for addon_id in addons:
        try:
            addon = AddonService.objects.get(id=addon_id, price__isnull=False)
            cost += float(addon.price)
        except: pass
            
    return cost