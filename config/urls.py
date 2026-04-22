# config/urls.py
from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('admin/', admin.site.urls),
    
    # Специфичные маршруты (должны быть первыми!)
    path('calculator/', include('calculator.urls')),
    path('api/lead/', include('leads.urls')),
    
    # 🔹 Маршруты каталога и галереи — С ПРЕФИКСАМИ!
    path('menu/', include('catalog.urls')),        # ← Было: '' → Стало: 'menu/'
    path('gallery/', include('gallery.urls')),      # ← Было: '' → Стало: 'gallery/'
    
    # 🔹 Core (главная + страницы) — ПОСЛЕДНИМ, с пустым префиксом
    path('', include('core.urls')),                 # ← Обрабатывает корень "/"
]

if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
