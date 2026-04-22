from django.contrib import admin
from import_export.admin import ImportExportModelAdmin
from .models import Lead

@admin.register(Lead)
class LeadAdmin(ImportExportModelAdmin):
    list_display = ('name', 'phone', 'event_date', 'total_sum', 'status', 'created_at')
    list_filter = ('status', 'created_at')
    search_fields = ('name', 'phone')
    readonly_fields = ('calc_data', 'created_at') # Менеджер не может менять расчет
    fieldsets = (
        ('Контакты', {'fields': ('name', 'phone', 'email', 'event_date')}),
        ('Детали', {'fields': ('guests', 'total_sum', 'status')}),
        ('Расчет (JSON)', {'fields': ('calc_data',), 'classes': ('collapse',)}),
        ('Заметки', {'fields': ('manager_note',)}),
    )

    def has_delete_permission(self, request, obj=None):
        return False # Заявки удалять нельзя (только архив)