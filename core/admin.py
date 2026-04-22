from django.contrib import admin
from .models import Page, Block


class BlockInline(admin.TabularInline):
    model = Block
    extra = 1
    fields = ('title', 'block_type', 'order', 'image')


@admin.register(Page)
class PageAdmin(admin.ModelAdmin):
    list_display = ('title', 'slug', 'is_active')
    list_filter = ('is_active',)
    search_fields = ('title', 'slug')
    prepopulated_fields = {'slug': ('title',)}
    inlines = [BlockInline]


@admin.register(Block)
class BlockAdmin(admin.ModelAdmin):
    list_display = ('title', 'page', 'block_type', 'order')
    list_filter = ('block_type', 'page')
    search_fields = ('title',)
