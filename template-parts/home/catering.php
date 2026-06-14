<?php
/**
 * Template Part: КЕЙТЕРИНГ — split layout
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="studio-section reveal" aria-labelledby="catering-heading">
    <div class="studio-section__header">
        <p class="studio-section__eyebrow">Полное решение</p>
        <h2 id="catering-heading" class="studio-section__title">Бар + Кейтеринг</h2>
        <p class="studio-section__sub">Закажите всё вместе и получите скидку 15%</p>
    </div>

    <div class="catering-split">
        <div class="catering-split__text">
            <h3 class="catering-split__title">Это не просто еда —<br>это атмосфера</h3>
            <p class="catering-split__lead">
                Мы создаём гастрономическую атмосферу, которая усиливает эмоции вашего события — от первого бокала до последнего блюда.
            </p>
            <ul class="catering-split__list">
                <li>Фуршеты, банкеты, кофе-брейки</li>
                <li>BBQ, гастростанции, интерактивные зоны</li>
                <li>Выездная кухня для мероприятий на природе</li>
                <li>Персонал, оборудование, логистика — всё включено</li>
            </ul>
            <div class="catering-split__actions">
                <a href="<?php echo esc_url(home_url('/bar-catering')); ?>"
                   class="btn-pill btn-pill--gold">Комбо-пакеты</a>
                <a href="<?php echo esc_url(home_url('/catering')); ?>"
                   class="btn-pill btn-pill--ghost">Подробнее</a>
            </div>
        </div>

        <div class="benefits-grid">
            <div class="benefit-card reveal reveal--delay-1">
                <div class="benefit-card__icon" aria-hidden="true">💰</div>
                <div>
                    <div class="benefit-card__title">Скидка 15%</div>
                    <div class="benefit-card__text">на комплект бар + кейтеринг</div>
                </div>
            </div>
            <div class="benefit-card reveal reveal--delay-2">
                <div class="benefit-card__icon" aria-hidden="true">🎯</div>
                <div>
                    <div class="benefit-card__title">Одна команда</div>
                    <div class="benefit-card__text">один менеджер, один договор</div>
                </div>
            </div>
            <div class="benefit-card reveal reveal--delay-3">
                <div class="benefit-card__icon" aria-hidden="true">⏰</div>
                <div>
                    <div class="benefit-card__title">Синхронизация</div>
                    <div class="benefit-card__text">всё по тайм-коду, без накладок</div>
                </div>
            </div>
        </div>
    </div>
</section>
