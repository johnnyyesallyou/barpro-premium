from django.urls import path
from . import views

app_name = 'calculator'

urlpatterns = [
    path('', views.calculator_page, name='calculator'),
    path('api/', views.calculate_api, name='calc_api'),
]