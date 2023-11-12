<?php
$categories = get_the_category($this->ID);
$tags = get_the_tags($this->ID);
$category_names = array();
if (!empty($categories)) {
    foreach( $categories as $category ) {
        $category_names[] = $category->name;
    }
}
$tag_names = array();
if (!empty($tags)) {
    foreach( $tags as $tag ) {
        $tag_names[] = $tag->name;
    }
}
?>
<?php if ( !empty($categories) ) : ?>
    <div class="amp-wp-meta amp-wp-tax-category">
        <?php printf( esc_html__( 'Categories: %s', 'videopro' ), implode( ', ', $category_names ) ); ?>
    </div>
<?php endif; ?>

<?php if ( !empty($tags) && ! is_wp_error( $tags ) ) : ?>
    <div class="amp-wp-meta amp-wp-tax-tag">
        <?php printf( esc_html__( 'Tags: %s', 'videopro' ), implode( ', ', $tag_names ) ); ?>
    </div>
<?php endif; ?>