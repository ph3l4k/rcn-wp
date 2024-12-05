<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class RCN_Elementor_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'recursive_category_navigator';
    }

    public function get_title() {
        return __( 'Recursive Category Navigator', 'recursive-category-navigator' );
    }

    public function get_icon() {
        return 'eicon-product-categories';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'recursive-category-navigator' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'parent_category',
            [
                'label' => __( 'Parent Category', 'recursive-category-navigator' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '0',
                'options' => $this->get_product_categories(),
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __( 'Columns', 'recursive-category-navigator' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '4',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '6' => '6',
                ],
            ]
        );

        $this->add_control(
            'category_ids',
            [
                'label' => __( 'Filter by Category IDs', 'recursive-category-navigator' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Enter category IDs separated by commas. Leave empty to show all.', 'recursive-category-navigator' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $category_renderer = new RCN_Category_Renderer();
        echo $category_renderer->render_category_nav( $settings );
    }

    private function get_product_categories() {
        $categories = get_terms( 'product_cat', ['hide_empty' => false] );
        $options = ['0' => __( 'None (Show top-level)', 'recursive-category-navigator' )];
        foreach ( $categories as $category ) {
            $options[$category->term_id] = $category->name;
        }
        return $options;
    }
}

