<?php

/**
 * Class Widget_Anokhina - Widget of Temperature and Exchange Rates
 */

class Widget_Anokhina extends WP_Widget {

	function __construct() {
		parent::__construct(
			'widget_anokhina',
			'Widget Anokhina',
			array( 'description' => 'Displaying Widget of Temperature and Exchange Rates.' )
		);
	}

    /**
     * Display widget
     * @param array $args
     * @param array $instance
     */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

        $this->get_temperature( $instance );
        $this->get_rate( $instance );

		echo $args['after_widget'];
	}

    /**
     * Form edit widget
     * @param array $instance
     * @return string|void
     */
	public function form( $instance ) {
		$title = '';
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
        $lat = WIDGET_ANOKHINA_LAT;
        if ( isset( $instance[ 'lat' ] ) ) {
            $lat = $instance[ 'lat' ];
        }
        $lon = WIDGET_ANOKHINA_LON;
        if ( isset( $instance[ 'lon' ] ) ) {
            $lon = $instance[ 'lon' ];
        }
        $unit = WIDGET_ANOKHINA_UNIT;
        if ( isset( $instance[ 'unit' ] ) ) {
            $unit = $instance[ 'unit' ];
        }
        $currency = WIDGET_ANOKHINA_CURRENCY;
        if ( isset( $instance[ 'currency' ] ) ) {
            $currency = $instance[ 'currency' ];
        }
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'widget_anokhina'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'lat' ); ?>"><?php _e('Latitude', 'widget_anokhina'); ?></label>
            <input class="widefat numbers" id="<?php echo $this->get_field_id( 'lat' ); ?>" name="<?php echo $this->get_field_name( 'lat' ); ?>" type="text" value="<?php echo esc_attr( $lat ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'lon' ); ?>"><?php _e('Longitude', 'widget_anokhina'); ?></label>
            <input class="widefat numbers" id="<?php echo $this->get_field_id( 'lon' ); ?>" name="<?php echo $this->get_field_name( 'lon' ); ?>" type="text" value="<?php echo esc_attr( $lon ); ?>" />
        </p>
        <p><?php _e('Unit', 'widget_anokhina'); ?></p>
        <p>
            <input class="widefat" id="<?php echo $this->get_field_id( 'unit_c' ); ?>" name="<?php echo $this->get_field_name( 'unit' ); ?>" type="radio" value="c" <?php echo ( $unit == 'c' ? 'checked=true' : ''); ?> />
            <label for="<?php echo $this->get_field_id( 'unit_c' ); ?>">C</label>
        </p>
        <p>
            <input class="widefat" id="<?php echo $this->get_field_id( 'unit_f' ); ?>" name="<?php echo $this->get_field_name( 'unit' ); ?>" type="radio" value="f" <?php echo ( $unit == 'f' ? 'checked=true' : ''); ?> />
            <label for="<?php echo $this->get_field_id( 'unit_f' ); ?>">F</label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'currency' ); ?>"><?php _e('Currency', 'widget_anokhina'); ?>Currency</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'currency' ); ?>" name="<?php echo $this->get_field_name( 'currency' ); ?>" type="text" value="<?php echo esc_attr( $currency ); ?>" />
        </p>
		<?php
	}

    /**
     * Update widget
     * @param array $new_instance
     * @param array $old_instance
     * @return array - result instance
     */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['lat'] = ( ! empty( $new_instance['lat'] ) ) ? strip_tags( $new_instance['lat'] ) : '';
        $instance['lon'] = ( ! empty( $new_instance['lon'] ) ) ? strip_tags( $new_instance['lon'] ) : '';
        $instance['unit'] = ( ! empty( $new_instance['unit'] ) ) ? strip_tags( $new_instance['unit'] ) : '';
        $instance['currency'] = ( ! empty( $new_instance['currency'] ) ) ? strip_tags( $new_instance['currency'] ) : '';
		return $instance;
	}

    /**
     * Get current temperature from remote API
     * @param $args: lat - latitude, lon - longitude, unit - C or F
     * @return bool - result
     */

	private function get_temperature($args) {
        $url = WIDGET_ANOKHINA_TEMP_API_URL . WIDGET_ANOKHINA_TEMP_API_KEY . '/' . $args['lat'] . ',' . $args['lon'];

        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $array_response = json_decode( wp_remote_retrieve_body( $response ), true );

        $GLOBALS['temperature'] = 'ERROR';
        $GLOBALS['unit'] = false;

        if ( isset( $array_response['currently']['temperature'] ) ) {
            $temperature = $array_response['currently']['temperature'];
            if ( $args['unit'] == 'c') {
                $temperature = round( ( ( $temperature - 32 ) /1.8 ), 2 );
                $GLOBALS['temperature'] = $temperature;
            }
        }

        if ( isset( $args['unit'] ) ) {
            $GLOBALS['unit'] = mb_strtoupper($args['unit']);
        }

        include(WIDGET_ANOKHINA_TEMPLATES_DIR . '/temperature.php');

    }

    /**
     * Get current exchange rate from remote API
     * @param $args - currency (USD, EUR, ..)
     * @return bool - result
     */

    private function get_rate($args) {
        $url = WIDGET_ANOKHINA_RATE_API_URL;

        $response = wp_remote_get( $url . '?valcode=' . $args['currency'] . '&json' );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $array_response = json_decode( wp_remote_retrieve_body( $response ), true );

        $GLOBALS['rate'] = 'ERROR';
        $GLOBALS['currency_text'] = '';

        if ( isset( $array_response[0]['rate'] ) ) {
            $GLOBALS['rate'] = round( $array_response[0]['rate'], 3 );
        }

        if ( isset( $array_response[0]['cc'] ) ) {
            $GLOBALS['currency_text'] = $array_response[0]['cc'];
        }

        include(WIDGET_ANOKHINA_TEMPLATES_DIR . '/rate.php');

    }

}

