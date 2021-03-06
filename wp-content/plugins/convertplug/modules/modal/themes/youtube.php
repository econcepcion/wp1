<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( ! function_exists( 'modal_theme_youtube' ) ) {

	/**
	 * Function Name: cp_get_youtube_video_url.
	 *
	 * @param  string $video_id        string parameter.
	 * @param  string $video_start     string parameter.
	 * @param  string $player_controls string parameter.
	 * @param  string $player_actions  string parameter.
	 * @return string                  string parameter.
	 */
	function cp_get_youtube_video_url( $video_id, $video_start, $player_controls, $player_actions ) {
		$video_url = 'https://www.youtube.com/embed/' . $video_id . '?wmode=opaque&player=html5&rel=0&autoplay=0&fs=0';

		if ( $video_start ) {
			$video_url .= '&start=' . $video_start;
		} else {
			$video_url .= '&start=0';
		}

		if ( '1' === $player_controls || '1' === $player_controls ) {
			$video_url .= '&controls=1';
		} else {
			$video_url .= '&controls=0';
		}

		if ( '1' === $player_actions || '1' === $player_actions ) {
			$video_url .= '&showinfo=1';
		} else {
			$video_url .= '&showinfo=0';
		}

		return $video_url;
	}

	/**
	 * Function Name: modal_theme_youtube.
	 *
	 * @param  array  $atts    array parameters.
	 * @param  string $content string parameter.
	 * @return mixed          string parameter.
	 */
	function modal_theme_youtube( $atts, $content = null ) {
		/**
		 * Define Variables.
		 */
		global $cp_form_vars;

		$style_id         = '';
		$settings_encoded = '';
		shortcode_atts(
			array(
				'style_id'         => '',
				'settings_encoded' => '',
			), $atts
		);
		$style_id         = $atts['style_id'];
		$settings_encoded = $atts['settings_encoded'];

		$settings       = base64_decode( $settings_encoded );
		$style_settings = unserialize( $settings );

		foreach ( $style_settings as $key => $setting ) {
			$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
			;
		}

		unset( $style_settings['style_id'] );

		// Generate UID.
		$uid       = uniqid();
		$uid_class = 'content-' . $uid;

		$individual_vars = array(
			'uid'         => $uid,
			'uid_class'   => $uid_class,
			'style_class' => 'cp-youtube',
		);

		/**
		 * Merge short code variables arrays.
		 *
		 * @array   $individual_vars        Individual style EXTRA short-code variables.
		 * @array   $cp_form_vars           CP Form global short-code variables.
		 * @array   $style_settings         Individual style short-code variables.
		 * @array   $atts                   short-code attributes.
		 */
		$all = array_merge(
			$individual_vars,
			$cp_form_vars,
			$style_settings,
			$atts
		);

		// Merge arrays - 'shortcode atts' & 'style options'.
		$a = shortcode_atts( $all, $style_settings );

		// Before filter.
		apply_filters_ref_array( 'cp_youtube_css', array( $a ) );

		// Style - individual options.
		$modal_size_style = '';
		$iframe_wrap      = '';
		$v_height         = $a['cp_modal_width'];
		$v_height        *= 1;
		$value_height     = ( ( $v_height / 16 ) * 9 );

		// Youtube Video.
		$video_url = cp_get_youtube_video_url( $a['video_id'], $a['video_start'], $a['player_controls'], $a['player_actions'] );
		if ( 'cp-modal-custom-size' === $a['modal_size'] ) {
			$modal_size_style .= 'max-width:' . $a['cp_modal_width'] . 'px; width: 100%; height:' . $value_height . 'px;';
			$windowcss         = '';
		} else {
			$customcss = '';
		}

		// Before filter.
		apply_filters_ref_array( 'cp_modal_global_before', array( $a ) ); ?>

		<!-- BEFORE CONTENTS -->
		<div class="cp-row">
			<?php if ( 'cp-modal-window-size' === $a['modal_size'] ) { ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-no-margin-padding" style="float: none; height: 100vh; margin: 0px auto; padding: 0px;">
				<iframe class="cp-youtube-frame" style="margin: 0;" width="100%" height="100%" src="<?php echo $video_url; ?>" data-autoplay="<?php echo esc_attr( $a['player_autoplay'] ); ?>" frameborder="0" allowfullscreen=""></iframe>
			</div>
			<?php } else { ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float: none;margin: 0 auto; padding: 0px;<?php echo esc_attr( $modal_size_style ); ?>">
				<iframe class="cp-youtube-frame" style="margin:0;<?php echo esc_attr( $modal_size_style ); ?>" src="<?php echo $video_url; ?>" data-autoplay="<?php echo esc_attr( $a['player_autoplay'] ); ?>" frameborder="0" allowfullscreen></iframe>
			</div>
			<?php } ?>
		</div><!-- .row-youtube-iframe -->
		<?php
		$a['cta_delay']    = isset( $a['cta_delay'] ) ? $a['cta_delay'] : '';
		$a['cta_bg_color'] = isset( $a['cta_bg_color'] ) ? $a['cta_bg_color'] : '';
		if ( $a['cta_switch'] ) {
			?>

			<div class="cp-row cp-form-container" data-cta-delay="<?php echo esc_attr( $a['cta_delay'] ); ?>" style="<?php echo 'background: ' . $a['modal_bg_color']; ?>;" >
				<?php
				// Embed CP Form.
				apply_filters_ref_array( 'cp_get_form', array( $a ) );
				?>
			</div>
			<?php
		}
		?>

		<?php
		/** = After filter
		 * -----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_modal_global_after', array( $a ) );

		return ob_get_clean();
	}
}
