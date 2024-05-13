<?php
$colors = new Color_Group_CPT();
?>


<div class="custom-input-wrapper" style="display:none;">
    <label for="custom_color_field">Custom color Field:</label>
    <input type="text" id="custom_color_field" name="custom_color_field">
</div>
<div>
    <div id="popupButton">Kies een kleur</div>
</div>
<div>* Op kleur gemaakte verf kan niet retour</div>
<div class="custom-color-price"></div>
<div id="myPopup" class="popup">
    <div id="popup-content" class="popup-content">
        <div  id="popup-header" class="popup-header">
        <h3><?php echo ('Kies een kleur') ?></h3>
        <div>
            <p>De prijs van onze natuurverven varieert afhankelijk van de gekozen kleur.
                Zodra je een kleur selecteert, past de prijs zich automatisch aan.
                Elke kleur is ingedeeld in een prijsgroep (Prijsgroep 1 t/m 3), wat de prijsbepaling vergemakkelijkt.
                Met een simpele klik op een kleur zie je direct de bijbehorende prijs.
            </p>

        </div>
        <div class="color-filter-bar">
            
        
        <div class="search-box">
            <input type="text" id="filterInput" placeholder="Zoek een kleur...">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
            </svg>
        </div>
        <div class="color-filters" style="display: flex; flex-wrap: wrap;"><div><small>Filter op kleur:</small></div>
    <?php
        foreach ($colors::COLOR_VALUES as $color => $value) {
            echo '<div class="color-filter-choice" style="text-align: center; " class="filter-color-item" data-colorfilter="'.$color.'">';
            echo '<div style="width: 18px; height: 18px; background-color: ' . $value['hex'] . ';" data-colorfilter="'.$color.'"></div>';
          
            echo '</div>';
        }
?>
        </div>
        </div>
        </div>
        <div id="color-content">

        </div>
        <div  id="color-result" id="color-result">

        </div>

    </div>
</div>
<style>
    .single_variation_wrap .woocommerce-variation-price {
        display: none;
    }
</style>