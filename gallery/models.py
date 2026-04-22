from django.db import models
from utils.validators import validate_image_file

class Case(models.Model):
    title = models.CharField("Название мероприятия", max_length=200)
    date = models.DateField("Дата")
    guests_count = models.PositiveIntegerField("Гостей")
    location = models.CharField("Место", max_length=100, blank=True)
    description = models.TextField("Описание")
    is_published = models.BooleanField("Опубликовано", default=True)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = "Кейс"
        verbose_name_plural = "Кейсы"
        ordering = ['-date']

class CaseImage(models.Model):
    case = models.ForeignKey(Case, related_name='images', on_delete=models.CASCADE)
    image = models.ImageField(
        "Фото",
        upload_to='cases/',
        validators=[validate_image_file],
        help_text="Максимальный размер: 5 МБ. Форматы: JPG, PNG, GIF, WEBP"
    )
    is_cover = models.BooleanField("Обложка", default=False)

    def __str__(self):
        return f"Фото для {self.case.title}"

    class Meta:
        verbose_name = "Фото кейса"
        verbose_name_plural = "Фото кейсов"

class Client(models.Model):
    name = models.CharField("Название", max_length=100)
    logo = models.ImageField(
        "Логотип",
        upload_to='clients/',
        validators=[validate_image_file],
        help_text="Максимальный размер: 5 МБ. Форматы: JPG, PNG, GIF, WEBP"
    )
    website = models.URLField("Сайт", blank=True)

    def __str__(self):
        return self.name

    class Meta:
        verbose_name = "Клиент"
        verbose_name_plural = "Клиенты"