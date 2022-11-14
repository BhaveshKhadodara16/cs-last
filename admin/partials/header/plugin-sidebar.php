<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$image_url = WDPAD_PRO_PLUGIN_URL . 'admin/images/right_click.png';
?>
</div>
<div class="fps-section-right">
    <div class="dots-seperator">
        <button class="toggleSidebar" title="toogle sidebar">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
        </button>
    </div>
<div class="dotstore_plugin_sidebar">
<?php 
    $review_url    = '';
    $plugin_at     = '';
    $changelog_url = '';
    $doc_url       = '';
    if ( wcdrfc_fs()->is__premium_only() ) {
        if ( wcdrfc_fs()->can_use_premium_code() ) {
            $review_url     = 'https://www.thedotstore.com/woocommerce-conditional-discount-rules-for-checkout/#tab-reviews';
            $plugin_at      = 'theDotstore';
            $changelog_url  = 'https://www.thedotstore.com/woocommerce-conditional-discount-rules-for-checkout#tab-update-log';
            $doc_url        = 'https://docs.thedotstore.com/category/323-premium-plugin-settings';
        } else {
            $review_url     = 'https://wordpress.org/plugins/woo-conditional-discount-rules-for-checkout/#reviews';
            $plugin_at      = 'WP.org';
            $changelog_url  = 'https://wordpress.org/plugins/woo-conditional-discount-rules-for-checkout/#developers';
            $doc_url        = 'https://docs.thedotstore.com/collection/318-conditional-discount-rules-for-woocommerce-checkout';
        }
    } else {
        $review_url     = 'https://wordpress.org/plugins/woo-conditional-discount-rules-for-checkout/#reviews';
        $plugin_at      = 'WP.org';
        $changelog_url  = 'https://wordpress.org/plugins/woo-conditional-discount-rules-for-checkout/#developers';
        $doc_url        = 'https://docs.thedotstore.com/collection/318-conditional-discount-rules-for-woocommerce-checkout';
    }
    ?>
    <?php
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
            }
        } else {
            ?>
            <div class="dotstore-sidebar-section dotstore-upgrade-to-pro">
                <div class="dotstore-important-link-heading">
                    <span class="dashicons dashicons-superhero-alt"></span>
                    <span class="heading-text"><?php esc_html_e('Upgrade to Conditional Discount Pro', 'woo-conditional-discount-rules-for-checkout'); ?></span>
                </div>
                <div class="dotstore-important-link-content">
                    <ul class="dotstore-pro-list">
                        <li><?php esc_html_e('Offer a range of discounts to enable better conversions', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Craft discounts on product price, to make a better impact', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Targeted discounts for customers make them feel special', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Push global discounts across the store', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Drive location-based sales by offering location-specific discounts', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Totally simplify process of dynamic pricing and discounts', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Seamlessly manage discounts and pricing', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Facilitate sustainable revenue generation', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                        <li><?php esc_html_e('Improve popularity of online store across target customer groups', 'woo-conditional-discount-rules-for-checkout'); ?></li>
                    </ul>
                    <div class="dotstore-pro-button">
                        <a class="button" target="_blank" href="<?php echo esc_url( 'https://bit.ly/39IhAEr' ); ?>"><?php esc_html_e('Get Premium Now »', 'woo-conditional-discount-rules-for-checkout'); ?></a>
                    </div>
                </div>
            </div>
            <?php
        }
    ?>
    <div class="dotstore-important-link dotstore-sidebar-section">
        <?php
            $review_url = '';
            $plugin_at  = '';
            if ( wcdrfc_fs()->is__premium_only() ) {
                if ( wcdrfc_fs()->can_use_premium_code() ) {
                    $review_url = esc_url( 'https://www.thedotstore.com/woocommerce-conditional-discount-rules-for-checkout/#tab-reviews' );
                    $plugin_at  = 'theDotstore';
                }
            } else {
                $review_url = esc_url( 'https://wordpress.org/plugins/woo-conditional-discount-rules-for-checkout/#reviews' );
                $plugin_at  = 'WP.org';
            }
        ?>
            <div class="content_box">
                <h3><?php esc_html_e( 'Like This Plugin?', 'woo-conditional-discount-rules-for-checkout' );?></h3>
                <div class="wcdrc-star-rating">
                    <input type="radio" id="5-stars" name="rating" value="5">
                    <label for="5-stars" class="star"></label>
                    <input type="radio" id="4-stars" name="rating" value="4">
                    <label for="4-stars" class="star"></label>
                    <input type="radio" id="3-stars" name="rating" value="3">
                    <label for="3-stars" class="star"></label>
                    <input type="radio" id="2-stars" name="rating" value="2">
                    <label for="2-stars" class="star"></label>
                    <input type="radio" id="1-star" name="rating" value="1">
                    <label for="1-star" class="star"></label>
                    <input type="hidden" id="wcdrc-review-url" value="<?php echo  esc_url( $review_url ) ;?>">
                </div>
                <p><?php esc_html_e( 'Your Review is very important to us as it helps us to grow more.', 'woo-conditional-discount-rules-for-checkout' );?></p>
            </div>
    </div>
	<!-- <div class="dotstore-important-link">
		<h2><span class="dotstore-important-link-title"><?php esc_html_e( 'Important link', 'woo-conditional-discount-rules-for-checkout' ); ?></span></h2>
		<div class="video-detail important-link">
			<ul>
				<li>
					<img src="<?php echo esc_url($image_url); ?>">
					<a target="_blank"
					   href="<?php echo esc_url('https://docs.thedotstore.com/collection/318-conditional-discount-rules-for-woocommerce-checkout');?>"> <?php esc_html_e( 'Plugin documentation', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
				</li>
				<li>
					<img src="<?php echo esc_url($image_url); ?>">
					<a target="_blank"
					   href="<?php echo esc_url('https://www.thedotstore.com/support/');?> "><?php esc_html_e( 'Support platform', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
				</li>
				<li>
					<img src="<?php echo esc_url($image_url); ?>">
					<a target="_blank"
					   href="<?php echo esc_url('https://www.thedotstore.com/feature-requests/');?>"><?php esc_html_e( 'Suggest A Feature', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
				</li>
				<li>
					<img src="<?php echo esc_url($image_url); ?>">
					<a target="_blank"
					   href="<?php echo esc_url('https://www.thedotstore.com/woocommerce-conditional-discount-rules-for-checkout#tab-change-log');?>"><?php esc_html_e( 'Changelog', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
				</li>
			</ul>
		</div>
	</div> -->
    <div class="dotstore-sidebar-section">
        <div class="dotstore-important-link-heading">
            <span class="dashicons dashicons-star-filled"></span>
            <span class="heading-text"><?php esc_html_e('Suggest A Feature', 'woo-conditional-discount-rules-for-checkout'); ?></span>
        </div>
        <div class="dotstore-important-link-content">
            <p><?php esc_html_e('Let us know how we can improve the plugin experience.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
            <p><?php esc_html_e('Do you have any feedback &amp; feature requests?', 'woo-conditional-discount-rules-for-checkout'); ?></p>
            <a target="_blank" href="<?php echo esc_url('https://www.thedotstore.com/suggest-a-feature'); ?>"><?php esc_html_e('Submit Request', 'woo-conditional-discount-rules-for-checkout'); ?> »</a>
        </div>
    </div>
    <div class="dotstore-sidebar-section">
        <div class="dotstore-important-link-heading">
            <span class="dashicons dashicons-editor-kitchensink"></span>
            <span class="heading-text"><?php esc_html_e('Changelog', 'woo-conditional-discount-rules-for-checkout'); ?></span>
        </div>
        <div class="dotstore-important-link-content">
            <p><?php esc_html_e('We improvise our products on a regular basis to deliver the best results to customer satisfaction.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
            <a target="_blank" href="<?php echo esc_url($changelog_url); ?>"><?php esc_html_e('Visit Here', 'woo-conditional-discount-rules-for-checkout'); ?> »</a>
        </div>
    </div>
    <div class="dotstore-sidebar-section">
        <div class="dotstore-important-link-heading">
            <span class="dashicons dashicons-sos"></span>
            <span class="heading-text"><?php esc_html_e('Five Star Support', 'woo-conditional-discount-rules-for-checkout'); ?></span>
        </div>
        <div class="dotstore-important-link-content">
            <p><?php esc_html_e('Got a question? Get in touch with theDotstore developers. We are happy to help!', 'woo-conditional-discount-rules-for-checkout'); ?></p>
            <a target="_blank" href="<?php echo esc_url('https://www.thedotstore.com/support/'); ?>"><?php esc_html_e('Submit a Ticket', 'woo-conditional-discount-rules-for-checkout'); ?> »</a>
        </div>
    </div>
    <div class="dotstore-sidebar-section">
        <div class="dotstore-important-link-heading">
            <span class="dashicons dashicons-media-document"></span>
            <span class="heading-text"><?php esc_html_e('Plugin documentation', 'woo-conditional-discount-rules-for-checkout'); ?></span>
        </div>
        <div class="dotstore-important-link-content">
            <p><?php esc_html_e('Please check our documentation for any type of help regarding this plugin.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
            <a target="_blank" href="<?php echo esc_url($doc_url); ?>"><?php esc_html_e('Checkout documentation', 'woo-conditional-discount-rules-for-checkout'); ?> »</a>
        </div>
    </div>

	<!-- html for popular plugin !-->
	<div class="dotstore-important-link dotstore-sidebar-section">
        <div class="dotstore-important-link-heading">
            <span class="dashicons dashicons-plugins-checked"></span>
            <span class="heading-text"><?php esc_html_e('Our Popular Plugins', 'woo-conditional-discount-rules-for-checkout'); ?></span>
        </div>
        <div class="video-detail important-link">
            <ul>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/Advanced-Flat-Rate-Shipping-Method.png' ); ?>" alt="<?php esc_attr_e( 'Conditional Product Fees For WooCommerce Checkout', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/flat-rate-shipping-plugin-for-woocommerce/" ); ?>">
                        <?php esc_html_e( 'Flat Rate Shipping Plugin For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/Conditional-Product-Fees-For-WooCommerce-Checkout.png' ); ?>" alt="<?php esc_attr_e( 'Conditional Product Fees For WooCommerce Checkout', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/product/woocommerce-extra-fees-plugin/" ); ?>">
                        <?php esc_html_e( 'Extra Fees Plugin for WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/plugn-login-128.png' ); ?>" alt="<?php esc_attr_e( 'Hide Shipping Method For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/hide-shipping-method-for-woocommerce/" ); ?>">
                        <?php esc_html_e( 'Hide Shipping Method For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/WooCommerce-Blocker-Prevent-Fake-Orders.png' ); ?>" alt="<?php esc_attr_e( 'WooCommerce Blocker – Prevent Fake Orders', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/woocommerce-anti-fraud" ); ?>">
                        <?php esc_html_e( 'WooCommerce Anti-Fraud', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/Advanced-Product-Size-Charts-for-WooCommerce.png' ); ?>" alt="<?php esc_attr_e( 'Product Size Charts Plugin For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/woocommerce-advanced-product-size-charts/" ); ?>">
                        <?php esc_html_e( 'Product Size Charts Plugin For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/wcbm-logo.png' ); ?>" alt="<?php esc_attr_e( 'WooCommerce Category Banner Management', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/product/woocommerce-category-banner-management/" ); ?>">
                        <?php esc_html_e( 'WooCommerce Category Banner Management', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'images/thedotstore-images/popular-plugins/woo-product-att-logo.png' ); ?>" alt="<?php esc_attr_e( 'Product Attachment For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>">
                    <a target="_blank" href="<?php echo esc_url( "https://www.thedotstore.com/woocommerce-product-attachment/" ); ?>">
                        <?php esc_html_e( 'Product Attachment For WooCommerce', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </a>
                </li>
                </br>
            </ul>
        </div>
        <div class="view-button">
            <a class="view_button_dotstore" href="<?php echo esc_url( "http://www.thedotstore.com/plugins/" ); ?>"  target="_blank"><?php esc_html_e( 'View All', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
        </div>
    </div>
	<!-- html end for popular plugin !-->
</div>
</div>
</div>
</div>