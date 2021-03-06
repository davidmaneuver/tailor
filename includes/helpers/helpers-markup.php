<?php

/**
 * Markup helper functions.
 *
 * @package Tailor
 * @subpackage Helpers
 * @since 1.0.0
 */

if ( ! function_exists( 'tailor_filter_allowed_html' ) ) {

	/**
	 * Allow certain data attributes to be saved as part of HTML post content.
	 *
	 * @since 1.0.0
	 *
	 * @param $allowed
	 * @param $context
	 * @return mixed
	 */
	function tailor_filter_allowed_html( $allowed, $context ) {

		if ( is_array( $context ) ) {
			return $allowed;
		}


		if ( 'post' == $context ) {
			$allowed['div']['data-slides'] = true;
			$allowed['div']['data-autoplay'] = true;
			$allowed['div']['data-arrows'] = true;
			$allowed['div']['data-dots'] = true;
			$allowed['div']['data-fade'] = true;
		}

		return $allowed;
	}

	add_filter( 'wp_kses_allowed_html', 'tailor_filter_allowed_html', 10, 2 );
}

if ( ! function_exists( 'tailor_partial' ) ) {

	/**
	 * Loads a template partial.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The generic partial name.
	 * @param string $name The specialized partial name.
	 * @param array $args An associative array containing arguments to pass to the partial template.
	 */
	function tailor_partial( $slug, $name = '', $args = array() ) {

		$theme_partial_dir = 'tailor/';

		/**
		 * Filters the theme partial directory.
		 *
		 * @since 1.0.0
		 *
		 * @param string $theme_partial_dir_path
		 */
		$theme_partial_dir = apply_filters( 'tailor_theme_partial_dir', $theme_partial_dir );
		$theme_partial_dir = trailingslashit( $theme_partial_dir );

		if ( $name ) {
			$partial = locate_template( array( "{$slug}-{$name}.php", trailingslashit( $theme_partial_dir ) . "{$slug}-{$name}.php" ) );

			if ( ! $partial ) {
				$partial = tailor_locate_partial( "{$slug}-{$name}.php" );
			}
		}
		if (!$partial) {
			$partial = locate_template( array( "{$slug}.php", trailingslashit( $theme_partial_dir ) . "{$slug}.php" ) );

			if ( ! $partial ) {
				$partial = tailor_locate_partial( "{$slug}.php" );
			}
		}

		/**
		 * Filters the partial template.
		 *
		 * @since 1.0.0
		 *
		 * @param string $partial
		 * @param string $slug
		 * @param string $name
		 */
		$partial = apply_filters( 'tailor_partial', $partial, $slug, $name );

		if ( $partial ) {

			/**
			 * Fires before a template partial is loaded.
			 *
			 * @since 1.0.0
			 *
			 * @param string $partial
			 * @param string $slug
			 * @param string $name
			 */
			do_action( "tailor_partial_{$slug}", $partial, $slug, $name );

			if ( $args && is_array( $args ) ) {
				extract( $args );
			}

			include $partial;
		}
	}
}

if ( ! function_exists( 'tailor_locate_partial' ) ) {

	/**
	 * Returns the name of the highest priority partial file that exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $partial_names
	 * @return string $partial
	 */
	function tailor_locate_partial( $partial_names ) {
		$plugin_partial_paths = array( tailor()->plugin_dir() . 'partials/' );

		/**
		 * Filter the partial paths.
		 *
		 * @since 1.0.0
		 *
		 * @param array $plugin_partial_paths
		 */
		$plugin_partial_paths = apply_filters( 'tailor_plugin_partial_paths', $plugin_partial_paths );

		$partial = '';

		foreach ( array_reverse( $plugin_partial_paths ) as $plugin_partial_path ) {
			$plugin_partial_path = trailingslashit( $plugin_partial_path );

			foreach ( (array) $partial_names as $partial_name ) {
				if ( file_exists( $plugin_partial_path . $partial_name ) ) {
					$partial = $plugin_partial_path . $partial_name;
					break;
				}
			}
		}

		return $partial;
	}
}

if ( ! function_exists( 'tailor_css' ) ) {

    /**
     * Returns a singleton instance of the custom CSS manager.
     *
     * @since 1.0.0.
     *
     * @return Tailor_Custom_CSS
     */
    function tailor_css() {
        return Tailor_Custom_CSS::get_instance();
    }
}

if ( ! function_exists( 'tailor_clean_content' ) ) {

	/**
	 * Removes unwanted <p> and <br> tags from the content.
	 *
	 * @since  1.0.0
	 *
	 * @param string $content
	 * @return string
	 */
	function tailor_clean_content( $content ) {

		$array = array (
			'<p>['      => '[',
			']</p>'     => ']',
			']<br />'   => ']'
		);

		return strtr( $content, $array );
	}

	add_filter( 'the_content', 'tailor_clean_content' );
}

if ( ! function_exists( 'tailor_do_shakespeare' ) ) {

	/**
	 * Returns a random quote from ol' Shakespeare.
	 *
	 * @since  1.0.0
	 *
	 * @param int $quote_index
	 * @return string
	 */
	function tailor_do_shakespeare( $quote_index = 0 ) {

		$quotes = array (
			'This life, which had been the tomb of his virtue and of his honour, is but a walking shadow; a poor player, that struts and frets his hour upon the stage, and then is heard no more: it is a tale told by an idiot, full of sound and fury, signifying nothing.',
			'All the world\'s a stage, and all the men and women merely players: they have their exits and their entrances; and one man in his time plays many parts, his acts being seven ages.',
			'There is a tide in the affairs of men, Which taken at the flood, leads on to fortune. Omitted, all the voyage of their life is bound in shallows and in miseries. On such a full sea are we now afloat. And we must take the current when it serves, or lose our ventures.',
			'Some are born great, some achieve greatness, and some have greatness thrust upon them.',
			'Good night, good night! Parting is such sweet sorrow, that I shall say good night till it be morrow.',
			'What a piece of work is a man, how noble in reason, how infinite in faculties, in form and moving how express and admirable, in action how like an angel, in apprehension how like a god.',
			'Sweet are the uses of adversity which, like the toad, ugly and venomous, wears yet a precious jewel in his head.',
		);

		if ( array_key_exists( $quote_index, $quotes ) ) {
			return $quotes[ $quote_index ];
		}

		return $quotes[0];
	}
}

if ( ! function_exists( 'tailor_get_attributes' ) ) {

	/**
	 * Concatenates a list of attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 * @param string $prefix
	 * @return string
	 */
	function tailor_get_attributes( $atts = array(), $prefix = '' ) {
		$attributes = '';
		foreach ( $atts as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$attributes .= ' ' . $prefix . $key . '="' . $value . '"';
		}
		return $attributes;
	}
}

if ( ! function_exists( 'tailor_pluralize_string' ) ) {

	/**
	 * Pluralizes a given string.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param $string
	 * @return string
	 */
	function tailor_pluralize_string( $string ) {
		$last = $string[ strlen( $string ) - 1 ];
		if ( 'y' == $last ) {
			$cut = substr( $string, 0, -1 );
			$plural = $cut . 'ies';
		}
		else {
			$plural = $string . 's';
		}
		return $plural;
	}
}

if ( ! function_exists( 'tailor_screen_reader_text' ) ) {

	/**
	 * Returns an HTML snippet containing screen reader text.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text
	 * @return string
	 */
	function tailor_screen_reader_text( $text = '' ) {
		if ( empty( $text ) ) {
			return $text;
		}
		return sprintf( '<span class="screen-reader-text">%s</span>', esc_attr( $text ) );
	}
}

// Hook onto 'oembed_dataparse' and get 2 parameters
add_filter( 'oembed_dataparse','responsive_wrap_oembed_dataparse',10,2);

function responsive_wrap_oembed_dataparse( $html, $data ) {

	if ( empty( $data->type ) || ! is_object( $data ) || 'video' != $data->type ) {
		return $html;
	}

	$aspect_ratio = $data->width / $data->height;
	$class_name = 'tailor-responsive-embed';

	if ( abs( $aspect_ratio - ( 4 / 3 ) ) < abs( $aspect_ratio - ( 16 / 9 ) ) ) {
		$class_name .= ' tailor-responsive-embed-4by3';
	}

	$html = preg_replace( '/(width|height)="\d*"\s/', "", $html );

	return "<div class=\"{$class_name}\">{$html}</div>";
}
