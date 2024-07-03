<?php
require_once 'utils.php';
// Clase para gestionar productos y recomendaciones
class SWM_Recommended_Products {    
    function swm_get_recommended_products($user_preferences, $filled_preferences_count) {
        global $wpdb;
    
        // Evitar la división por 0
        if ($filled_preferences_count == 0){
            $filled_preferences_count = 1;
        }

        $query = $wpdb->prepare("
        SELECT 
            p.ID as product_id, 
            p.post_title as product_name,
            MAX(CASE WHEN pm.meta_key = '_weight' THEN pm.meta_value ELSE NULL END) as weight,
            MAX(CASE WHEN pm.meta_key = '_price' THEN pm.meta_value ELSE NULL END) as price,
            MAX(CASE WHEN pm.meta_key = '_wc_average_rating' THEN pm.meta_value ELSE NULL END) as average_rating,
            MAX(CASE WHEN pm.meta_key = '_wc_review_count' THEN pm.meta_value ELSE NULL END) as review_count,
            GROUP_CONCAT(DISTINCT CONCAT(t.slug, ':', tt.taxonomy) SEPARATOR ', ') as attributes
        FROM 
            {$wpdb->posts} p
        JOIN 
            {$wpdb->postmeta} pm ON p.ID = pm.post_id
        LEFT JOIN 
            {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN 
            {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN 
            {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE 
            p.post_type = %s
            AND (tt.taxonomy LIKE %s OR tt.taxonomy = %s)
        GROUP BY 
            p.ID, p.post_title
        ", 'product', 'pa_%', 'product_cat');
    
        $products = $wpdb->get_results($query);
    
        $recommended_products = array();
        // Peso asignado a cada parte de la puntación
        $P = 0.5;
        $utils_instance = new SWM_Utils();
        // Hay que poner ambos scores en base 10 para que sean comparables
        foreach ($products as $product) {
            $ratings = SWM_Recommended_Products::swm_get_ratings($product->product_id);
            $product_score = $utils_instance->swm_calculate_product_score($product, $user_preferences)/$filled_preferences_count;
            $bayes_score = $utils_instance->swm_calculate_bayesian_score($ratings)/5;
            $score = $product_score * $P + $bayes_score * (1 - $P);
            $recommended_products[] = array(
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_score' => $product_score,
                'bayes_score' => $bayes_score,
                'score' => $score
            );
        }
    
        return $recommended_products;
    }
    
    function swm_get_ratings($product_id) {
        global $wpdb;

        $query = $wpdb->prepare("
        SELECT 
            cm.meta_value AS rating
        FROM 
            {$wpdb->prefix}posts p
        JOIN 
            {$wpdb->prefix}comments c ON p.ID = c.comment_post_ID
        JOIN 
            {$wpdb->prefix}commentmeta cm ON c.comment_ID = cm.comment_id AND cm.meta_key = 'rating'
        WHERE 
            p.ID = %d
            AND p.post_type = 'product'
            AND c.comment_type = 'review'
            AND c.comment_approved = 1
        ", $product_id);

        $results = $wpdb->get_results($query, ARRAY_A);

        // Inicializar el array de ratings con valores iniciales de 0 para cada rating del 1 al 5
        $ratings = array_fill(0, 5, 1);

        // Contar la cantidad de veces que se ha dado cada rating
        foreach ($results as $row) {
            $rating = intval($row['rating']);
            $ratings[$rating-1]++;
        }

        return $ratings;
    }
    
    function swm_display_recommended_products() {
        $user_id = get_current_user_id();
        $swm_user_preferences_instance = new SWM_User_Preferences();
    
        $user_preferences = array(
            'marca' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_marca', true),
            'forma_esfera' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_forma_esfera', true),
            'color_correa' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_color_correa', true),
            'material_correa' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_material_correa', true),
            'pantalla_tactil' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_pantalla_tactil', true),
            'vida_bateria' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_vida_bateria', true),
            'bluetooth' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_bluetooth', true),
            'tamano_pantalla' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_tamano_pantalla', true),
            'peso' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_peso', true),
            'precio' => $swm_user_preferences_instance::swm_get_user_preference($user_id, 'preferencia_precio', true)
        );
    
        // Contar cuántas preferencias están rellenas
        $filled_preferences_count = 0;
        foreach ($user_preferences as $preference) {
            if (!empty($preference)) {
                $filled_preferences_count++;
            }
        }

        $recommended_products = SWM_Recommended_Products::swm_get_recommended_products($user_preferences, $filled_preferences_count);
    
        // Ordenar productos por puntuación de coincidencia
        usort($recommended_products, function($a, $b) {
            return bccomp($b['score'], $a['score'], 14);
        });
        
        // Mostrar productos recomendados
        if (!empty($recommended_products)) {
            // Mostrar los 8 productos mejor valorados
            echo '<div class="productos-mejor-valorados">';
            for ($i = 0; $i < min(8, count($recommended_products)); $i++) {
                $product = $recommended_products[$i];
                $post = get_post($product['product_id']);
                $permalink = get_permalink($product['product_id']);
                $precio = get_post_meta($product['product_id'], '_price', true);
                $imagen_id = get_post_thumbnail_id($product['product_id']);
                $imagen_url = wp_get_attachment_url($imagen_id);
                $average_rating = get_post_meta($product['product_id'], '_wc_average_rating', true);
                $review_count = get_post_meta($product['product_id'], '_wc_review_count', true);
            
                echo '<div class="producto">';
                echo '<a href="' . esc_url($permalink) . '">';
                if ($imagen_url) {
                    echo '<img src="' . esc_url($imagen_url) . '" alt="' . esc_attr($post->post_title) . '">';
                }
                echo '<h3>' . esc_html($post->post_title) . '</h3>';
                echo '</a>';
                echo '<p>Precio: ' . esc_html($precio) . ' €</p>';
                echo '<p>Valoración: ' . esc_html($average_rating) . ' (' . esc_html($review_count) . ' reseñas)</p>';
                //echo '<p>Puntuación Producto: ' . esc_html($product['product_score']) . '</p>';
                //echo '<p>Puntuación Bayes: ' . esc_html($product['bayes_score']) . '</p>';
                //echo '<p>Puntuación: ' . esc_html($product['score']) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No se encontraron productos que coincidan con tus preferencias.</p>';
        }
    }    
}
