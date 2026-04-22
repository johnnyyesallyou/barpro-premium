from django.shortcuts import render
from .models import Package, Cocktail

def packages_list(request):
    packages = Package.objects.all()
    return render(request, 'catalog/packages.html', {'packages': packages})

def menu_list(request):
    cocktails = Cocktail.objects.filter(is_active=True)
    return render(request, 'catalog/menu.html', {'cocktails': cocktails})