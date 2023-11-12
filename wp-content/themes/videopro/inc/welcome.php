<?php
/**
 * cactus theme sample theme options file. This file is generated from Export feature in Option Tree.
 *
 * @package videopro
 */

//hook and redirect
function videopro_activation($oldname, $oldtheme=false) {
	//header( 'Location: '.admin_url().'admin.php?page=cactus-welcome');
	wp_redirect(admin_url().'themes.php?page=videopro-welcome');
}
add_action('after_switch_theme', 'videopro_activation', 10 ,  2);

//welcome menu
add_action('admin_menu', 'videopro_welcome_menu');
function videopro_welcome_menu() {
	add_theme_page(esc_html__('Welcome','videopro'), esc_html__('VideoPro Welcome','videopro'), 'edit_theme_options', 'videopro-welcome', 'videopro_welcome_function', 'dashicons-megaphone', '2.5');
}

//welcome page
function videopro_welcome_function(){
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome/css/font-awesome.min.css', array(), '4.3.0');
    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome';
    ?>
    <div class="wrap welcome-page">
        <?php videopro_welcome_tabs(); ?>
    </div>
    <div class="wrap">
        <?php videopro_welcome_tab_content( $tab ); ?>
    </div>
    <?php
}

function videopro_admin_enqueue_scripts() {
        wp_enqueue_style( 'videopro-adm-google-fonts', videopro_get_google_fonts_url(array('Poppins')), array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'videopro_admin_enqueue_scripts' );

//tabs
function videopro_welcome_tabs() {
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome';
	$cactus_welcome_tabs = array(
		'welcome' => '<span class="dashicons dashicons-smiley"></span> '.esc_html__('Welcome','videopro'),
		'document' => '<span class="dashicons dashicons-format-aside"></span> '.esc_html__('Document','videopro'),
		'sample' => '<span class="dashicons dashicons-download"></span> '.esc_html__('Sample Data','videopro'),
		'support' => '<span class="dashicons dashicons-businessman"></span> '.esc_html__('Support','videopro'),
	);

	echo '<h1></h1>';
    echo '<h2 class="nav-tab-wrapper">';
    echo '<ul>';
    foreach ( $cactus_welcome_tabs as $tab_key => $tab_caption ) {
        $active = $current_tab == $tab_key ? 'tab-active' : '';
        if ($tab_key == 'sample') {
            echo '<li class="' . $active . '"><a class="nav-tab" href="' . admin_url('tools.php?page=fw-backups-demo-content') . '">' . $tab_caption . '</a></li>';
        } else {
            echo '<li class="'.$active.'"><a class="nav-tab" href="?page=videopro-welcome&tab=' . $tab_key . '">' . $tab_caption . '</a></li>';
        }
    }
    echo '</ul>';
    echo '</h2>';
}
function videopro_welcome_tab_content( $tab ){
	if($tab == 'document'){ ?>
    	<p>You could view <a class="button button-primary button-large" href="http://videopro.cactusthemes.com/doc/" target="_blank">Full Document</a> in new window</p
    ><?php
	} elseif($tab == 'sample'){
    	if(!class_exists('cactus_demo_importer')){
			?>
			<p style="color:#FF0000"> <?php echo esc_html__('Please install VideoPro-SampleData plugin to use this feature','videopro');?> </p>
			<?php
		} else {
			do_action('videopro_import_data_tab');
		}
	} elseif($tab == 'support'){ ?>
    	<p>You could open <a class="button button-primary button-large" href="http://support.cactusthemes.com/" target="_blank">Support Ticket</a> in new window</p>
	<?php } else{ ?>
		<div class="cactus-welcome-message">
			<h2 class="cactus-welcome-title"><?php esc_html_e('Welcome to VideoPro - The Ultimate Video Solution for WordPress','videopro');?></h2>
            <div class="cactus-welcome-inner">
                <a class="cactus-welcome-item" href="?page=videopro-welcome&tab=document">
                	<i class="fas fa-book"></i>
                    <h3><?php echo esc_html__('Full Document','videopro'); ?></h3>
                    <p><?php echo esc_html__('See the full user guide for all VideoPro functions','videopro'); ?></p>
                </a>
                <a class="cactus-welcome-item" href="http://videopro.cactusthemes.com/doc/quickstart.html" target="_blank">
                    <i class="fas fa-rocket"></i>
                    <h3><?php echo esc_html__('Quick Start','videopro'); ?></h3>
                    <p><?php echo esc_html__('Simple guide to set up and start running with Theme in 5 minutes!','videopro'); ?></p>
                </a>
                <br />
                <a class="cactus-welcome-item" href="<?php echo admin_url('tools.php?page=fw-backups-demo-content'); ?>">
                	<i class="fas fa-download"></i>
                    <h3><?php echo esc_html__('Sample Data','videopro'); ?></h3>
                    <p><?php echo esc_html__('Import sample data to have homepage like our live DEMO','videopro'); ?></p>
                </a>
                <a class="cactus-welcome-item" href="?page=videopro-welcome&tab=support">
                	<i class="fas fa-user"></i>
                    <h3><?php echo esc_html__('Support','videopro'); ?></h3>
                    <p><?php echo esc_html__('Need support using the theme? We are here for you.','videopro'); ?></p>
                </a>
                <div class="cactus-welcome-item cactus-welcome-item-wide cactus-welcome-changelog">
                	<h3>Release Logs</h3>
                        <ul>
<li><span>VideoPro 2.3.2.4 - 2017/12/15</span><br/>
includes Cactus-Video
<ul>
	<li><span class="fixed">#Fix:</span> WooCommerce breadcrumbs is broken</li>
	<li><span class="fixed">#Fix:</span> CSS bugs</li>
	<li><span class="fixed">#Fix:</span> WooCommerce Product Lightbox doesn't work</li>
	<li><span class="fixed">#Fix:</span> Order By Most Viewed in Channel doesn't work properly</li>
	<li><span class="fixed">#Fix:</span> Auto-Next and Auto-Replay Video doesn't work properly</li>
	<li><span class="improved">#Update:</span> add a loading icon while downloading Video Screenshots when mouse is over the first image</li>
</ul>
</li>
<li><span>VideoPro 2.3.2.3 - 2017/11/25</span><br/>
includes Visual Composer 5.4.5
<ul>
	<li><span class="new">#Add:</span> option to choose Login Redirect page</li>
	<li><span class="fixed">#Fix:</span> Mega Menu display issues</li>
	<li><span class="fixed">#Fix:</span> channel avatar height</li>
	<li><span class="fixed">#Fix:</span> AMP doesn't spport inline CSS</li>
	<li><span class="fixed">#Fix:</span> an empty p tag appears in Author Description in WP 4.9</li>
</ul>
</li>
<li><span>VideoPro 2.3.2.2 - 2017/11/11</span><br/>
includes Cactus-Video 2.3.2.1;
<ul>
	<li><span class="fixed">#Fix:</span> Custom Player shortcode and Elite play feature does not work</li>
</ul>
</li>
<li><span>VideoPro 2.3.2.1 - 2017/11/10</span><br/>
includes Cactus-Video 2.3.2.1;
<ul>
	<li><span class="fixed">#Fix:</span> unable to play YouTube videos</li>
</ul>
</li>
<li><span>VideoPro 2.3.2 - 2017/11/09</span><br/>
includes Cactus-Video 2.3.2; Cactus-Actor 1.1.3; Cactus-Rating 1.2.1; Visual Composer 5.4.2;
<ul>
	<li><span class="improved">#Update:</span> Support MyCred plugin with Sell & Buy Addon</li>
	<li><span class="improved">#Update:</span> Support Indeed Ultimate Membership Pro plugin</li>
	<li><span class="improved">#Update:</span> Support Quform - require VideoPro Quform Add-on (premium)</li>
	<li><span class="fixed">#Fix:</span> using Player Logic with Header Image option</li>
	<li><span class="fixed">#Fix:</span> some warning notices</li>
	<li><span class="fixed">#Fix:</span> incorrect video metadata in embed feature</li>
	<li><span class="fixed">#Fix:</span> using Embed Code in Multi-links when main video is URL or File</li>
</ul>
</li>
<li><span>VideoPro 2.3.1 - 2017/10/24</span><br/>
includes Cactus-Video 2.3.1; VideoPro-Shortcodes 1.3.4.1; Cactus-Actor 1.1.2; Cactus-Rating 1.2.0.1; Visual Composer 5.4
<ul>
	<li><span class="new">#Add:</span> Auto Synchronize YouTube & Vimeo View Count option</li>
	<li><span class="fixed">#Fix:</span> fatal error with PHP 5.4</li>
	<li><span class="fixed">#Fix:</span> "order by views" (using Top 10 plugin) does not work correctly</li>
	<li><span class="fixed">#Fix:</span> Embed Facebook Video with Vertical ratio does not display properly</li>
</ul>
</li>
<li><span>VideoPro 2.3 - 2017/10/11</span><br/>
includes Cactus-Video 2.3; VideoPro-Shortcodes 1.3.4; Cactus-Actor 1.1.1; Visual Composer 5.3
<ul>
	<li><span class="new">#Add:</span> option to use Custom Video Player shortcode. Work with any Video Player that uses shortcode (ex: bzplayer Pro - not officially https://codecanyon.net/item/bzplayer-pro-live-streaming-player/20496418)</li>
	<li><span class="new">#Add:</span> option to change video order in Playlist</li>
	<li><span class="new">#Add:</span> support Elite Player (https://codecanyon.net/item/elite-video-player-wordpress-plugin/10496434)</li>
	<li><span class="improved">#Update:</span> support Multi-links for Video File</li>
	<li><span class="improved">#Update:</span> Menu Visibility Logic works with mobile menu</li>
	<li><span class="improved">#Update:</span> Sample Data</li>
	<li><span class="fixed">#Fix:</span> Layout is broken when using Top Content Sidebar in some pages</li>
	<li><span class="fixed">#Fix:</span> Layout issues of Posts Slider layout 8 shortcode</li>
	<li><span class="fixed">#Fix:</span> Video Extensions admin link appears in admin bar for all user roles</li>
	<li><span class="fixed">#Fix:</span> Auto Next video feature in playlist stops when screensize is small</li>
	<li><span class="fixed">#Fix:</span> remove autoplay option for Daily Motion AMP Video page</li>
	<li><span class="fixed">#Fix:</span> Layout issues of LightBox player when using Video File with different players</li>
	<li><span class="fixed">#Fix:</span> Google Structure Data validation</li>
</ul>
</li>
<li><span>VideoPro 2.2.1 - 2017/09/18</span><br/>
includes Cactus-Video 2.2.1; VideoPro-Shortcodes 1.3.3
<ul>
	<li><span class="fixed">#Fix:</span>Sidebar setting for Watch Later page template doesn't work</li>
	<li><span class="fixed">#Fix:</span>CSS issue when using Thumbnail Image header and JW Player for video</li>
	<li><span class="fixed">#Fix:</span>"Add To Watch Later" button doesn't work when posts are ajax-loaded</li>
	<li><span class="fixed">#Fix:</span>Avatar size issue in BuddyPress 2.9.1</li>
	<li><span class="improved">#Update:</span>Improve AMP template, add related posts links and fix RTL issue</li>
</ul>
</li>
<li><span>VideoPro 2.2 - 2017/08/22</span><br/>
includes Cactus-Video 2.2; VideoPro-Shortcodes 1.3.2; Visual Composer 5.2.1
<ul>
	<li><span class="new">#Add:</span> Video & Post Page Template</li>
	<li><span class="new">#Add:</span> support Google AMP for Single Post. Check <a href="http://videopro.cactusthemes.com/doc/docs/videopro-google-amp-compatibility/">Doc</a></li>
	<li><span class="new">#Add:</span> support Facebook Instant Articles for Single Post. Check <a href="http://videopro.cactusthemes.com/doc/docs/videopro-2-0-features/facebook-instant-article-support/">Doc</a></li>
	<li><span class="new">#Add:</span> option to redirect users to the new post after submitting</li>
	<li><span class="improved">#Update:</span> only load video screenshots when mouse hovers on, improve loading performance</li>
	<li><span class="fixed">#Fix:</span> conflict with Advanced Custom Fields Pro</li>
	<li><span class="fixed">#Fix:</span> layout issues with BuddyPress 2.9.0</li>
	<li><span class="fixed">#Fix:</span> backward-compatible with PHP 5.3</li>
	<li><span class="fixed">#Fix:</span> Advance Search Form does not work properly with custom post types</li>
	<li><span class="fixed">#Fix:</span> remove BuddyPress notification doesn't work</li>
	<li><span class="fixed">#Fix:</span> Ajax-pagination does not work properly when ordering by like</li>
</ul>
</li>
<li><span>VideoPro 2.1.1 - 2017/07/04</span><br/>
includes Cactus-Video 2.1.1; VideoPro-Shortcodes 1.3.1;
<ul>
	<li><span class="improved">#Update:</span> option to order by Most Viewed for Actors, Playlists and Channels using SCB shortcode</li>
	<li><span class="improved">#Update:</span> option to turn on/off auto-paragraph feature in new Text Widget WP 4.8, support using shortcodes inside the widget</li>
	<li><span class="improved">#Update:</span> add notifications to subscribers when posts are added from back-end</li>
	<li><span class="fixed">#Fix:</span> duplicated right side-ad in video post that has Video Header set to Thumbnail Image</li>
	<li><span class="fixed">#Fix:</span> order posts by Like in channel</li>
	<li><span class="fixed">#Fix:</span> option to use JWPlayer 7 settings</li>
	<li><span class="fixed">#Fix:</span> minor bugs</li>
</ul>
</li>
<li><span>VideoPro 2.1 - 2017/05/29</span><br/>
includes Cactus-Video 2.1; VideoPro-Shortcodes 1.3; Cactus-Actor 1.1
	<ul>
		<li><span class="new">#Add:</span> SCB shortcode to support Channels, Playlists and Actors post types</li>
		<li><span class="new">#Add:</span> Posts Slider shortcode to support Channels, Playlists and Actors post types</li>
		<li><span class="new">#Add:</span> Video Embed Code sharing feature</li>
		<li><span class="new">#Add:</span> option to change Movie Series list into select box</li>
		<li><span class="new">#Add:</span> add Preloading feature</li>
		<li><span class="new">#Add:</span> add new Actors when creating new video</li>
		<li><span class="improved">#Update:</span> toggle JW Player settings on post-basis</li>
		<li><span class="improved">#Update:</span> support YouTube link with Start param</li>
		<li><span class="improved">#Update:</span> compatible with Advanced Custom Fields plugin</li>
		<li><span class="improved">#Update:</span> support playing video on Facebook if using embed code</li>
		<li><span class="improved">#Update:</span> compatible with Give (donation) plugin</li>
		<li><span class="improved">#Update:</span> compatible with WPDiscuz plugin</li>
		<li><span class="improved">#Update:</span> add User Avatar to login menu</li>
		<li><span class="improved">#Update:</span> check plugin versions to ensure theme-compatibility</li>
		<li><span class="fixed">#Fix:</span> Error with BuddyPress if Notifications component is turned off</li>
		<li><span class="fixed">#Fix:</span> conflict with latest version of JW Player and light-box feature</li>
		<li><span class="fixed">#Fix:</span> cannot go to Edit Video page if a video has an Ad</li>
		<li><span class="fixed">#Fix:</span> SCB shortcode cannot list videos after Bulk Edit Video in admin</li>
		<li><span class="fixed">#Fix:</span> default language of Google+ button</li>
		<li><span class="fixed">#Fix:</span> order by Input ID doesn't work in shortcodes</li>
		<li><span class="fixed">#Fix:</span> multi-links feature issues with Thumbnail Image header</li>
		<li><span class="fixed">#Fix:</span> duplicated posts in category page if choosing order by title</li>
		<li><span class="fixed">#Fix:</span> setting for ordering items in More Videos doesn't work correctly</li>
	</ul>
</li>
<li><span>VideoPro 2.0.9 - 2017/05/03</span><br/>
includes Cactus-Video 2.0.9; VideoPro-Shortcodes 1.2.1.2;
	<ul>
		<li><span class="new">#Add:</span> option to use all features of JW Player 7 for WP plugin premium, including HD Toggle or Video Playlist, Video Sub Title</li>
		<li><span class="new">#Add:</span> option to change Player Icon animation effect of Image Header mode</li>
		<li><span class="improved">#Update:</span> show "no-data" message in Video Post submission form if there isn't any categories, channels or playlists</li>
		<li><span class="fixed">#Fix:</span> Warning messages and deprecated functions</li>
		<li><span class="fixed">#Fix:</span> Edit Video button link does not appear when using Image Header mode</li>
		<li><span class="fixed">#Fix:</span> Option to limit number of channels allowed to create does not work</li>
		<li><span class="fixed">#Fix:</span> Multi-link feature when using Image Header mode and https issue</li>
		<li><span class="fixed">#Fix:</span> Player Light on/off feature issue when using Image Header mode</li>
	</ul>
</li>
<li><span>VideoPro 2.0.8 - 2017/04/12</span><br/>
includes Cactus-Video 2.0.7.1; VideoPro-Shortcodes 1.2.1.1; Cactus-Actor 1.0.4.6; Visual Composer 5.1.1
	<ul>
		<li><span class="improved">#Update:</span> Support WooCommerce 3.0.1</li>
		<li><span class="improved">#Update:</span> Option to upload small category thumbnail image</li>
		<li><span class="fixed">#Fix:</span> Warning messages and deprecated functions</li>
		<li><span class="fixed">#Fix:</span> Incorrect datetime in Posts Slider layout 7</li>
		<li><span class="fixed">#Fix:</span> Cannot edit video (from front-end) if permalink structure is not %postname%</li>
		<li><span class="fixed">#Fix:</span> unable to click Save in the first time when adding shortcode (in VC Editor)</li>
		<li><span class="fixed">#Fix:</span> Number of subscribers is always 0 in Channels Listing Compact Mode</li>
	</ul>
</li>
<li><span>VideoPro 2.0.7 - 2017/03/23</span><br/>
includes Cactus-Video 2.0.7; VideoPro-Shortcodes 1.2.1; Visual Composer 5.1
	<ul>
		<li><span class="new">#Add:</span> Option to turn off Channel Subscription</li>
		<li><span class="new">#Add:</span> Posts Slider can display Actors</li>
		<li><span class="new">#Add:</span> Option to only show Channels and Playlists of current uploader in Submission Form</li>
		<li><span class="improved">#Update:</span> Improve UX on mobile - Touch to scroll will not trigger openning single post anymore</li>
		<li><span class="improved">#Update:</span> compatible with Everlight Photo Gallery plugin</li>
		<li><span class="improved">#Update:</span> Visual Composer 5.1</li>
		<li><span class="fixed">#Fix:</span> JW Player doesnot work when using Image Header option for video post</li>
		<li><span class="fixed">#Fix:</span> BuddyPress Notification Bubble shows info for all visitors in profile page</li>
		<li><span class="fixed">#Fix:</span> Video Edit page layout breaks at screen width 1281-1366</li>
	</ul>
</li>
<li><span>VideoPro 2.0.6 - 2017/03/06</span><br/>
includes Cactus-Video 2.0.6; VideoPro-Shortcodes 1.2.0.5; Cactus-Ads 2.5.4.5; VideoPro-Child-Theme-Poster 1.0.1
<ul>
	<li><span class="fixed">#Fix:</span> Statistics of HTML ads does not work</li>
	<li><span class="fixed">#Fix:</span> Margin Top of main body in Category page is too big when it does not have thumbnail</li>
	<li><span class="fixed">#Fix:</span> Mega Menu works with Tag menu item</li>
	<li><span class="fixed">#Fix:</span> Child Theme Poster Size - size of post thumbnails in Related Posts section</li>
	<li><span class="improved">#Update:</span> Quick & Bulk Edit Post Channels and Playlists</li>
</ul>
</li>
<li><span>VideoPro 2.0.5.6 - 2017/02/06</span><br/>
includes Cactus-Video 2.0.5.7; Cactus-Ads 2.5.4.3
<ul>
	<li>#Fix: Cover image in BuddyPress profile page does not show</li>
	<li>#Fix: cannot open full-screen Facebook Video</li>
	<li>#Fix: warning errors in Membership sign up page</li>
	<li>#Fix: Video Ads doesn't show up when Video Header option is Thumbnail Image</li>
	<li>#Fix: HTML iframe Ads doesn't show up (should not use a Video Iframe in Video Ads)</li>
	<li>#Fix: option to show Post Likes button to be separated from Social Sharing option</li>
	<li>#Fix: cannot change Video Player Background Color</li>
	<li>#Add: option to show BuddyPress notification bubble on header (in Theme Options > BuddyPress)</li>
</ul>
</li>
<li><span>VideoPro 2.0.5.5 - 2017/02/06</span><br/>
includes Cactus-Video 2.0.5.6; VideoPro-Shortcodes 1.2.0.5;
<ul>
	<li>#Fix: unable to open Lightbox Player on mobile</li>
	<li>#Fix: sidebar setting for Shop page</li>
	<li>#Fix: using incorrect self-hosted video player for Posts Slider with inline player</li>
	<li>#Fix: Channels and Playlists does not list all in video Submit Form</li>
	<li>#Fix: css issues</li>
	<li>#Update: language file</li>
</ul>
</li>
<li><span>VideoPro 2.0.5.4 - 2017/01/25</span><br/>
includes Cactus-Video 2.0.5.5; VideoPro-Shortcodes 1.2.0.4;
<ul>
    <li>#Fix: Layout Switch Tool in category page doesn't work properly</li>
    <li>#Fix: draft posts appear in Category page</li>
    <li>#Fix: JW Player 7 issues with Posts Slider shortcode using inline and lightbox player mode</li>
    <li>#Update: support Autoplay feature of JW Player 7 Premium version</li>
    <li>#Update: add Gallery and Audio icon for appropriate post formats</li>
</ul>
</li>
                        <li><span>VideoPro 2.0.5.3 - 2016/12/29</span><br/>
includes Cactus-Video 2.0.5.4; Cactus-Actor 1.0.4.5
<ul>
    <li>#Update: only admin can mark an user "verified"</li>
    <li>#Update: support Video Resolution toggle for JW Player 7</li>
    <li>#Fix: missing "Search Results for..." in breadcrumbs</li>
    <li>#Fix: translate text in admin of Cactus Actor plugin</li>
    <li>#Fix: Ajax loading issue in single Playlist</li>
</ul>
</li>
                        <li><span>VideoPro 2.0.5.2 - 2016/12/24</span><br/>
includes Cactus-Video 2.0.5.3; Advance-Search-Form 1.4.9.6; VideoPro-SampleData 1.3
<ul>
    <li>#Fix: issues with Force Using JWPlayer for YouTube videos</li>
    <li>#Fix: incorrect Video Duration when using YouTube WP Plugin to import videos</li>
    <li>#Fix: require choosing Categories in Contact Form 7 - Submission form</li>
    <li>#Fix: incorrect next/prev videos when time format is not standard</li>
    <li>#Update: sample data for V2</li>
    <li>#Update: lazy load background image</li>
    <li>#Update: sanitize search input value to security</li>
    <li>#Update: show Like/Dislike buttons for standard posts</li>
</ul>
</li>
                            <li><span>VideoPro 2.0.5.1 - 2016/12/15</span><br/>
includes Cactus-Video 2.0.5.2;
<ul>
    <li>#Fix: link to comments section</li>
    <li>#Fix: conflict with Captcha field in Post Submission form</li>
    <li>#Fix: bulk edit - adding videos to series</li>
    <li>#Fix: ajax pagination in Channels Listing page - WP 4.7 issue</li>
    <li>#Update: support Contact Form 4.6</li>
    <li>#Add: option to set height for popup form</li>
    <li>#Add: option to close popup form when clicking outside content</li>
</ul>
</li>
<li><span>VideoPro 2.0.5 - 2016/12/09</span><br/>
includes Cactus-Video 2.0.5; Cactus-Actor 1.0.4.3
<ul>
    <li>#Fix: Carousel Slider in RTL on IE browser</li>
    <li>#Fix: auto-next feature for YouTube videos</li>
    <li>#Fix: various issues</li>
    <li>#Fix: cannot close Floating Video if Video Header is Thumbnail</li>
    <li>#Fix: RTL issues</li>
    <li>#Update: correct comments link when Disqus is used</li>
    <li>#Update: show views, likes, comments count in Popular Posts Widget based on condition</li>
    <li>#Update: WordPress 4.7 support</li>
    <li>#Add: Channel Thumbnail option, to upload Channel Thumbnail</li>
    <li>#Add: option to print out video meta tags, support Google Search Results</li>
    <li>#Add: option to mark Channel, Actor and Author "verified"</li>
</ul>
</li>
<li><span>VideoPro 2.0.4 - 2016/12/06</span><br/>
includes Cactus-Video 2.0.4;
<ul>
    <li>#Fix: adding videos to series using Quick Edit doesn't work</li>
    <li>#Fix: Twitter meta tags</li>
    <li>#Fix: drag and drop file in Front-end Post Submission, using GravityForms</li>
    <li>#Fix: do not refresh after uploading videos</li>
    <li>#Update: support Category, Playlist, Channel dropdown in GravityForms</li>
    <li>#Update: order of videos in Actor and Playlist page changes to DESC</li>
    <li>#Update: support deLucks SEO plugin</li>
    <li>#Update: languge file</li>
</ul>
</li>
<li><span>VideoPro 2.0.3 - 2016/11/30</span><br/>
includes Cactus-Video 2.0.3; VideoPro-Shortcodes 1.2.0.3; Visual Composer 5.0.1
<ul>
    <li>#Fix: option to hide view count doesn't work in standard posts</li>
    <li>#Fix: param Column of Authors Listing shortcode doesn't work</li>
    <li>#Fix: Ajax pagination doesn' work in Channel and Author pages</li>
    <li>#Fix: "Agreement" checkbox in Creating Channel form doesn't require</li>
    <li>#Update: Option to turn on/off Public Profile menu item</li>
    <li>#Update: Option to add Register menu item</li>
    <li>#Update: language files</li>
</ul>
</li>
                            <li><span>VideoPro 2.0.2 - 2016/11/21</span><br/>
                                includes Cactus-Video 2.0.2; VideoPro-Shortcodes 1.2.0.2; Cactus-Ads 2.5.4.1; Advance Search Form 1.4.9.4
                                <ul>
                                    <li>#Fix: incorrect Back link when going to Edit Video page from Public Profile</li>
                                    <li>#Fix: Tag suggestion of Advance Search Form</li>
                                    <li>#Fix: option to hide comments count in Single Video page</li>
                                    <li>#Fix: when creating first playlist in channel, it does not appear</li>
                                    <li>#Fix: missing Edit Playlist button</li>
                                    <li>#Fix: https issue with Google Reponsive Ad shortcode</li>
                                    <li>#Update: add Delete link in Edit Popup of Channel and Playlist for quick action</li>
                                    <li>#Update: improve RTL and language files</li>
                                    <li>#Update: able to select Ads from dropdown</li>
                                    <li>#Update: option to change Categories, Channels, Playlists checkbox and radio group into Dropdown</li>
                                    <li>#Update: add option to Force Using JWPlayer for YouTube videos</li>
                                    <li>#Update: add filter to change number of videos in single actor page (http://bit.ly/2g9Ojqt)</li>
                                    <li>#Update: support hierarchical layout in Categories widget</li>
                                    <li>#Update: add option to set default status for User-Generated Content in Video Extensions > Membership Settings</li>
                                </ul>
                            </li>
                            <li><span>VideoPro 2.0.1 - 2016/11/11</span><br/>
                            includes Cactus-Video 2.0.1; VideoPro-Shortcodes 1.2.0.1
                            <ul>
                                <li>#Fix: checking incorrect post format in SCB shortcodes</li>
                                <li>#Fix: set default value for Delay Before Auto-Next</li>
                                <li>#Update: option to disable Video Ads for a specific Membership</li>
                                <li>#Update: support entering Video Duration for self-hosted videos</li>
                                <li>#Update: Visual Composer 5.0</li>
                                <li>#Update: language files of plugins</li>
                            </ul>
                            </li>
                            <li><span>VideoPro 2.0 - 2016/11/07</span></br/>
                            includes Cactus-Videos 2.0;  VideoPro-Shortcodes 1.2; Cactus-Actors 1.1;
                            <ul>
                            <li>#Fix: issues with Video Duration</li>
                            <li>#Fix: Watched Later template</li>
                            <li>#Fix: various bugs</li>
                            <li>#Update: adding Twitter Metadata</li>
                            <li>#Update: adding Customg Social Accounts for Actor</li>
                            <li>#Update: support Short YouTube URL (youtu.be)</li>
                            <li>#Update: adding Random condition for SCB shortcode</li>
                            <li>#Add: Membership features</li>
                            <li>#Add: support WPMU Membership 2 plugin</li>
                            <li>#Add: support BuddyPress</li>
                            </ul>
                            </li>
                            <li>Full release logs: <a href="http://videopro.cactusthemes.com/release_log.html" target="_blank">Release Logs</a></li>
                        </ul>
                </div>
            </div>
		</div>
	<?php }
}


//old import sample data
add_action( 'admin_notices', 'videopro_print_current_version_msg' );
function videopro_print_current_version_msg()
{
	$current_theme = wp_get_theme('videopro');
	$current_version =  $current_theme->get('Version');

    // check child theme version
    $child_theme = wp_get_theme();
    if($child_theme->get('Name') != $current_theme->get('Name')){
        $current_version .= '. ' . $child_theme->get('Name') . ' ver.' . $child_theme->get('Version');
    }
	echo '<div style="display:none" id="current_version">' . $current_version . '</div>';
}

add_action( 'admin_footer', 'videopro_import_sample_data_comfirm' );
function videopro_import_sample_data_comfirm()
{
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#ct_support_forum').parent().attr('target','_blank');
        $('#ct_documentaion').parent().attr('target','_blank');
        $('#option-tree-sub-header').append('<span class="option-tree-ui-button left text">VideoPro</span><span class="option-tree-ui-button left vesion ">ver. ' + $('#current_version').text() + '</span>');
    });
    </script>
    <?php
}
