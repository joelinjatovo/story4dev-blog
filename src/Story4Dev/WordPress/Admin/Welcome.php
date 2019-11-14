<?php
namespace Story4Dev\WordPress\Admin;

/**
 * Welcome
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class Welcome{
    /**
     * Welcome pages.
     *
     * @var array
     */
    private static $welcomes = array();

    /**
     * Error messages.
     *
     * @var array
     */
    private static $errors = array();

    /**
     * Update messages.
     *
     * @var array
     */
    private static $messages = array();

    /**
     * Include the welcomes page classes.
     */
    public static function get_pages() {
        if ( empty( self::$welcomes ) ) {
            $welcomes = array();
            
            $welcomes[] = new Dashboard();

            self::$welcomes = apply_filters( 'wppd_get_welcomes_pages', $welcomes );
        }

        return self::$welcomes;
    }

    /**
     * Save the welcomes.
     */
    public static function save() {
        global $current_page;

        check_admin_referer( 'wppd-welcomes' );

        // Trigger actions.
        do_action( 'wppd_welcomes_save_' . $current_page );
        do_action( 'wppd_post_welcomes_' . $current_page );
        do_action( 'wppd_post_welcomes' );

        //self::add_message( __( 'Your request has been executed.', 'WpPerDim' ) );

        do_action( 'wppd_welcomes_posted' );
    }

    /**
     * Add a message.
     *
     * @param string $text Message.
     */
    public static function add_message( $text ) {
        self::$messages[] = $text;
    }

    /**
     * Add an error.
     *
     * @param string $text Message.
     */
    public static function add_error( $text ) {
        self::$errors[] = $text;
    }

    /**
     * Output messages + errors.
     */
    public static function show_messages() {
        if ( count( self::$errors ) > 0 ) {
            foreach ( self::$errors as $error ) {
                echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
            }
        } elseif ( count( self::$messages ) > 0 ) {
            foreach ( self::$messages as $message ) {
                echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
            }
        }
    }

    /**
     * Welcome page.
     *
     * Handles the display of the main woocommerce settings page in admin.
     */
    public static function output() {
        global $current_page;
        
        // Get tabs for the welcomes page.
        $tabs = apply_filters('wppd_welcomes_tabs_array', []);
        
        include WPPD_DIR . '/template/admin/welcome.php';
    }

    /**
     * Output welcome fields.
     *
     * @param array[] $options Opens array to output.
     */
    public static function output_fields( $options ) {
        foreach ( $options as $key => $value ) {
            if ( ! isset( $value['type'] ) ) {
                continue;
            }
            if ( ! isset( $value['id'] ) ) {
                $value['id'] = '';
            }
            if ( ! isset( $value['title'] ) ) {
                $value['title'] = isset( $value['name'] ) ? $value['name'] : '';
            }
            if ( ! isset( $value['class'] ) ) {
                $value['class'] = '';
            }
            if ( ! isset( $value['css'] ) ) {
                $value['css'] = '';
            }
            if ( ! isset( $value['default'] ) ) {
                $value['default'] = '';
            }
            if ( ! isset( $value['desc'] ) ) {
                $value['desc'] = '';
            }
            if ( ! isset( $value['desc_tip'] ) ) {
                $value['desc_tip'] = false;
            }
            if ( ! isset( $value['placeholder'] ) ) {
                $value['placeholder'] = '';
            }
            if ( ! isset( $value['suffix'] ) ) {
                $value['suffix'] = '';
            }
            if ( ! isset( $value['value'] ) ) {
                $value['value'] = self::get_post( $value['id'], $value['default'] );
            }

            // Custom attribute handling.
            $custom_attributes = array();

            if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
                foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
                    $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
                }
            }
            
            // Description handling.
            $field_description = Welcome::get_field_description( $value );
            $description       = $field_description['description'];
            $tooltip_html      = $field_description['tooltip_html'];

            // Switch based on type.
            switch ( $value['type'] ) {

                // Section Titles.
                case 'title':
                    if ( ! empty( $value['title'] ) ) {
                        echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
                    }
                    if ( ! empty( $value['desc'] ) ) {
                        echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
                        echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
                        echo '</div>';
                    }
                    echo '<div class="col">' . "\n\n";
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) );
                    }
                    break;

                // Section Ends.
                case 'sectionend':
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_end' );
                    }
                    echo '</div>';
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_after' );
                    }
                    break;

                // Section Titles.
                case 'table-start':
                    echo '<table class="form-table">' . "\n\n";
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) );
                    }
                    break;

                // Section Titles.
                case 'table-end':
                    echo '</table>' . "\n\n";
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) );
                    }
                    break;

                // Submit button.
                case 'submit':
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_end' );
                    }
                    echo '<p class="submit">';
                        if ( empty( $GLOBALS['hide_save_button'] ) ) :
                            echo '<button name="save" class="button-primary wppd-save-button" type="submit" value="' . esc_html( $value['title'] ) . '">' . esc_html( $value['title'] ) . '</button>';
                        endif;
                        wp_nonce_field( 'wppd-welcomes' );
                    echo '</p>';
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_after' );
                    }
                    break;

                // Notice paragraphe
                case 'notice':
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_end' );
                        echo '<div class="' . esc_attr( $value['class'] ) . '">' . "\n\n";
                            if ( ! empty( $value['title'] ) ) {
                                echo '<h3>' . esc_html( $value['title'] ) . '</h3>';
                            }
                            if ( ! empty( $value['desc'] ) ) {
                                echo '<p class="' . esc_attr( $value['id'] ) . '-description">';
                                    echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
                                echo '</p>';
                            }
                        echo '</div>';
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_after' );
                    }
                    break;

                // Notice paragraphe
                case 'file':
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_end' );
                        ?>
                        <tr valign="top" class="titledesc">
                            <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
                            <td class="forminp forminp-checkbox">
                        <?php
                            echo '<input type="file" name="' . esc_attr( $value['id'] ) . '" >';
                        ?>
                            </td>
                        </tr>
                        <?php
                        do_action( 'wppd_welcomes_' . sanitize_title( $value['id'] ) . '_after' );
                    }
                    break;

                // Checkbox input.
                case 'checkbox':
                    $option_value = $value['value'];
                    $visibility_class = array();

                    if ( ! isset( $value['hide_if_checked'] ) ) {
                        $value['hide_if_checked'] = false;
                    }
                    if ( ! isset( $value['show_if_checked'] ) ) {
                        $value['show_if_checked'] = false;
                    }
                    if ( 'yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked'] ) {
                        $visibility_class[] = 'hidden_option';
                    }
                    if ( 'option' === $value['hide_if_checked'] ) {
                        $visibility_class[] = 'hide_options_if_checked';
                    }
                    if ( 'option' === $value['show_if_checked'] ) {
                        $visibility_class[] = 'show_options_if_checked';
                    }

                    if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) {
                        ?>
                            <tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
                                <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
                                <td class="forminp forminp-checkbox">
                                    <fieldset>
                        <?php
                    } else {
                        ?>
                            <fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
                        <?php
                    }

                    if ( ! empty( $value['title'] ) ) {
                        ?>
                            <legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
                        <?php
                    }

                    ?>
                        <label for="<?php echo esc_attr( $value['id'] ); ?>">
                            <input
                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                type="checkbox"
                                class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
                                value="1"
                                <?php checked( $option_value, 'yes' ); ?>
                                <?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
                            /> <?php echo $description; // WPCS: XSS ok. ?>
                        </label> <?php echo $tooltip_html; // WPCS: XSS ok. ?>
                    <?php

                    if ( ! isset( $value['checkboxgroup'] ) || 'end' === $value['checkboxgroup'] ) {
                        ?>
                                    </fieldset>
                                </td>
                            </tr>
                        <?php
                    } else {
                        ?>
                            </fieldset>
                        <?php
                    }
                    break;

                // Standard text inputs and subtypes like 'number'.
                case 'text':
                case 'password':
                case 'datetime':
                case 'datetime-local':
                case 'date':
                case 'month':
                case 'time':
                case 'week':
                case 'number':
                case 'email':
                case 'url':
                case 'tel':
                    $option_value = $value['value'];

                    ?><tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                            <input
                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                type="<?php echo esc_attr( $value['type'] ); ?>"
                                style="<?php echo esc_attr( $value['css'] ); ?>"
                                value="<?php echo esc_attr( $option_value ); ?>"
                                class="<?php echo esc_attr( $value['class'] ); ?>"
                                placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
                                <?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
                                /><?php echo esc_html( $value['suffix'] ); ?> <?php echo $description; // WPCS: XSS ok. ?>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'hidden':
                    $option_value = $value['value'];

                    ?><tr valign="top" style="display: none;">
                        <th scope="row" class="titledesc"></th>
                        <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                            <input
                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                type="<?php echo esc_attr( $value['type'] ); ?>"
                                style="<?php echo esc_attr( $value['css'] ); ?>"
                                value="<?php echo esc_attr( $option_value ); ?>"
                                class="<?php echo esc_attr( $value['class'] ); ?>"
                                placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
                                <?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
                                /><?php echo esc_html( $value['suffix'] ); ?> <?php echo $description; // WPCS: XSS ok. ?>
                        </td>
                    </tr>
                    <?php
                    break;

                // Default: run an action.
                default:
                    Settings::output_fields( [$options[$key]] );
                    break;
            }
        }
    }

    /**
     * Get a setting from the settings API.
     *
     * @param string $option_name Option name.
     * @param mixed  $default     Default value.
     * @return mixed
     */
    public static function get_post( $option_name, $default = '' ) {
        // Post value.
        $option_value = isset($_POST[$option_name])?$_POST[$option_name]:null;

        if ( is_array( $option_value ) ) {
            $option_value = array_map( 'stripslashes', $option_value );
        } elseif ( ! is_null( $option_value ) ) {
            $option_value = stripslashes( $option_value );
        }

        return ( null === $option_value ) ? $default : $option_value;
    }

    /**
     * Run POST Request
     *
     * @param array $options Options array to output.
     * @param array $data    Optional. Data to use for saving. Defaults to $_POST.
     * @return bool
     */
    public static function save_fields( $options, $data = null ) {
        return true;
    }

    /**
     * Helper function to get the formatted description and tip HTML for a
     * given form field. Plugins can call this when implementing their own custom
     * settings types.
     *
     * @param  array $value The form field value array.
     * @return array The description and tip as a 2 element array.
     */
    public static function get_field_description( $value ) {
        $description  = '';
        $tooltip_html = '';

        if ( true === $value['desc_tip'] ) {
            $tooltip_html = $value['desc'];
        } elseif ( ! empty( $value['desc_tip'] ) ) {
            $description  = $value['desc'];
            $tooltip_html = $value['desc_tip'];
        } elseif ( ! empty( $value['desc'] ) ) {
            $description = $value['desc'];
        }

        if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ), true ) ) {
            $description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
        } elseif ( $description && in_array( $value['type'], array( 'checkbox' ), true ) ) {
            $description = wp_kses_post( $description );
        } elseif ( $description ) {
            $description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
        }

        if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ), true ) ) {
            $tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
        } elseif ( $tooltip_html ) {
            $tooltip_html = wc_help_tip( $tooltip_html );
        }

        return array(
            'description'  => $description,
            'tooltip_html' => $tooltip_html,
        );
    }
}