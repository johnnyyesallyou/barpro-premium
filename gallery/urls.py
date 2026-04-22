from django.urls import path
from . import views

app_name = 'gallery'

urlpatterns = [
    path('gallery/', views.cases_page, name='cases'),        # ← /gallery/
    path('gallery/<int:case_id>/', views.case_detail, name='case_detail'),
]
