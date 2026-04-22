from django.urls import path
from . import views

app_name = 'catalog'  # ← ЭТО СТРОКА РЕШАЕТ ПРОБЛЕМУ

urlpatterns = [
    path('packages/', views.packages_list, name='packages'),
    path('menu/', views.menu_list, name='menu'),
]