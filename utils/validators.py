"""
Валидаторы для загружаемых файлов
Защита от загрузки вредоносных файлов
"""
from django.core.exceptions import ValidationError
from django.core.files.images import get_image_dimensions
import os


def validate_image_file(file):
    """
    Валидирует загружаемые изображения
    - Проверяет размер файла (макс 5 МБ)
    - Проверяет расширение файла
    - Проверяет что это действительно изображение
    """
    # Максимальный размер файла: 5 МБ
    max_size = 5 * 1024 * 1024
    if file.size > max_size:
        raise ValidationError(
            f"Размер файла не должен превышать {max_size // (1024*1024)} МБ. "
            f"Ваш файл: {file.size // (1024*1024)} МБ"
        )
    
    # Разрешенные расширения
    valid_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp']
    ext = os.path.splitext(file.name)[1].lower()
    
    if ext not in valid_extensions:
        raise ValidationError(
            f"Недопустимое расширение файла '{ext}'. "
            f"Разрешены: {', '.join(valid_extensions)}"
        )
    
    # Проверяем что это действительно изображение
    try:
        # Пытаемся получить размеры изображения
        width, height = get_image_dimensions(file)
        if width is None or height is None:
            raise ValidationError("Файл не является корректным изображением")
        
        # Проверяем разумные размеры (не больше 10000x10000 пикселей)
        if width > 10000 or height > 10000:
            raise ValidationError(
                f"Слишком большое разрешение изображения: {width}x{height}. "
                f"Максимум: 10000x10000 пикселей"
            )
            
    except Exception as e:
        if isinstance(e, ValidationError):
            raise
        raise ValidationError(
            "Не удалось обработать файл. Убедитесь что это корректное изображение"
        )
    
    return file


def validate_file_size(max_mb=5):
    """
    Фабрика валидатора размера файла
    Использование: validators=[validate_file_size(10)]
    """
    def validator(file):
        max_size = max_mb * 1024 * 1024
        if file.size > max_size:
            raise ValidationError(
                f"Размер файла не должен превышать {max_mb} МБ"
            )
        return file
    return validator
