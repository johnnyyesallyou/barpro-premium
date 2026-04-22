from django.contrib import admin
from .models import CalculatorSettings


@admin.register(CalculatorSettings)
class CalculatorSettingsAdmin(admin.ModelAdmin):
    list_display = ('bartender_price', 'cocktail_base_price', 'min_guests', 'max_guests')
