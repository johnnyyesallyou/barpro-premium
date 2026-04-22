from django.urls import path
from . import views

app_name = 'catalog'

urlpatterns = [
    path('menu/', views.menu_list, name='menu'),        # ← /menu/
    path('packages/', views.packages_list, name='packages'),  # ← /packages/
]
