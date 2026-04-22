# Generated manually for BarPro Premium

from django.db import migrations, models
import django.db.models.deletion
import utils.validators


class Migration(migrations.Migration):

    dependencies = [
        ('catalog', '0001_initial'),
    ]

    operations = [
        # Добавляем поля в AddonService
        migrations.AddField(
            model_name='addonservice',
            name='category',
            field=models.CharField(
                choices=[
                    ('themed_bar', 'Тематический бар'),
                    ('show', 'Шоу-программа'),
                    ('additional', 'Дополнительная услуга'),
                    ('equipment', 'Оборудование'),
                ],
                default='additional',
                max_length=50,
                verbose_name='Категория',
            ),
        ),
        migrations.AddField(
            model_name='addonservice',
            name='order',
            field=models.PositiveIntegerField(default=0, verbose_name='Порядок'),
        ),
        
        # Создаём модель TeamMember
        migrations.CreateModel(
            name='TeamMember',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(max_length=100, verbose_name='Имя')),
                ('role', models.CharField(max_length=100, verbose_name='Должность')),
                ('bio', models.TextField(verbose_name='Биография')),
                ('photo', models.ImageField(
                    blank=True,
                    null=True,
                    upload_to='team/',
                    validators=[utils.validators.validate_image_file],
                    verbose_name='Фото'
                )),
                ('experience_years', models.IntegerField(default=0, verbose_name='Опыт (лет)')),
                ('specialization', models.CharField(blank=True, max_length=200, verbose_name='Специализация')),
                ('order', models.IntegerField(default=0, verbose_name='Порядок')),
                ('is_active', models.BooleanField(default=True, verbose_name='Активен')),
            ],
            options={
                'verbose_name': 'Член команды',
                'verbose_name_plural': 'Команда',
                'ordering': ['order', 'name'],
            },
        ),
        
        # Создаём модель LoyaltyLevel
        migrations.CreateModel(
            name='LoyaltyLevel',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('cocktails_count', models.IntegerField(verbose_name='Количество коктейлей')),
                ('gift_name', models.CharField(max_length=200, verbose_name='Название подарка')),
                ('gift_description', models.TextField(blank=True, verbose_name='Описание подарка')),
                ('gift_value', models.DecimalField(decimal_places=2, default=0, max_digits=10, verbose_name='Стоимость подарка')),
                ('icon', models.CharField(default='🎁', max_length=10, verbose_name='Иконка (emoji)')),
                ('order', models.IntegerField(default=0, verbose_name='Порядок')),
                ('is_active', models.BooleanField(default=True, verbose_name='Активен')),
            ],
            options={
                'verbose_name': 'Уровень лояльности',
                'verbose_name_plural': 'Программа лояльности',
                'ordering': ['cocktails_count'],
            },
        ),
        
        # Обновляем ordering для AddonService
        migrations.AlterModelOptions(
            name='addonservice',
            options={
                'ordering': ['category', 'order', 'name'],
                'verbose_name': 'Доп. услуга',
                'verbose_name_plural': 'Доп. услуги',
            },
        ),
    ]
