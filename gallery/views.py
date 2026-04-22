from django.shortcuts import render, get_object_or_404
from .models import Case, Client

def case_list(request):
    cases = Case.objects.filter(is_published=True)
    clients = Client.objects.all()
    return render(request, 'gallery/cases.html', {'cases': cases, 'clients': clients})

def case_detail(request, case_id):
    case = get_object_or_404(Case, id=case_id)
    images = case.images.all()
    return render(request, 'gallery/case_detail.html', {'case': case, 'images': images})