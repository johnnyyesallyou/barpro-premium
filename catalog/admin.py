# catalog/admin.py
from django.contrib import admin
from django.utils.safestring import mark_safe
from import_export.admin import ImportExportMixin
from import_export.formats.base_formats import XLSX
from .models import Cocktail, Package, PackageCocktail, AddonService, TeamMember, LoyaltyLevel
from .resources import CocktailResource


# 🔹 Inline для пакетов
class PackageCocktailInline(admin.TabularInline):
    model = PackageCocktail
    extra = 1
    fields = ('cocktail', 'portions')


# 🔹 Коктейли: превью + экспорт
@admin.register(Cocktail)
class CocktailAdmin(ImportExportMixin, admin.ModelAdmin):
    resource_class = CocktailResource
    import_formats = [XLSX]
    export_formats = [XLSX]
    
    list_display = ('image_preview', 'name', 'type', 'strength', 'price', 'is_active', 'order')
    list_filter = ('type', 'is_active')
    search_fields = ('name', 'composition')
    list_editable = ('is_active', 'order')
    ordering = ('order', 'name')
    
    def image_preview(self, obj):
        if obj.image:
            return mark_safe(
                f'<img src="{obj.image.url}" width="60" height="60" '
                f'style="object-fit:cover; border-radius:6px; border:1px solid #ddd">'
            )
        return mark_safe('<span style="color:#999">—</span>')
    image_preview.short_description = "Фото"


# 🔹 Пакеты
@admin.register(Package)
class PackageAdmin(ImportExportMixin, admin.ModelAdmin):
    list_display = ('name', 'price', 'is_popular')
    list_filter = ('is_popular',)
    search_fields = ('name', 'description')
    inlines = [PackageCocktailInline]


# 🔹 Состав пакетов
@admin.register(PackageCocktail)
class PackageCocktailAdmin(admin.ModelAdmin):
    list_display = ('package', 'cocktail', 'portions')
    list_filter = ('package',)


# 🔹 Доп. услуги (тематические бары, шоу и т.д.) — С ФОТО вместо иконки
@admin.register(AddonService)
class AddonServiceAdmin(ImportExportMixin, admin.ModelAdmin):
    list_display = ('image_preview', 'name', 'category', 'price', 'order', 'is_active_in_calc')
    list_filter = ('category', 'is_active_in_calc')
    search_fields = ('name', 'description')
    list_editable = ('order',)
    ordering = ('category', 'order', 'name')
    
    # Превью фото
    def image_preview(self, obj):
        if obj.image:
            return mark_safe(
                f'<img src="{obj.image.url}" width="60" height="60" '
                f'style="object-fit:cover; border-radius:8px; border:1px solid #ddd">'
            )
        return mark_safe('<span style="color:#999; font-size:1.5rem">🖼️</span>')
    image_preview.short_description = "Фото"
    
    # Группировка полей
    fieldsets = (
        ('Основное', {'fields': ('name', 'category', 'price', 'description')}),
        ('Медиа', {'fields': ('image',)}),  # 🔹 Было: ('icon',)
        ('Настройки', {'fields': ('order', 'is_active_in_calc')}),
    )


# 🔹 Команда
@admin.register(TeamMember)
class TeamMemberAdmin(ImportExportMixin, admin.ModelAdmin):
    list_display = ('name', 'role', 'experience_years', 'order', 'is_active')
    list_filter = ('is_active', 'experience_years')
    search_fields = ('name', 'role', 'specialization')
    list_editable = ('order', 'is_active')
    ordering = ('order', 'name')


# 🔹 Программа лояльности
@admin.register(LoyaltyLevel)
class LoyaltyLevelAdmin(ImportExportMixin, admin.ModelAdmin):
    list_display = ('cocktails_count', 'gift_name', 'gift_value', 'icon', 'order', 'is_active')
    list_filter = ('is_active',)
    search_fields = ('gift_name', 'gift_description')
    list_editable = ('order', 'is_active')
    ordering = ('cocktails_count',)