<?php
/**
 * WCFM plugin view
 *
 * WCFM Profile View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.2.5
 */

global $WCFM;

$user_id = get_current_user_id();

$first_name = get_user_meta( $user_id, 'first_name', true );
$last_name  = get_user_meta( $user_id, 'last_name', true );
$email  = get_user_meta( $user_id, 'billing_email_address', true );
$phone  = get_user_meta( $user_id, 'billing_phone', true );
$about  = get_user_meta( $user_id, 'description', true );

$bfirst_name = get_user_meta( $user_id, 'billing_first_name', true );
$blast_name  = get_user_meta( $user_id, 'billing_last_name', true );
$baddr_1  = get_user_meta( $user_id, 'billing_address_1', true );
$baddr_2  = get_user_meta( $user_id, 'billing_address_2', true );
$bcountry  = get_user_meta( $user_id, 'billing_country', true );
$bcity  = get_user_meta( $user_id, 'billing_city', true );
$bstate  = get_user_meta( $user_id, 'billing_state', true );
$bzip  = get_user_meta( $user_id, 'billing_postcode', true );

$sfirst_name = get_user_meta( $user_id, 'shipping_first_name', true );
$slast_name  = get_user_meta( $user_id, 'shipping_last_name', true );
$saddr_1  = get_user_meta( $user_id, 'shipping_address_1', true );
$saddr_2  = get_user_meta( $user_id, 'shipping_address_2', true );
$scountry  = get_user_meta( $user_id, 'shipping_country', true );
$scity  = get_user_meta( $user_id, 'shipping_city', true );
$sstate  = get_user_meta( $user_id, 'shipping_state', true );
$szip  = get_user_meta( $user_id, 'shipping_postcode', true );


$is_marketplece = wcfm_is_marketplace();

if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
	if( wcfm_is_vendor() ) {
		if( $is_marketplece == 'wcvendors' )  {
			$twitter = get_user_meta( $user_id, '_wcv_twitter_username', true );
			$facebook = get_user_meta( $user_id, '_wcv_facebook_url', true );
			$instagram = get_user_meta( $user_id, '_wcv_instagram_username', true );
			$youtube = get_user_meta( $user_id, '_wcv_youtube_url', true );
			$linkdin = get_user_meta( $user_id, '_wcv_linkedin_url', true );
			$google_plus = get_user_meta( $user_id, '_wcv_googleplus_url', true );
			$snapchat = get_user_meta( $user_id, '_wcv_snapchat_username', true );
			$pinterest = get_user_meta( $user_id, '_wcv_pinterest_url', true );
		} elseif( $is_marketplece == 'wcmarketplace' )  {
			$twitter = get_user_meta( $user_id, '_vendor_twitter_profile', true );
			$facebook = get_user_meta( $user_id, '_vendor_fb_profile', true );
			$instagram = get_user_meta( $user_id, '_vendor_instagram', true );
			$youtube = get_user_meta( $user_id, '_vendor_youtube', true );
			$linkdin = get_user_meta( $user_id, '_vendor_linkdin_profile', true );
			$google_plus = get_user_meta( $user_id, '_vendor_google_plus_profile', true );
			$snapchat = get_user_meta( $user_id, '_vendor_snapchat', true );
			$pinterest = get_user_meta( $user_id, '_vendor_pinterest', true );
		} else {	
			$twitter = get_user_meta( $user_id, '_twitter_profile', true );
			$facebook = get_user_meta( $user_id, '_fb_profile', true );
			$instagram = get_user_meta( $user_id, '_instagram', true );
			$youtube = get_user_meta( $user_id, '_youtube', true );
			$linkdin = get_user_meta( $user_id, '_linkdin_profile', true );
			$google_plus = get_user_meta( $user_id, '_google_plus_profile', true );
			$snapchat = get_user_meta( $user_id, '_snapchat', true );
			$pinterest = get_user_meta( $user_id, '_pinterest', true );
		}
	} else {	
		$twitter = get_user_meta( $user_id, '_twitter_profile', true );
		$facebook = get_user_meta( $user_id, '_fb_profile', true );
		$instagram = get_user_meta( $user_id, '_instagram', true );
		$youtube = get_user_meta( $user_id, '_youtube', true );
		$linkdin = get_user_meta( $user_id, '_linkdin_profile', true );
		$google_plus = get_user_meta( $user_id, '_google_plus_profile', true );
		$snapchat = get_user_meta( $user_id, '_snapchat', true );
		$pinterest = get_user_meta( $user_id, '_pinterest', true );
	}
}

?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-user-circle"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Profile', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_wcvendors_profile' ); ?>
		<form id="wcfm_profile_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_wcvendors_profile_form' ); ?>
			
			<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_profile_personal_head">
				  <label class="fa fa-user"></label>
					<?php _e('Personal', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_profile_personal_expander" class="wcfm-content">
					  <?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_billing', array(
																																																"first_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $first_name ),
																																																"last_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $last_name ),
																																																"email" => array('label' => __('Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $email ),
																																																"phone" => array('label' => __('Phone', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $phone ),
																																																"about" => array('label' => __('About', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $about ),
																																																) ) );
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
				<!-- end collapsible -->
				
				<!-- collapsible -->
				<?php if( $wcfm_is_allow_address_profile = apply_filters( 'wcfm_is_allow_address_profile', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_profile_address_head">
						<label class="fa fa-address-card-o"></label>
						<?php _e('Address', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_profile_address_expander" class="wcfm-content">
							<div class="wcfm_profile_heading"><h3><?php _e( 'Billing', 'wc-frontend-manager' ); ?></h3></div>
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_billing', array(
																																																	"bfirst_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bfirst_name ),
																																																	"blast_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $blast_name ),
																																																	"baddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $baddr_1 ),
																																																	"baddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $baddr_2 ),
																																																	"bcountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $bcountry ),
																																																	"bcity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bcity ),
																																																	"bstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bstate ),
																																																	"bzip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bzip ),
																																																	) ) );
							?>
							
							<div class="wcfm_clearfix"></div>
							<div class="wcfm_profile_heading"><h3><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></h3></div>
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_shipping', array(
																																																	"sfirst_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sfirst_name ),
																																																	"slast_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $slast_name ),
																																																	"saddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_1 ),
																																																	"saddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_2 ),
																																																	"scountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $scountry ),
																																																	"scity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $scity ),
																																																	"sstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sstate ),
																																																	"szip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $szip ),
																																																	) ) );
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<!-- collapsible -->
				<?php if( $wcfm_is_allow_social_profile = apply_filters( 'wcfm_is_allow_social_profile', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_profile_form_social_head">
						<label class="fa fa-users"></label>
						<?php _e('Social', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_profile_form_social_expander" class="wcfm-content">
							<?php
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_social', array(  																														  "twitter" => array('label' => __('Twitter', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $twitter ),
																																								"facebook" => array('label' => __('Facebook', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $facebook ),
																																								"instagram" => array('label' => __('Instagram', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $instagram ),
																																								"youtube" => array('label' => __('Youtube', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $youtube ),
																																								"linkdin" => array('label' => __('linkdin', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $linkdin ),
																																								"google_plus" => array('label' => __('Google Plus', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $google_plus ),
																																								"snapchat" => array('label' => __('Snapchat', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $snapchat ),
																																								"pinterest" => array('label' => __('Pinterest', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $pinterest ),
																																								) ) );
								} else {
									if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
										wcfmu_feature_help_text_show( __( 'Social Profile', 'wc-frontend-manager' ) );
									}
								}
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<?php do_action( 'end_wcfm_wcvendors_profile', $user_id ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_profile_submit">
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfmprofile_save_button" class="wcfm_submit_button" />
			</div>
			
		</form>
		<?php
		do_action( 'after_wcfm_wcvendors_profile' );
		?>
	</div>
</div>