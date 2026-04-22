from django.contrib import admin
from .models import Case, CaseImage, Client


class CaseImageInline(admin.TabularInline):
    model = CaseImage
    extra = 1


@admin.register(Case)
class CaseAdmin(admin.ModelAdmin):
    list_display = ('title', 'date', 'guests_count', 'location', 'is_published')
    list_filter = ('is_published',)
    search_fields = ('title', 'description', 'location')
    date_hierarchy = 'date'
    inlines = [CaseImageInline]


@admin.register(CaseImage)
class CaseImageAdmin(admin.ModelAdmin):
    list_display = ('case', 'is_cover')
    list_filter = ('is_cover',)


@admin.register(Client)
class ClientAdmin(admin.ModelAdmin):
    list_display = ('name', 'website')
    search_fields = ('name', 'website')
