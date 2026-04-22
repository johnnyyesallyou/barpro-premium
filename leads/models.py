# leads/models.py
from django.db import models

class Lead(models.Model):
    STATUS_CHOICES = (
        ('new', 'Новая'),
        ('work', 'В работе'),
        ('done', 'Закрыта'),
        ('cancel', 'Отмена')
    )
    
    name = models.CharField("Имя", max_length=100)
    phone = models.CharField("Телефон", max_length=20)
    email = models.EmailField("Email", blank=True)
    
    event_date = models.DateField("Дата мероприятия", blank=True, null=True)
    message = models.TextField("Комментарий клиента", blank=True)
    
    # Данные из калькулятора
    guests = models.PositiveIntegerField("Гостей", blank=True, null=True)
    calc_data = models.JSONField("Параметры расчета", blank=True, null=True, help_text="JSON с настройками калькулятора")
    total_sum = models.PositiveIntegerField("Сумма из расчета", blank=True, null=True)
    
    status = models.CharField("Статус", max_length=20, choices=STATUS_CHOICES, default='new')
    manager_note = models.TextField("Заметка менеджера", blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.name} ({self.phone})"

    class Meta:
        verbose_name = "Заявка"
        verbose_name_plural = "Заявки"
        ordering = ['-created_at']