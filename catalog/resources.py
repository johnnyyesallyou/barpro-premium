# catalog/resources.py
from import_export import resources
from .models import Cocktail

class CocktailResource(resources.ModelResource):
    class Meta:
        model = Cocktail
        fields = (
            'id',
            'name',
            'type',
            'strength',
            'price',
            'composition',
            'image',
            'is_active',
            'order',
        )
        export_order = fields  # Порядок колонок в Excel
        
    # Форматирование цены при экспорте
    def dehydrate_price(self, cocktail):
        return f"{cocktail.price:.2f}"
    
    # Обработка импорта: конвертация цены из строки в число
    def before_import_row(self, row, **kwargs):
        if 'price' in row and row['price']:
            try:
                row['price'] = float(str(row['price']).replace(',', '.'))
            except (ValueError, TypeError):
                row['price'] = None