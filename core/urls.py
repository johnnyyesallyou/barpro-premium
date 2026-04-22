# core/urls.py
from django.urls import path
from . import views

app_name = 'core'  # ← Обязательно для namespace 'core'

urlpatterns = [
    path('', views.home, name='home'),
    path('team/', views.team_page, name='team'),
    path('loyalty/', views.loyalty_page, name='loyalty'),
    path('bars/', views.themed_bars_page, name='bars'),      # ← name='bars' (не 'themed_bars'!)
    path('shows/', views.shows_page, name='shows'),
    path('page/<slug:slug>/', views.page_detail, name='page_detail'),
]