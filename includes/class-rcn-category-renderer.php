<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class RCN_Category_Renderer {
    public function render_category_nav( $atts ) {
        $defaults = array(
            'parent_id' => 0,
            'columns' => 4,
            'category_ids' => '',
        );
        $atts = wp_parse_args( $atts, $defaults );

        $args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true, // This will only get categories with products
            'parent' => $atts['parent_id']
        );

        if ( ! empty( $atts['category_ids'] ) ) {
            $args['include'] = explode( ',', $atts['category_ids'] );
            $args['parent'] = '';
        }

        $categories = get_terms( $args );

        if ( empty( $categories ) ) {
            return '';
        }

        ob_start();
        echo '<div class="rcn-category-navigator">';
        echo '<div class="rcn-category-grid rcn-columns-' . esc_attr( $atts['columns'] ) . '">';
        foreach ( $categories as $category ) {
            if ( $this->category_has_products( $category->term_id ) ) {
                $this->render_category_item( $category );
            }
        }
        echo '</div>';
        echo '</div>';
        return ob_get_clean();
    }

    private function render_category_item( $category ) {
        $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
        $image_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : RCN_PLUGIN_URL . 'assets/images/placeholder.png';
        $link = get_term_link( $category, 'product_cat' );

        echo '<div class="rcn-category-item">';
        echo '<a href="' . esc_url( $link ) . '">';
        echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $category->name ) . '">';
        echo '<h3>' . esc_html( $category->name ) . '</h3>';
        echo '</a>';
        echo '</div>';
    }

    public function category_has_products( $category_id ) {
        $args = array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category_id,
                    'include_children' => false,
                ),
            ),
            'posts_per_page' => 1,
        );

        $query = new WP_Query( $args );
        return $query->have_posts();
    }

    public function display_subcategories() {
        if ( is_product_category() ) {
            $current_category = get_queried_object();
            if ( $current_category && is_a( $current_category, 'WP_Term' ) && $current_category->taxonomy === 'product_cat' ) {
                echo $this->render_category_nav( array( 'parent_id' => $current_category->term_id ) );
            }
        }
    }

    public function filter_products_by_current_category( $query ) {
        if ( is_product_category() && $query->is_main_query() ) {
            $current_category = get_queried_object();
            $subcategories = get_terms( array(
                'taxonomy' => 'product_cat',
                'parent' => $current_category->term_id,
                'fields' => 'ids',
            ) );

            if ( ! empty( $subcategories ) ) {
                $query->set( 'tax_query', array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $current_category->term_id,
                        'include_children' => false,
                    ),
                ) );
            }
        }
        return $query;
    }
}

