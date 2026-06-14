<?php
/**
 * Honeypot anti-spam fields
 * Включается в любую форму: get_template_part('template-parts/honeypot')
 *
 * На сервере проверяем: !empty($_POST['website']) || !empty($_POST['hp_name'])
 *
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!--
  HONEYPOT: скрыты через CSS, видны только ботам.
  Не добавляйте autocomplete и не делайте их видимыми.
-->
<div class="hp-fields"
     aria-hidden="true"
     style="position:absolute;left:-9999px;top:-9999px;opacity:0;height:0;overflow:hidden;"
     tabindex="-1">
    <label for="hp_website">Website</label>
    <input type="text"  id="hp_website" name="website"  value="" autocomplete="off" tabindex="-1">
    <label for="hp_name_field">Name</label>
    <input type="text"  id="hp_name_field" name="hp_name"  value="" autocomplete="off" tabindex="-1">
</div>
<!-- Время начала заполнения формы — проверяем на сервере (< 3 сек = бот) -->
<input type="hidden" name="_form_time" value="<?php echo esc_attr( (string) time() ); ?>">
