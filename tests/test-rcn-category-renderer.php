<?php
class Test_RCN_Category_Renderer extends WP_UnitTestCase {
    public function test_render_category_nav() {
        $renderer = new RCN_Category_Renderer();
        $output = $renderer->render_category_nav([]);
        $this->assertStringContainsString('rcn-category-navigator', $output);
    }

    public function test_category_has_subcategories() {
        $renderer = new RCN_Category_Renderer();
        $parent_category = $this->factory->term->create(['taxonomy' => 'product_cat']);
        $child_category = $this->factory->term->create(['taxonomy' => 'product_cat', 'parent' => $parent_category]);
        $this->assertTrue($renderer->category_has_subcategories($parent_category));
    }
}