<?php
// Clase para utilidades y funciones de ayuda
require_once 'user_preferences.php';
class SWM_Utils {
    // Función para convertir valores decimales a formato WooCommerce (1.2 -> 1-2)
    function swm_convert_decimal_to_woocommerce_format($value) {
        return str_replace(array('.', ','), '-', $value);
    }

    function swm_calculate_product_score($product, $user_preferences){
        $product_score = 0;
        if (!is_user_logged_in()){
            return $product_score;
        }
        $product_attributes = explode(', ', $product->attributes);
    
        // Compara cada atributo del producto con las preferencias del usuario y calcula la puntuación
        foreach ($product_attributes as $attribute) {
            list($value, $taxonomy) = explode(':', $attribute);
            $value = strtolower(trim($value));
            $taxonomy = strtolower(trim($taxonomy));
    
            error_log("Comparando atributo $taxonomy con valor $value contra la preferencia del usuario");
    
            // Modificar peso de cada categoría??
            switch ($taxonomy) {
                case 'pa_forma-de-la-esfera':
                    if (!empty($user_preferences['forma_esfera']) && $value == strtolower($user_preferences['forma_esfera'])) {
                            $product_score++;
                    }
                    break;
                case 'pa_color-de-la-correa':
                    if (!empty($user_preferences['color_correa']) && $value == strtolower($user_preferences['color_correa'])) {
                        $product_score++;
                    }
                    break;
                case 'pa_material-de-la-correa':
                    if (!empty($user_preferences['material_correa']) && $value == strtolower($user_preferences['material_correa'])) {
                        $product_score++;
                    }
                    break;
                case 'pa_pantalla-tactil':
                    if (!empty($user_preferences['pantalla_tactil']) && $value == strtolower($user_preferences['pantalla_tactil'])) {
                        $product_score++;
                    }
                    break;
                case 'pa_vida-de-la-bateria-dias':
                    if (!empty($user_preferences['vida_bateria']) && $value == SWM_Utils::swm_convert_decimal_to_woocommerce_format(strtolower($user_preferences['vida_bateria']))) {
                        $product_score++;
                    }
                    break;
                case 'pa_bluetooth':
                    if (!empty($user_preferences['bluetooth']) && $value == strtolower($user_preferences['bluetooth'])) {
                        $product_score++;
                    }
                    break;
                case 'pa_tamano-de-pantalla':
                    if (!empty($user_preferences['tamano_pantalla']) && $value == SWM_Utils::swm_convert_decimal_to_woocommerce_format(strtolower($user_preferences['tamano_pantalla']))) {
                        $product_score++;
                    }
                    break;
                case 'product_cat':
                    if (!empty($user_preferences['marca']) && $value == strtolower($user_preferences['marca'])) {
                        $product_score++;
                    }
                    break;
            }
        }
    
        // Comparar el peso (considerando un margen de tolerancia)
        if (!empty($user_preferences['peso']) && (abs(floatval($product->weight) - floatval($user_preferences['peso']))) < 0.1) {
            $product_score++;
        }
    
        // Comparar el precio (considerando un margen de tolerancia)
        if (!empty($user_preferences['precio']) && (abs(floatval($product->price) <= (floatval($user_preferences['precio']))) + 10)) {
            $product_score++;
        }
    
        return $product_score;
    }
        
    // Función para calcular la puntuación bayesiana
    function swm_calculate_bayesian_score($ratings) {
        $K = 5; // Número de posibles valoraciones
        $z = 1.96; // Cuantil para α=0.05
        $N = array_sum($ratings); // Número total de valoraciones

        if ($N == 0) {
            return 0; // Evitar división por cero si no hay valoraciones
        }

        $sum_sk_nk = 0;
        $sum_sk2_nk = 0;

        for ($k = 1; $k <= $K; $k++) {
            $sum_sk_nk += $k * ($ratings[$k - 1] + 1);
            $sum_sk2_nk += $k * $k * ($ratings[$k - 1] + 1);
        }

        $mean = ($sum_sk_nk) / ($N + $K);
        $variance = (($sum_sk2_nk) / ($N + $K)) - $mean ** 2;
        $standard_deviation = sqrt($variance);

        $bayes_score = $mean - ($z * $standard_deviation) / sqrt($N + $K + 1);

        return $bayes_score;
    }
}
