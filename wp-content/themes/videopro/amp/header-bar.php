<header id="#top" class="amp-wp-header">
    <div>
        <a href="<?php echo esc_url( $this->get( 'home_url' ) ); ?>">
            <?php
            $logo = ot_get_option('logo_image','') == '' ? esc_url(get_template_directory_uri()) . '/images/logo.png' : ot_get_option('logo_image','');
            ?>
            <?php
            if ( $logo ) : ?>
                <amp-img src="<?php echo esc_url($logo); ?>" height="30" layout="fixed-height" class=""></amp-img>
            <?php else : ?>
                <?php echo esc_html( $this->get( 'blog_name' ) ); ?>
            <?php endif; ?>
        </a>
    </div>
</header>

