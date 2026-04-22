# calculator/logic.py
from .models import CalculatorSettings
import logging

logger = logging.getLogger(__name__)


def calc_cost(data: dict) -> dict:
    """
    Рассчитывает стоимость на основе параметров
    Возвращает dict с результатом и возможными ошибками
    
    Args:
        data: словарь с параметрами {guests, hours, cocktails_per_person, addons}
    
    Returns:
        {'total': int, 'error': str|None}
    """
    # Получаем настройки из БД
    try:
        settings = CalculatorSettings.objects.first()
    except Exception as e:
        logger.warning(f"Failed to load CalculatorSettings: {e}")
        # Используем значения по умолчанию
        settings = type('obj', (object,), {
            'bartender_price': 3000,
            'cocktail_base_price': 450
        })()
    
    # Валидация и получение параметров
    try:
        guests = int(data.get('guests', 10))
        hours = int(data.get('hours', 3))
        cocktails_per_person = int(data.get('cocktails_per_person', 2))
    except (ValueError, TypeError) as e:
        logger.warning(f"Invalid data types in calc_cost: {e}")
        return {
            'total': 0,
            'error': 'Некорректные данные. Проверьте введенные значения.'
        }
    
    # Проверка границ значений
    if not (1 <= guests <= 1000):
        return {
            'total': 0,
            'error': 'Количество гостей должно быть от 1 до 1000'
        }
    
    if not (1 <= hours <= 24):
        return {
            'total': 0,
            'error': 'Количество часов должно быть от 1 до 24'
        }
    
    if not (1 <= cocktails_per_person <= 20):
        return {
            'total': 0,
            'error': 'Количество коктейлей на человека должно быть от 1 до 20'
        }
    
    # Базовая формула расчета
    # (Часы работы * Цена бармена) + (Гости * Коктейли на человека * Цена коктейля)
    cost = (hours * settings.bartender_price) + \
           (guests * cocktails_per_person * settings.cocktail_base_price)
    
    # Добавляем дополнительные услуги
    addons = data.get('addons', [])
    
    if not isinstance(addons, list):
        logger.warning(f"Invalid addons format: {type(addons)}")
        return {
            'total': 0,
            'error': 'Некорректный формат дополнительных услуг'
        }
    
    # Ограничиваем количество услуг (защита от переполнения)
    addons = addons[:50]
    
    from catalog.models import AddonService
    for addon_id in addons:
        try:
            addon_id = int(addon_id)
            addon = AddonService.objects.get(
                id=addon_id,
                price__isnull=False,
                is_active_in_calc=True
            )
            cost += float(addon.price)
        except (ValueError, TypeError):
            logger.warning(f"Invalid addon_id type: {addon_id}")
            continue
        except AddonService.DoesNotExist:
            logger.warning(f"AddonService {addon_id} not found or inactive")
            continue
        except Exception as e:
            logger.error(f"Error adding addon {addon_id}: {e}")
            continue
    
    # Применяем максимальный лимит (защита от переполнения)
    max_total = 50_000_000  # 50 миллионов рублей
    if cost > max_total:
        logger.warning(f"Calculated cost {cost} exceeds max limit {max_total}")
        cost = max_total
    
    return {
        'total': int(cost),
        'error': None
    }