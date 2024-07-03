<?php
// Clase para gestionar preferencias
require_once 'product_attributes.php';
class SWM_User_Preferences
{
    public static function swm_save_user_preference($user_id, $key, $value) {
        update_user_meta($user_id, $key, sanitize_text_field($value));
    }

    public static function swm_get_user_preference($user_id, $key) {
        return get_user_meta($user_id, $key, true);
    }

    // Función para renderizar el formulario de preferencias
    function swm_render_preferences_form()
    {
        $user_id = get_current_user_id();

        $preferencia_marca = $this->swm_get_user_preference($user_id, 'preferencia_marca', true);
        $preferencia_forma_esfera = $this->swm_get_user_preference($user_id, 'preferencia_forma_esfera', true);
        $preferencia_color_correa = $this->swm_get_user_preference($user_id, 'preferencia_color_correa', true);
        $preferencia_material_correa = $this->swm_get_user_preference($user_id, 'preferencia_material_correa', true);
        $preferencia_pantalla_tactil = $this->swm_get_user_preference($user_id, 'preferencia_pantalla_tactil', true);
        $preferencia_vida_bateria = $this->swm_get_user_preference($user_id, 'preferencia_vida_bateria', true);
        $preferencia_bluetooth = $this->swm_get_user_preference($user_id, 'preferencia_bluetooth', true);
        $preferencia_tamano_pantalla = $this->swm_get_user_preference($user_id, 'preferencia_tamano_pantalla', true);
        $preferencia_peso = $this->swm_get_user_preference($user_id, 'preferencia_peso', true);
        $preferencia_precio = $this->swm_get_user_preference($user_id, 'preferencia_precio', true);

    	// Si se ha enviado el formulario, actualizar preferencias desde $_POST
    	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        	$preferencia_marca = sanitize_text_field($_POST['preferencia_marca']);
        	$preferencia_forma_esfera = sanitize_text_field($_POST['preferencia_forma_esfera']);
        	$preferencia_color_correa = sanitize_text_field($_POST['preferencia_color_correa']);
        	$preferencia_material_correa = sanitize_text_field($_POST['preferencia_material_correa']);
            $preferencia_pantalla_tactil = sanitize_text_field($_POST['preferencia_pantalla_tactil']);
        	$preferencia_vida_bateria = sanitize_text_field($_POST['preferencia_vida_bateria']);
        	$preferencia_bluetooth = sanitize_text_field($_POST['preferencia_bluetooth']);
        	$preferencia_tamano_pantalla = sanitize_text_field($_POST['preferencia_tamano_pantalla']);
        	$preferencia_peso = sanitize_text_field($_POST['preferencia_peso']);
        	$preferencia_precio = sanitize_text_field($_POST['preferencia_precio']);

        	$this->swm_save_user_preference($user_id, 'preferencia_marca', $preferencia_marca);
        	$this->swm_save_user_preference($user_id, 'preferencia_forma_esfera', $preferencia_forma_esfera);
        	$this->swm_save_user_preference($user_id, 'preferencia_color_correa', $preferencia_color_correa);
        	$this->swm_save_user_preference($user_id, 'preferencia_material_correa', $preferencia_material_correa);
            $this->swm_save_user_preference($user_id, 'preferencia_pantalla_tactil', $preferencia_pantalla_tactil);
        	$this->swm_save_user_preference($user_id, 'preferencia_vida_bateria', $preferencia_vida_bateria);
        	$this->swm_save_user_preference($user_id, 'preferencia_bluetooth', $preferencia_bluetooth);
        	$this->swm_save_user_preference($user_id, 'preferencia_tamano_pantalla', $preferencia_tamano_pantalla);
        	$this->swm_save_user_preference($user_id, 'preferencia_peso', $preferencia_peso);
        	$this->swm_save_user_preference($user_id, 'preferencia_precio', $preferencia_precio);
    	}
        $attribute_values = SWM_ProductAttributes::swm_get_all_unique_attribute_values();

        // HTML del formulario
        ob_start();
?>
        <form method="post" action="" class="swm-preferences-form">
            <table class="form-table">
                <tr>
                    <th><label for="preferencia_marca">Marca</label></th>
                    <td>
                        <select name="preferencia_marca" id="preferencia_marca">
                            <option value="">Selecciona una opción</option>
                            <?php foreach ($attribute_values['marca'] as $value) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($preferencia_marca, $value); ?>><?php echo esc_html($value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="preferencia_forma_esfera">Forma de la Esfera</label></th>
                    <td>
                        <select name="preferencia_forma_esfera" id="preferencia_forma_esfera">
                            <option value="">Selecciona una opción</option>
                            <?php foreach ($attribute_values['forma_esfera'] as $value) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($preferencia_forma_esfera, $value); ?>><?php echo esc_html($value); ?></option>
                            <?php endforeach; ?>
                        </select>
                </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="preferencia_color_correa">Color de la Correa</label></th>
                    <td>
                        <select name="preferencia_color_correa" id="preferencia_color_correa">
                            <option value="">Selecciona una opción</option>
                            <?php foreach ($attribute_values['color_correa'] as $value) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($preferencia_color_correa, $value); ?>><?php echo esc_html($value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="preferencia_material_correa">Material de la Correa</label></th>
                    <td>
                        <select name="preferencia_material_correa" id="preferencia_material_correa">
                            <option value="">Selecciona una opción</option>
                            <?php foreach ($attribute_values['material_correa'] as $value) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($preferencia_material_correa, $value); ?>><?php echo esc_html($value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="preferencia_pantalla_tactil">Pantalla Táctil</label></th>
                    <td>
                        <select name="preferencia_pantalla_tactil" id="preferencia_pantalla_tactil">
                            <option value="">Selecciona una opción</option>
                            <?php foreach ($attribute_values['pantalla_tactil'] as $value) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($preferencia_pantalla_tactil, $value); ?>><?php echo esc_html($value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="preferencia_vida_bateria">Vida de la Batería (días)</label></th>
                    <td><input type="text" name="preferencia_vida_bateria" id="preferencia_vida_bateria" value="<?php echo esc_attr($preferencia_vida_bateria); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="preferencia_bluetooth">Bluetooth</label></th>
                    <td>
                        <select name="preferencia_bluetooth" id="preferencia_bluetooth">
                            <option value="">Selecciona una opción</option>
                            <?php foreach ($attribute_values['bluetooth'] as $value) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($preferencia_bluetooth, $value); ?>><?php echo esc_html($value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="preferencia_tamano_pantalla">Tamaño de Pantalla (pulgadas)</label></th>
                    <td><input type="text" name="preferencia_tamano_pantalla" id="preferencia_tamano_pantalla" value="<?php echo esc_attr($preferencia_tamano_pantalla); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="preferencia_peso">Peso</label></th>
                    <td><input type="text" name="preferencia_peso" id="preferencia_peso" value="<?php echo esc_attr($preferencia_peso); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="preferencia_precio">Precio</label></th>
                    <td><input type="text" name="preferencia_precio" id="preferencia_precio" value="<?php echo esc_attr($preferencia_precio); ?>" class="regular-text"></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar Preferencias"></p>
        </form>
<?php
        return ob_get_clean();
    }

    // Mostrar el formulario
    function swm_display_preferences_page()
    {
        // Verificar si el usuario está logueado
        if (is_user_logged_in()) {
            echo '<div class="swm-preferences-form">';
            echo SWM_User_Preferences::swm_render_preferences_form();
            echo '</div>';

            // Procesar el formulario cuando se envía
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
                $user_id = get_current_user_id();
                $this->swm_save_user_preference($user_id, 'preferencia_marca', sanitize_text_field($_POST['preferencia_marca']));
                $this->swm_save_user_preference($user_id, 'preferencia_forma_esfera', sanitize_text_field($_POST['preferencia_forma_esfera']));
                $this->swm_save_user_preference($user_id, 'preferencia_color_correa', sanitize_text_field($_POST['preferencia_color_correa']));
                $this->swm_save_user_preference($user_id, 'preferencia_material_correa', sanitize_text_field($_POST['preferencia_material_correa']));
                $this->swm_save_user_preference($user_id, 'preferencia_pantalla_tactil', sanitize_text_field($_POST['preferencia_pantalla_tactil']));
                $this->swm_save_user_preference($user_id, 'preferencia_vida_bateria', sanitize_text_field($_POST['preferencia_vida_bateria']));
                $this->swm_save_user_preference($user_id, 'preferencia_bluetooth', sanitize_text_field($_POST['preferencia_bluetooth']));
                $this->swm_save_user_preference($user_id, 'preferencia_tamano_pantalla', sanitize_text_field($_POST['preferencia_tamano_pantalla']));
                $this->swm_save_user_preference($user_id, 'preferencia_peso', sanitize_text_field($_POST['preferencia_peso']));
                $this->swm_save_user_preference($user_id, 'preferencia_precio', sanitize_text_field($_POST['preferencia_precio']));
            }
        } else {
            echo '<p>Debes iniciar sesión para acceder a tus preferencias de smartwatches.</p>';
        }
    }
}
