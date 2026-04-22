from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('admin/', admin.site.urls),
    
    # Специфичные маршруты (должны быть первыми!)
    path('calculator/', include('calculator.urls')),
    path('api/lead/', include('leads.urls')),
    
    # Маршруты каталога и галереи
    path('', include('catalog.urls')),
    path('', include('gallery.urls')),
    
    # Общий роутер для страниц (должен быть последним!)
    path('', include('core.urls')),
]

if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)