from django.db import models

class Page(models.Model):
    title = models.CharField("Заголовок", max_length=200)
    slug = models.SlugField("URL-адрес", unique=True, help_text="Например: home, contacts")
    is_active = models.BooleanField("Активна", default=True)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = "Страница"
        verbose_name_plural = "Страницы"

class Block(models.Model):
    TYPE_CHOICES = [
        ('text', 'Текст'), 
        ('image', 'Изображение'), 
        ('hero', 'Главный экран'), 
        ('cta', 'Призыв к действию')
    ]
    page = models.ForeignKey(Page, on_delete=models.CASCADE, related_name='blocks', verbose_name="Страница")
    title = models.CharField("Заголовок блока", max_length=200, blank=True)
    content = models.TextField("Текст", blank=True)
    image = models.ImageField("Изображение", upload_to='blocks/', blank=True, null=True)
    block_type = models.CharField("Тип", max_length=50, choices=TYPE_CHOICES)
    order = models.PositiveIntegerField("Порядок", default=0)

    def __str__(self):
        return f"{self.title or self.block_type} ({self.page.title})"

    class Meta:
        verbose_name = "Блок контента"
        verbose_name_plural = "Блоки контента"
        ordering = ['order']