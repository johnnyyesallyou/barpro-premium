# calculator/views.py
from django.shortcuts import render
from django.http import JsonResponse
from django.views.decorators.http import require_POST
from django.views.decorators.csrf import ensure_csrf_cookie
import json
import logging
from .logic import calc_cost
from catalog.models import AddonService

logger = logging.getLogger(__name__)


def calculator_page(request):
    """Отображает страницу калькулятора с группировкой услуг."""
    # Группируем услуги по категориям
    themed_bars = AddonService.objects.filter(category='themed_bar', is_active_in_calc=True).order_by('order')
    shows = AddonService.objects.filter(category='show', is_active_in_calc=True).order_by('order')
    additional = AddonService.objects.filter(category='additional', is_active_in_calc=True).order_by('order')
    equipment = AddonService.objects.filter(category='equipment', is_active_in_calc=True).order_by('order')
    
    return render(request, 'calculator/calculator.html', {
        'themed_bars': themed_bars,
        'shows': shows,
        'additional': additional,
        'equipment': equipment,
    })


@ensure_csrf_cookie
@require_POST
def calculate_api(request):
    """
    API endpoint для расчета стоимости (AJAX).
    Защищено: валидация данных, обработка ошибок, логирование
    """
    try:
        # Парсим JSON
        try:
            data = json.loads(request.body)
        except json.JSONDecodeError:
            logger.warning(f"Invalid JSON in calculate_api from IP {request.META.get('REMOTE_ADDR')}")
            return JsonResponse({
                'status': 'error',
                'message': 'Некорректный формат данных'
            }, status=400)
        
        # Вызываем функцию расчета
        result = calc_cost(data)
        
        # Проверяем наличие ошибки
        if result.get('error'):
            logger.info(f"Calculation error: {result['error']}")
            return JsonResponse({
                'status': 'error',
                'message': result['error']
            }, status=400)
        
        # Возвращаем успешный результат
        logger.info(
            f"Calculation successful: {result['total']} RUB, "
            f"IP: {request.META.get('REMOTE_ADDR')}"
        )
        
        return JsonResponse({
            'total': result['total'],
            'status': 'ok'
        })
        
    except Exception as e:
        # Логируем ошибку, но НЕ показываем детали клиенту
        logger.error(
            f"Error in calculate_api from IP {request.META.get('REMOTE_ADDR')}: {e}",
            exc_info=True
        )
        
        return JsonResponse({
            'status': 'error',
            'message': 'Произошла ошибка при расчете. Попробуйте позже.'
        }, status=500)