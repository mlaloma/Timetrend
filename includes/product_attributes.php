<?php
// Clase para gestionar los atributos del producto
class SWM_ProductAttributes {
    public static function swm_get_unique_attribute_values($taxonomy) {
        global $wpdb;

        $query = "SELECT DISTINCT name 
                  FROM {$wpdb->terms} t 
                  INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
                  WHERE tt.taxonomy = %s";

        $results = $wpdb->get_results($wpdb->prepare($query, $taxonomy));

        $values = array();
        foreach ($results as $result) {
            $values[] = $result->name;
        }

        return $values;
    }

    public static function swm_get_all_unique_attribute_values() {
        global $wpdb;

        $query_marcas = $wpdb->prepare("
            SELECT t.term_id, t.name
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = %s
            ", 'product_cat');
        $results_marcas = $wpdb->get_results($query_marcas);

        $marcas = array();
        foreach ($results_marcas as $marca) {
            $marcas[] = $marca->name;
        }

        $values = array(
            'marca' => $marcas,
            'forma_esfera' => self::swm_get_unique_attribute_values('pa_forma-de-la-esfera'),
            'color_correa' => self::swm_get_unique_attribute_values('pa_color-de-la-correa'),
            'material_correa' => self::swm_get_unique_attribute_values('pa_material-de-la-correa'),
            'pantalla_tactil' => array('Si', 'No'),
            'vida_bateria' => array(),
            'bluetooth' => array('Si', 'No'),
            'tamano_pantalla' => array(),
            'peso' => array(),
            'precio' => array()
        );

        return $values;
    }
}
