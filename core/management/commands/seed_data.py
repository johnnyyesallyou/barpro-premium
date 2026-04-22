import io
from datetime import date
from django.core.management.base import BaseCommand
from django.core.files.base import ContentFile
from django.core.files.images import ImageFile
from PIL import Image as PILImage
from core.models import Page, Block
from catalog.models import Cocktail, Package, PackageCocktail, AddonService
from gallery.models import Case, CaseImage, Client
from calculator.models import CalculatorSettings

def get_placeholder_img(color="#d4af37", text="BarPro", size=(800, 600)):
    """Генерирует заглушку-картинку для демо"""
    img = PILImage.new("RGB", size, color)
    buffer = io.BytesIO()
    img.save(buffer, format="JPEG", quality=85)
    buffer.seek(0)
    return ImageFile(buffer, name=f"demo_{text}.jpg")

class Command(BaseCommand):
    help = 'Заполняет БД демо-данными по ТЗ'

    def handle(self, *args, **options):
        self.stdout.write(self.style.SUCCESS('🚀 Начинаю наполнение БД...'))

        # 1. Настройки калькулятора
        settings, _ = CalculatorSettings.objects.get_or_create(
            defaults={
                'bartender_price': 3500,
                'cocktail_base_price': 450,
                'min_guests': 10,
                'max_guests': 1000
            }
        )

        # 2. Коктейли
        cocktails_data = [
            {"name": "Негрони", "type": "alco", "strength": "24%", "price": 450, "composition": "Джин, Кампари, Красный вермут, Апельсиновая цедра"},
            {"name": "Апероль Шприц", "type": "alco", "strength": "11%", "price": 400, "composition": "Апероль, Просекко, Содовая, Апельсин"},
            {"name": "Виски Сауэр", "type": "alco", "strength": "18%", "price": 480, "composition": "Бурбон, Лимонный сироп, Яичный белок, Ангостура"},
            {"name": "Мохито Классик", "type": "alco", "strength": "12%", "price": 420, "composition": "Белый ром, Мята, Лайм, Тростниковый сахар, Содовая"},
            {"name": "Клубничный Лимонад", "type": "non_alco", "strength": "0%", "price": 350, "composition": "Клубничное пюре, Лимонный сок, Содовая, Сироп"},
        ]
        cocktails = []
        for data in cocktails_data:
            c, _ = Cocktail.objects.get_or_create(name=data['name'], defaults={**data, 'image': get_placeholder_img(text=data['name'])})
            cocktails.append(c)

        # 3. Пакеты баров
        packages_data = [
            {"name": "Лайт Бар", "price": 25000, "desc": "Идеально для камерных вечеринок до 20 гостей."},
            {"name": "Премиум Коктейль", "price": 45000, "desc": "Полный бар, премиальный алкоголь, два бармена."},
            {"name": "Корпоратив PRO", "price": 90000, "desc": "До 50 гостей, авторская карта, шоу-программа, стойка."},
        ]
        packages = []
        for p in packages_data:
            pkg, _ = Package.objects.get_or_create(name=p['name'], defaults={
                'description': p['desc'],
                'price': p['price'],
                'is_popular': 'Премиум' in p['name'],
                'image': get_placeholder_img(text=p['name'])
            })
            packages.append(pkg)

        # 4. Привязка коктейлей к пакетам
        if not PackageCocktail.objects.exists():
            PackageCocktail.objects.create(package=packages[0], cocktail=cocktails[3], portions=20)
            PackageCocktail.objects.create(package=packages[0], cocktail=cocktails[4], portions=20)
            PackageCocktail.objects.create(package=packages[1], cocktail=cocktails[0], portions=15)
            PackageCocktail.objects.create(package=packages[1], cocktail=cocktails[1], portions=15)
            PackageCocktail.objects.create(package=packages[2], cocktail=cocktails[0], portions=20)
            PackageCocktail.objects.create(package=packages[2], cocktail=cocktails[2], portions=20)

        # 5. Дополнительные услуги
        addons = [
            {"name": "Аренда барной стойки", "price": 15000, "icon": "🍸"},
            {"name": "Ведущий-тамада", "price": 40000, "icon": "🎤"},
            {"name": "DJ и световое оборудование", "price": 25000, "icon": "🎧"},
            {"name": "Официанты", "price": 3000, "icon": "🤵", "description": "Цена за 1 час на 1 человека"},
            {"name": "Пирамида из бокалов", "price": 8000, "icon": "🥂"},
        ]
        for a in addons:
            AddonService.objects.get_or_create(name=a['name'], defaults=a)

        # 6. Кейсы
        cases = [
            {"title": "Свадьба в Парке Горького", "date": date(2025, 6, 15), "guests_count": 80, "location": "Москва", "description": "Выездной бар на свежем воздухе. Фруктовый и коктейльный бар."},
            {"title": "Корпоратив IT-компании", "date": date(2025, 10, 20), "guests_count": 150, "location": "Москва-Сити", "description": "Масштабное мероприятие. 3 бара, лазерное шоу, VIP-зона."},
        ]
        for c in cases:
            case, _ = Case.objects.get_or_create(title=c['title'], defaults=c)
            if not CaseImage.objects.filter(case=case).exists():
                CaseImage.objects.create(case=case, image=get_placeholder_img(text=case.title), is_cover=True)

        # 7. Клиенты
        Client.objects.get_or_create(name="Яндекс.Еда", website="https://eda.yandex.ru")
        Client.objects.get_or_create(name="Тинькофф", website="https://tinkoff.ru")
        Client.objects.get_or_create(name="VK", website="https://vk.com")

        # 8. Главная страница и блоки
        page, _ = Page.objects.get_or_create(slug='home', defaults={'title': 'Главная', 'is_active': True})
        if not Block.objects.filter(page=page).exists():
            Block.objects.create(page=page, block_type='hero', title='Выездной бар под ключ', content='Москва и Санкт-Петербург', order=1)
            Block.objects.create(page=page, block_type='text', title='Почему выбирают нас?', content='Приезжаем за 2 часа. Работаем по договору. Знаем все тонкости выездных мероприятий.', order=2)

        self.stdout.write(self.style.SUCCESS('✅ База успешно наполнена!'))
        self.stdout.write(self.style.WARNING('📸 Замените демо-картинки в админке на реальные фото.'))