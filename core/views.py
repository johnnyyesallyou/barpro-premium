# core/views.py
from django.shortcuts import render, get_object_or_404
from .models import Page, Block
from catalog.models import Cocktail, Package, TeamMember, LoyaltyLevel, AddonService


def home(request):
    """Главная страница — Премиум версия"""
    context = {
        # Основные блоки
        'cocktails': Cocktail.objects.filter(is_active=True).order_by('order')[:6],
        'packages': Package.objects.all()[:3],
        'team_members': TeamMember.objects.filter(is_active=True).order_by('order')[:4],
        'loyalty_levels': LoyaltyLevel.objects.filter(is_active=True).order_by('cocktails_count'),
        
        # Услуги (тематические бары + шоу-программы вместе)
        'addon_services': AddonService.objects.filter(is_active_in_calc=True).order_by('category', 'order'),
    }
    return render(request, 'core/home.html', context)


def team_page(request):
    """Страница команды"""
    return render(request, 'core/team.html', {
        'team_members': TeamMember.objects.filter(is_active=True).order_by('order'),
    })


def loyalty_page(request):
    """Страница программы лояльности"""
    return render(request, 'core/loyalty.html', {
        'loyalty_levels': LoyaltyLevel.objects.filter(is_active=True).order_by('cocktails_count'),
    })


def themed_bars_page(request):
    """Страница тематических баров"""
    return render(request, 'core/themed_bars.html', {
        'themed_bars': AddonService.objects.filter(category='themed_bar', is_active_in_calc=True).order_by('order'),
    })


def shows_page(request):
    """Страница шоу-программ"""
    return render(request, 'core/shows.html', {
        'shows': AddonService.objects.filter(category='show', is_active_in_calc=True).order_by('order'),
    })


def page_detail(request, slug):
    """Детальная страница по slug"""
    page = get_object_or_404(Page, slug=slug, is_active=True)
    blocks = page.blocks.all().order_by('order')
    return render(request, 'pages/page_detail.html', {
        'page': page,
        'blocks': blocks,
    })