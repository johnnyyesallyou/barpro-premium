"""
Формы валидации для заявок
Защита от некорректных и вредоносных данных
"""
from django import forms
from .models import Lead
import re
import logging

logger = logging.getLogger(__name__)


class LeadForm(forms.ModelForm):
    """
    Форма для валидации заявок клиентов
    """
    class Meta:
        model = Lead
        fields = [
            'name', 'phone', 'email', 'event_date', 
            'message', 'guests', 'calc_data', 'total_sum'
        ]
    
    def clean_name(self):
        """Валидация имени"""
        name = self.cleaned_data.get('name', '').strip()
        
        if not name:
            raise forms.ValidationError("Имя не может быть пустым")
        
        if len(name) < 2:
            raise forms.ValidationError("Имя слишком короткое (минимум 2 символа)")
        
        if len(name) > 100:
            raise forms.ValidationError("Имя слишком длинное (максимум 100 символов)")
        
        # Проверяем на подозрительные символы (защита от SQL injection)
        if re.search(r'[<>{}[\]\\]', name):
            raise forms.ValidationError("Имя содержит недопустимые символы")
        
        return name
    
    def clean_phone(self):
        """Валидация телефона"""
        phone = self.cleaned_data.get('phone', '')
        
        if not phone:
            raise forms.ValidationError("Телефон обязателен для заполнения")
        
        # Убираем все кроме цифр и +
        phone_clean = re.sub(r'[^\d+]', '', phone)
        
        # Проверяем длину
        if len(phone_clean) < 10:
            raise forms.ValidationError(
                "Некорректный номер телефона (слишком короткий)"
            )
        
        if len(phone_clean) > 20:
            raise forms.ValidationError(
                "Некорректный номер телефона (слишком длинный)"
            )
        
        # Проверяем формат (должен начинаться с + или 7/8)
        if not re.match(r'^(\+7|7|8)\d{10}$', phone_clean):
            # Если не российский формат, проверяем международный
            if not re.match(r'^\+\d{10,15}$', phone_clean):
                logger.warning(f"Нестандартный формат телефона: {phone_clean}")
        
        return phone_clean
    
    def clean_email(self):
        """Валидация email"""
        email = self.cleaned_data.get('email', '').strip().lower()
        
        if not email:
            return email  # Email необязателен
        
        # Django уже проверяет формат через EmailField, но добавим доп. проверки
        if len(email) > 254:  # RFC 5321
            raise forms.ValidationError("Email слишком длинный")
        
        # Проверяем на подозрительные символы
        if re.search(r'[<>{}[\]\\]', email):
            raise forms.ValidationError("Email содержит недопустимые символы")
        
        return email
    
    def clean_guests(self):
        """Валидация количества гостей"""
        guests = self.cleaned_data.get('guests')
        
        if guests is None:
            return guests  # Может быть пустым
        
        if not isinstance(guests, int):
            try:
                guests = int(guests)
            except (ValueError, TypeError):
                raise forms.ValidationError("Количество гостей должно быть числом")
        
        if guests < 1:
            raise forms.ValidationError("Минимум 1 гость")
        
        if guests > 1000:
            raise forms.ValidationError(
                "Максимум 1000 гостей. Для больших мероприятий свяжитесь с нами напрямую"
            )
        
        return guests
    
    def clean_total_sum(self):
        """Валидация суммы"""
        total_sum = self.cleaned_data.get('total_sum')
        
        if total_sum is None:
            return total_sum
        
        if not isinstance(total_sum, (int, float)):
            try:
                total_sum = float(total_sum)
            except (ValueError, TypeError):
                raise forms.ValidationError("Сумма должна быть числом")
        
        if total_sum < 0:
            raise forms.ValidationError("Сумма не может быть отрицательной")
        
        # Максимальная разумная сумма: 10 миллионов рублей
        if total_sum > 10_000_000:
            raise forms.ValidationError(
                "Сумма слишком большая. Для крупных заказов свяжитесь с нами напрямую"
            )
        
        return int(total_sum)
    
    def clean_message(self):
        """Валидация сообщения"""
        message = self.cleaned_data.get('message', '').strip()
        
        if len(message) > 5000:
            raise forms.ValidationError("Сообщение слишком длинное (максимум 5000 символов)")
        
        # Проверяем на потенциально опасный HTML/JS
        dangerous_patterns = [
            r'<script',
            r'javascript:',
            r'onerror=',
            r'onclick=',
            r'<iframe',
        ]
        
        for pattern in dangerous_patterns:
            if re.search(pattern, message, re.IGNORECASE):
                logger.warning(f"Обнаружен подозрительный контент в сообщении: {pattern}")
                raise forms.ValidationError(
                    "Сообщение содержит недопустимый контент"
                )
        
        return message
    
    def clean_calc_data(self):
        """Валидация данных калькулятора"""
        calc_data = self.cleaned_data.get('calc_data')
        
        if calc_data is None:
            return calc_data
        
        # Проверяем что это словарь
        if not isinstance(calc_data, dict):
            raise forms.ValidationError("Некорректный формат данных калькулятора")
        
        # Проверяем размер JSON (не больше 10KB)
        import json
        json_str = json.dumps(calc_data)
        if len(json_str) > 10240:
            raise forms.ValidationError("Слишком много данных калькулятора")
        
        return calc_data
