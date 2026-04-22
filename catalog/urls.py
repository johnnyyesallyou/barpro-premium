from django.urls import path
from . import views

app_name = 'catalog'

urlpatterns = [
    path('menu/', views.menu_page, name='menu'),        # ← /menu/
    path('packages/', views.packages_page, name='packages'),  # ← /packages/
]
