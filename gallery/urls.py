from django.urls import path
from . import views

app_name = 'gallery'  # ← Просто добавьте эту строку

urlpatterns = [
    path('cases/', views.case_list, name='cases'),  # ← НЕ УДАЛЯЙТЕ это!
]