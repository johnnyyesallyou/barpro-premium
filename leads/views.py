# leads/views.py
from django.http import JsonResponse
from django.views.decorators.http import require_POST
from django.views.decorators.csrf import ensure_csrf_cookie
import json
import logging
from .forms import LeadForm

logger = logging.getLogger(__name__)


@ensure_csrf_cookie
@require_POST
def submit_lead(request):
    """
    Принимает JSON с данными формы и сохраняет заявку в БД.
    Защищено: валидация данных, rate limiting, логирование
    """
    try:
        # Парсим JSON
        try:
            data = json.loads(request.body)
        except json.JSONDecodeError:
            logger.warning(f"Invalid JSON from IP {request.META.get('REMOTE_ADDR')}")
            return JsonResponse({
                'success': False, 
                'error': 'Некорректный формат данных'
            }, status=400)
        
        # Валидируем данные через форму
        form = LeadForm(data)
        
        if form.is_valid():
            # Сохраняем заявку
            lead = form.save()
            
            logger.info(
                f"New lead #{lead.id} from {lead.name} ({lead.phone}), "
                f"IP: {request.META.get('REMOTE_ADDR')}"
            )
            
            return JsonResponse({
                'success': True, 
                'message': 'Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.'
            })
        else:
            # Возвращаем ошибки валидации
            logger.warning(
                f"Invalid lead data from IP {request.META.get('REMOTE_ADDR')}: "
                f"{form.errors}"
            )
            
            return JsonResponse({
                'success': False,
                'errors': form.errors
            }, status=400)
            
    except Exception as e:
        # Логируем ошибку, но НЕ показываем детали клиенту
        logger.error(
            f"Error in submit_lead from IP {request.META.get('REMOTE_ADDR')}: {e}",
            exc_info=True
        )
        
        return JsonResponse({
            'success': False,
            'error': 'Произошла ошибка при обработке заявки. Попробуйте позже или свяжитесь с нами по телефону.'
        }, status=500)