from django.db import models
from utils.validators import validate_image_file


class TeamMember(models.Model):
    """Модель члена команды барменов"""
    name = models.CharField('Имя', max_length=100)
    role = models.CharField('Должность', max_length=100)
    bio = models.TextField('Биография')
    photo = models.ImageField(
        'Фото',
        upload_to='team/',
        validators=[validate_image_file],
        blank=True,
        null=True
    )
    experience_years = models.IntegerField('Опыт (лет)', default=0)
    specialization = models.CharField('Специализация', max_length=200, blank=True)
    order = models.IntegerField('Порядок', default=0)
    is_active = models.BooleanField('Активен', default=True)
    
    class Meta:
        verbose_name = 'Член команды'
        verbose_name_plural = 'Команда'
        ordering = ['order', 'name']
    
    def __str__(self):
        return f"{self.name} - {self.role}"


class LoyaltyLevel(models.Model):
    """Модель уровня программы лояльности"""
    cocktails_count = models.IntegerField('Количество коктейлей')
    gift_name = models.CharField('Название подарка', max_length=200)
    gift_description = models.TextField('Описание подарка', blank=True)
    gift_value = models.DecimalField('Стоимость подарка', max_digits=10, decimal_places=2, default=0)
    icon = models.CharField('Иконка (emoji)', max_length=10, default='🎁')
    order = models.IntegerField('Порядок', default=0)
    is_active = models.BooleanField('Активен', default=True)
    
    class Meta:
        verbose_name = 'Уровень лояльности'
        verbose_name_plural = 'Программа лояльности'
        ordering = ['cocktails_count']
    
    def __str__(self):
        return f"{self.cocktails_count} коктейлей → {self.gift_name}"


class Cocktail(models.Model):
    TYPE_CHOICES = (('alco', 'Алкогольный'), ('non_alco', 'Безалкогольный'))
    name = models.CharField("Название", max_length=100)
    composition = models.TextField("Состав", help_text="Перечислите ингредиенты")
    strength = models.CharField("Крепость", max_length=50, blank=True)
    price = models.DecimalField("Цена за коктейль (₽)", max_digits=10, decimal_places=2, help_text="Используется для расчета в пакете")
    type = models.CharField("Тип", max_length=20, choices=TYPE_CHOICES)
    image = models.ImageField(
        "Фото", 
        upload_to='menu/', 
        blank=True,
        validators=[validate_image_file],
        help_text="Максимальный размер: 5 МБ. Форматы: JPG, PNG, GIF, WEBP"
    )
    is_active = models.BooleanField("Показывать", default=True)
    order = models.PositiveIntegerField("Порядок", default=0)

    def __str__(self):
        return self.name

    class Meta:
        verbose_name = "Коктейль"
        verbose_name_plural = "Коктейли"
        ordering = ['order']

class Package(models.Model):
    name = models.CharField("Название пакета", max_length=100)
    description = models.TextField("Описание")
    price = models.DecimalField("Базовая цена пакета (₽)", max_digits=10, decimal_places=2)
    image = models.ImageField(
        "Обложка",
        upload_to='packages/',
        validators=[validate_image_file],
        help_text="Максимальный размер: 5 МБ. Форматы: JPG, PNG, GIF, WEBP"
    )
    cocktails = models.ManyToManyField(Cocktail, through='PackageCocktail', verbose_name="Коктейли в пакете")
    is_popular = models.BooleanField("Хит продаж", default=False)

    def __str__(self):
        return self.name

    class Meta:
        verbose_name = "Пакет"
        verbose_name_plural = "Пакеты"

class PackageCocktail(models.Model):
    package = models.ForeignKey(Package, on_delete=models.CASCADE)
    cocktail = models.ForeignKey(Cocktail, on_delete=models.CASCADE)
    portions = models.PositiveIntegerField("Кол-во порций")

    def __str__(self):
        return f"{self.package.name} - {self.cocktail.name} ({self.portions} шт)"

    class Meta:
        verbose_name = "Состав пакета"
        verbose_name_plural = "Состав пакетов"

class AddonService(models.Model):
    """Дополнительная услуга / Тематический бар / Шоу-программа"""
    
    CATEGORY_CHOICES = [
        ('themed_bar', 'Тематический бар'),
        ('show', 'Шоу-программа'),
        ('additional', 'Дополнительная услуга'),
        ('equipment', 'Оборудование'),
    ]
    
    name = models.CharField("Услуга", max_length=100)
    price = models.DecimalField("Цена (₽)", max_digits=10, decimal_places=2, null=True, blank=True, help_text="Оставьте пустым, если 'По запросу'")
    description = models.TextField("Описание", blank=True)
    category = models.CharField("Категория", max_length=50, choices=CATEGORY_CHOICES, default='additional')
    image = models.ImageField(
        "Фото услуги",
        upload_to='addons/',
        blank=True,
        null=True,
        help_text="Рекомендуемый размер: 800×600 px. Форматы: JPG, PNG, WEBP"
    )
    order = models.PositiveIntegerField("Порядок", default=0)
    is_active_in_calc = models.BooleanField("Доступно в калькуляторе", default=True)

    def __str__(self):
        return self.name

    class Meta:
        verbose_name = "Доп. услуга"
        verbose_name_plural = "Доп. услуги"
        ordering = ['category', 'order', 'name']