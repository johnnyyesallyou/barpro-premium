from django.shortcuts import render
from .models import Case, Client

def case_list(request):
    cases = Case.objects.filter(is_published=True)
    clients = Client.objects.all()
    return render(request, 'gallery/cases.html', {'cases': cases, 'clients': clients})