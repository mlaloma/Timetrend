<?php
/**
 * Plugin Name: Motor de recomendación de productos.
 * Description: Plugin para recopilar preferencias de usuario y construir perfiles para recomendaciones personalizadas de smartwatches.
 * Version: 1.0
 * Author: Miguel Laloma
 * License: GPL-2.0+
 */

/**
 * Clase principal del motor de recomendación de productos.
 * Esta clase adicionalmente ejecuta el endpoint del asistente virtual.
 */
class SWM_Main {
    public function __construct() {
        // Incluir archivos necesarios
        $this->includes();
        
        // Registrar estilos
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Registrar shortcodes
        add_shortcode('swm_preferences', array($this, 'preferences_shortcode'));
        add_shortcode('swm_recommendations', array($this, 'recommendations_shortcode'));
    }
    
    private function includes() {
        include_once plugin_dir_path(__FILE__) . 'includes/user_preferences.php';
        include_once plugin_dir_path(__FILE__) . 'includes/recommended_products.php';
        include_once plugin_dir_path(__FILE__) . 'chatbot/bot_endpoint.php';
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('swm-styles', plugins_url('css/styles.css', __FILE__));
        wp_enqueue_script('swm-scripts', plugins_url('js/scripts.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('swm-scripts', 'chatbot_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    }
    
    // Shortcode para preferencias
    public function preferences_shortcode() {
        ob_start();
        $preferences_instance = new SWM_User_Preferences();
        $preferences_instance->swm_display_preferences_page();
        return ob_get_clean();
    }
    
    // Shortcode para recomendaciones
    public function recommendations_shortcode() {
        ob_start();
        $recommendation_instance = new SWM_Recommended_Products();
        $recommendation_instance->swm_display_recommended_products();
        return ob_get_clean();
    }
}

// Instanciar la clase principal
$swm_main_instance = new SWM_Main();
