<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ivole_Manual' ) ) :

	require_once('class-ivole-email.php');

	class Ivole_Manual {
	  public function __construct() {
			if( 'no' === get_option( 'ivole_enable_manual', 'no' ) ) {
				return;
			}
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'manual_sending' ), 20, 2 );
			add_action( 'admin_head', array( $this, 'add_custom_order_status_actions_button_css' ) );
			add_action( 'wp_ajax_ivole_manual_review_reminder', array( $this, 'manual_review_reminder' ) );
			// new column
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'custom_shop_order_column' ), 20 );
			add_action( 'manage_shop_order_posts_custom_column' , array( $this, 'custom_orders_list_column_content' ), 10, 2 );
			//
			add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ), 20, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'include_scripts' ) );
	  }

		public function manual_sending( $actions, $order ) {
			// Display the button for all orders that have a 'completed' status
	    if ( $order->has_status( array( 'completed' ) ) ) {
	        // Get Order ID (compatibility all WC versions)
	        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
	        // Set the action button
	        $actions['ivole'] = array(
	            'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=ivole_manual_review_reminder&order_id=' . $order_id ), 'ivole-manual-review-reminder', 'ivole_nonce' ),
	            'name'      => __( 'Send review reminder now', 'ivole' ),
	            'action'    => "view ivole-order ivole-o-" . $order_id, // keep "view" class for a clean button CSS
	        );
	    }
	    return $actions;
		}

		public function add_custom_order_status_actions_button_css() {
    	echo '<style>.view.ivole-order::after { font-family: woocommerce !important; content: "\e02d" !important; } .widefat .column-ivole-review-reminder {width: 100px;}</style>';
		}

		public function manual_review_reminder() {
			$order_id = intval( $_POST['order_id'] );

			//qTranslate integration
			$lang = get_post_meta( $order_id, '_user_language', true );
			$old_lang = '';
			if( $lang ) {
				global $q_config;
				$old_lang = $q_config['language'];
				$q_config['language'] = $lang;

				//WPML integration
				if ( has_filter( 'wpml_current_language' ) ) {
					$old_lang = apply_filters( 'wpml_current_language', NULL );
					do_action( 'wpml_switch_language', $lang );
				}
			}

			$e = new Ivole_Email( $order_id );
			$result = $e->trigger2( $order_id );

			//qTranslate integration
			if( $lang ) {
				$q_config['language'] = $old_lang;

				//WPML integration
				if ( has_filter( 'wpml_current_language' ) ) {
					do_action( 'wpml_switch_language', $old_lang );
				}
			}

			$status = get_post_meta( $order_id, '_ivole_review_reminder', true );
			$msg = '';
			if( '' === $status ) {
				$msg = __( 'No reminders sent', IVOLE_TEXT_DOMAIN );
			} else {
				$status = intval( $status );
				if( $status > 0) {
					$msg = $status . __( ' reminder(s) sent', IVOLE_TEXT_DOMAIN );
				} else {
					$msg = __( 'No reminders sent yet', IVOLE_TEXT_DOMAIN );
				}
			}

			if( is_array( $result ) && count( $result)  > 1 && 2 === $result[0] ) {
				wp_send_json( array( 'code' => 2, 'message' => $result[1], 'order_id' => $order_id ) );
			} elseif( 0 === $result ) {
				//unschedule automatic review reminder if manual send was successfull
				$timestamp = wp_next_scheduled( 'ivole_send_reminder', array( $order_id ) );
				//error_log('timestamp:' . $timestamp );
				if( $timestamp ) {
					wp_unschedule_event( $timestamp, 'ivole_send_reminder', array( $order_id ) );
				}
				wp_send_json( array( 'code' => 0, 'message' => $msg, 'order_id' => $order_id ) );
			} elseif( 1 === $result ) {
				wp_send_json( array( 'code' => 1, 'message' => $msg, 'order_id' => $order_id ) );
			} elseif( 3 === $result ) {
				wp_send_json( array( 'code' => 3, 'message' => __( 'Error: maximum number of reminders per order is limited to one.', IVOLE_TEXT_DOMAIN ), 'order_id' => $order_id ) );
			} elseif( 4 === $result ) {
				wp_send_json( array( 'code' => 4, 'message' => __( 'Error: the order does not contain any products from product categories for which review reminders are enabled.', IVOLE_TEXT_DOMAIN ), 'order_id' => $order_id ) );
			} elseif( 5 === $result ) {
				wp_send_json( array( 'code' => 5, 'message' => __( 'Error: the order was placed by a customer who doesn\'t have a role for which review reminders are enabled.', IVOLE_TEXT_DOMAIN ), 'order_id' => $order_id ) );
			}
			wp_send_json( array( 'code' => 98, 'message' => $msg, 'order_id' => $order_id ) );
		}

		public function custom_shop_order_column($columns) {
	    $columns['ivole-review-reminder'] = __( 'Review Reminder', IVOLE_TEXT_DOMAIN );
	    return $columns;
		}

		public function custom_orders_list_column_content( $column, $post_id ) {
			$tmp_flag = true;
	    if( 'ivole-review-reminder' === $column ) {
	      $reminder = get_post_meta( $post_id, '_ivole_review_reminder', true );
				if( '' === $reminder ) {
					echo __( '', IVOLE_TEXT_DOMAIN );
					$tmp_flag = false;
				} else {
					$reminder = intval( $reminder );
					if( $reminder > 0) {
						echo $reminder . __( ' reminder(s) sent', IVOLE_TEXT_DOMAIN );
					} else {
						echo __( 'No reminders sent', IVOLE_TEXT_DOMAIN );
					}
				}

				$timestamp = wp_next_scheduled( 'ivole_send_reminder', array( $post_id ) );
				if( $timestamp ) {
					if( $tmp_flag ) {
						echo '. ';
					}
					echo __( 'An automatic reminder is scheduled for ', IVOLE_TEXT_DOMAIN ) . date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $timestamp ) . '.';
				}
	    }
		}

		public function default_hidden_columns( $hidden, $screen ) {
			if ( isset( $screen->id ) && 'edit-shop_order' === $screen->id ) {
				//error_log( "Actions" );
				array_splice( $hidden, array_search( 'wc_actions', $hidden ), 1 );
			}
			return $hidden;
		}

		public function include_scripts( $hook ) {
			if ( 'edit.php' == $hook ) {
				wp_enqueue_script( 'ivole-manual-review-reminder', plugins_url( 'js/admin-manual.js', __FILE__ ), array(), false, false );
			}
		}
	}

endif;

?>
