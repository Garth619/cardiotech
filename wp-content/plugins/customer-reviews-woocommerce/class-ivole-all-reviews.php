<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('Ivole_All_Reviews')) :

    class Ivole_All_Reviews
    {

        /**
         * @var array holds the current shorcode attributes
         */
        public $shortcode_atts;

        private $ivrating = 'ivrating';

        public function __construct($shortcode_atts)
        {
            $defaults = array(
                'sort' => 'desc',
                'per_page' => 10,
                'number' => -1,
                'show_summary_bar' => 'true',
                'show_pictures' => 'false',
                'show_products' => 'true',
                'categories' => [],
                'products' => []
            );

            if ( isset( $shortcode_atts['categories'] ) ) {
                $categories = str_replace( ' ', '', $shortcode_atts['categories'] );
                $categories = explode( ',', $categories );
                $categories = array_filter( $categories, 'is_numeric' );
                $categories = array_map( 'intval', $categories );

                $shortcode_atts['categories'] = $categories;
            }

            if ( isset( $shortcode_atts['products'] ) ) {
                $products = str_replace( ' ', '', $shortcode_atts['products'] );
                $products = explode( ',', $products );
                $products = array_filter( $products, 'is_numeric' );
                $products = array_map( 'intval', $products );

                $shortcode_atts['products'] = $products;
            }

            $this->shortcode_atts = shortcode_atts($defaults, $shortcode_atts);
            $this->shortcode_atts['show_summary_bar'] = $this->shortcode_atts['show_summary_bar'] === 'true' ? true : false;
            $this->shortcode_atts['show_pictures'] = $this->shortcode_atts['show_pictures'] === 'true' ? true : false;
            $this->shortcode_atts['show_products'] = $this->shortcode_atts['show_products'] === 'true' ? true : false;
            // load styles and js
            $this->ivole_style_1();
        }

        public function show_all_reviews()
        {
            global $paged;
            $return = '';

            if ( get_query_var( 'paged' ) ) {
              $paged = get_query_var( 'paged' );
            } elseif ( get_query_var( 'page' ) ) {
              $paged = get_query_var( 'page' );
            } else { $paged = 1; }
            $page = $paged ? $paged : 1;

            $per_page = $this->shortcode_atts['per_page'];

						if( 0  == $per_page ) {
							$per_page = 10;
						}

            $return .= '<div class="ivole-all-reviews-shortcode">';

            // show summary bar
            if ($this->shortcode_atts['show_summary_bar']) {
                $return .= $this->show_summary_table();
            }

            $number = $this->shortcode_atts['number'] == -1 ? null : $this->shortcode_atts['number'];
            $args = array(
                'number'      => $number,
                'status'      => 'approve',
                'post_status' => 'publish',
                'post_type'   => 'product',
                'meta_key'    => 'rating',
                'orderby'     => 'comment_date_gmt',
                'order'       => $this->shortcode_atts['sort'],
                'post__in'    => $this->shortcode_atts['products']
            );

            // Query needs to be modified if category constraints are set
            if ( ! empty( $this->shortcode_atts['categories'] ) ) {
                add_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );
            }

            $comments = get_comments($args);
            $pages = ceil(count($comments)/$per_page);

            remove_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );

            if( true === $this->shortcode_atts['show_products'] ) {
              foreach( $comments as $comment ) {
                //add links to products
                $prod_temp = new WC_Product( $comment->comment_post_ID );
                $q_name = $prod_temp->get_title();
                //qTranslate integration
        				if( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
        					$q_name = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $q_name );
        				}
        				$q_name = strip_tags( $q_name );
                $image = wp_get_attachment_image_url( $prod_temp->get_image_id(), 'woocommerce_gallery_thumbnail', false );
                $permalink = $prod_temp->get_permalink();
                $comment->comment_content .= '<p class="iv-comment-product">';
                if( $image ) {
                  if( $permalink ) {
                    $comment->comment_content .= '<a href="' . $permalink . '" title="' . $q_name . '">';
                  }
                  $comment->comment_content .= '<img class="iv-comment-product-img" src="' . $image . '"/>';
                  if( $permalink ) {
                    $comment->comment_content .= '</a>';
                  }
                }
                if( $permalink ) {
                  $comment->comment_content .= '<a href="' . $permalink . '" title="' . $q_name . '">';
                }
                $comment->comment_content .= $q_name . '</p>';
                if( $permalink ) {
                  $comment->comment_content .= '</a>';
                }
              }
            }

            $show_pictures = $this->shortcode_atts['show_pictures'];
            if( 'yes' === get_option( 'ivole_attach_image', 'no' ) && true === $show_pictures ) {
              //check WooCommerce version because PhotoSwipe lightbox is only supported in version 3.0+
      				$class_a = 'ivole-comment-a-old';
      				if ( ( version_compare( WC()->version, "3.0", ">=" ) ) ) {
      					$class_a = 'ivole-comment-a';
      				}
      				foreach( $comments as $comment ) {
                //add pictures uploaded by customers
      					$pics = get_comment_meta( $comment->comment_ID, 'ivole_review_image' );
      					$pics_n = count( $pics );
      					if( $pics_n > 0 ) {
      						$comment->comment_content .= '<p class="iv-comment-image-text">' . __( 'Uploaded image(s):', IVOLE_TEXT_DOMAIN ) . '</p>';
      						$comment->comment_content .= '<div class="iv-comment-images">';
      						for( $i = 0; $i < $pics_n; $i ++) {
      							$comment->comment_content .= '<div class="iv-comment-image">';
      							$comment->comment_content .= '<a href="' . $pics[$i]['url'] . '" class="' . $class_a . '"><img src="' .
      								$pics[$i]['url'] . '" alt="' . sprintf( __( 'Image #%1$d from ', IVOLE_TEXT_DOMAIN ), $i + 1 ) .
      								$comment->comment_author . '" /></a>';
      							$comment->comment_content .= '</div>';
      						}
      						$comment->comment_content .= '<div style="clear:both;"></div></div';
      					} else {
      						//new implementation of storing pictures in comments meta
      						$pics = get_comment_meta( $comment->comment_ID, 'ivole_review_image2' );
      						$pics_n = count( $pics );
      						if( $pics_n > 0 ) {
      							$temp_comment_content_flag = false;
      							$temp_comment_content = '<p class="iv-comment-image-text">' . __( 'Uploaded image(s):', IVOLE_TEXT_DOMAIN ) . '</p>';
      							$temp_comment_content .= '<div class="iv-comment-images">';
      							for( $i = 0; $i < $pics_n; $i ++) {
      								$attachmentUrl = wp_get_attachment_url( $pics[$i] );
      								if( $attachmentUrl ) {
      									$temp_comment_content_flag = true;
      									$temp_comment_content .= '<div class="iv-comment-image">';
      									$temp_comment_content .= '<a href="' . $attachmentUrl . '" class="' . $class_a . '"><img src="' .
      										$attachmentUrl . '" alt="' . sprintf( __( 'Image #%1$d from ', IVOLE_TEXT_DOMAIN ), $i + 1 ) .
      										$comment->comment_author . '" /></a>';
      									$temp_comment_content .= '</div>';
      								}
      							}
      							$temp_comment_content .= '<div style="clear:both;"></div></div';
      							if( $temp_comment_content_flag ) {
      								$comment->comment_content .= $temp_comment_content;
      							}
      						}
      					}
      				}
            }

            $reverse_top_level = false;
            $this->shortcode_atts['sort'] === 'asc' ? $reverse_top_level = true : $reverse_top_level = false;
            $return .= '<ol class="commentlist">';
            $return .= wp_list_comments(apply_filters('woocommerce_product_review_list_args', array(
                'callback' => 'woocommerce_comments',
                'page'  => $page,
                'per_page' => $per_page,
                'reverse_top_level' => $reverse_top_level,
                'echo' => false
            )), $comments);
            $return .= '</ol>';

            $big = 999999999; // need an unlikely integer
            $args = array(
                'base'         => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'       => '?paged=%#%',
                'total'        => $pages,
                'current'      => $page,
                'show_all'     => false,
                'end_size'     => 1,
                'mid_size'     => 2,
                'prev_next'    => true,
                'prev_text'    => __('&laquo;'),
                'next_text'    => __('&raquo;'),
                'type'         => 'plain');

            // ECHO THE PAGENATION
            $return .= paginate_links($args);
            $return .= '</div>';
            return $return;
        }

        public function ivole_style_1()
        {
            if (is_singular() && !is_product()) {
              // Load gallery scripts on product pages only if supported.
          		if ( 'yes' === get_option( 'ivole_attach_image', 'no' ) && true === $this->shortcode_atts['show_pictures'] ) {
          			if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
          				$this->enqueue_wc_script( 'zoom' );
          			}
          			if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
          				$this->enqueue_wc_script( 'flexslider' );
          			}
          			if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
          				$this->enqueue_wc_script( 'photoswipe-ui-default' );
          				$this->enqueue_wc_style( 'photoswipe-default-skin' );
          				add_action( 'wp_footer', 'woocommerce_photoswipe' );
          			}
          			$this->enqueue_wc_script( 'wc-single-product' );
          		}

              wp_register_style('ivole-frontend-css', plugins_url('/css/frontend.css', __FILE__), array(), null, 'all');
              wp_register_script('ivole-frontend-js', plugins_url('/js/frontend.js', __FILE__), array(), null, true);
              wp_enqueue_style('ivole-frontend-css');
              wp_localize_script(
              'ivole-frontend-js',
              'ajax_object',
              array(
                  'ajax_url' => admin_url('admin-ajax.php'),
                  'ajax_nonce' => wp_create_nonce('ivole-review-vote'),
                  'text_processing' => __('Processing...', IVOLE_TEXT_DOMAIN),
                  'text_thankyou' => __('Thank you for your feedback!', IVOLE_TEXT_DOMAIN),
                  'text_error1' => __('An error occurred with submission of your feedback. Please refresh the page and try again.', IVOLE_TEXT_DOMAIN),
                  'text_error2' => __('An error occurred with submission of your feedback. Please report it to the website administrator.', IVOLE_TEXT_DOMAIN),
                  'ivole_disable_lightbox' => ( 'yes' == get_option( 'ivole_disable_lightbox', 'no' ) ? 1 : 0 )
                )
              );
              wp_enqueue_script('ivole-frontend-js');
            }
        }

        private function enqueue_wc_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WC_VERSION, $in_footer = true ) {
      		if ( ! wp_script_is( $handle, 'registered' ) ) {
            wp_register_script( $handle, $path, $deps, $version, $in_footer );
          }
          if( ! wp_script_is( $handle ) ) {
            wp_enqueue_script( $handle );
      		}
      	}

        private function enqueue_wc_style( $handle, $path = '', $deps = array(), $version = WC_VERSION, $media = 'all', $has_rtl = false ) {
      		if ( ! wp_style_is( $handle, 'registered' ) ) {
            wp_register_style( $handle, $path, $deps, $version, $media );
          }
          if( ! wp_style_is( $handle ) ) {
      		   wp_enqueue_style( $handle );
          }
      	}

        private function count_ratings($rating)
        {
            $args = array(
                'post_status' => 'publish',
                'post_type'   => 'product' ,
                'status' => 'approve',
                'parent' => 0,
                'count' => true,
                'post__in' => $this->shortcode_atts['products']
            );
            if ($rating > 0) {
                $args['meta_query'][] = array(
                    'key' => 'rating',
                    'value'   => $rating,
                    'compare' => '=',
                    'type'    => 'numeric'
                );
            }
            // Query needs to be modified if category constraints are set
            if ( ! empty( $this->shortcode_atts['categories'] ) ) {
                add_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );
            }
            $count = get_comments($args);
            remove_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );

            return $count;
        }

        public function show_summary_table()
        {
            $all = $this->count_ratings(0);
            if ($all > 0) {
                $five = (float)$this->count_ratings(5);
                $five_percent = floor($five / $all * 100);
                $five_rounding = $five / $all * 100 - $five_percent;
                $four = (float)$this->count_ratings(4);
                $four_percent = floor($four / $all * 100);
                $four_rounding = $four / $all * 100 - $four_percent;
                $three = (float)$this->count_ratings(3);
                $three_percent = floor($three / $all * 100);
                $three_rounding = $three / $all * 100 - $three_percent;
                $two = (float)$this->count_ratings(2);
                $two_percent = floor($two / $all * 100);
                $two_rounding = $two / $all * 100 - $two_percent;
                $one = (float)$this->count_ratings(1);
                $one_percent = floor($one / $all * 100);
                $one_rounding = $one / $all * 100 - $one_percent;
                $hundred = $five_percent + $four_percent + $three_percent + $two_percent + $one_percent;
                // if( $hundred < 100 ) {
                // 	$to_distribute = 100 - $hundred;
                // 	$roundings = array( '5' => $five_rounding, '4' => $four_rounding, '3' => $three_rounding, '2' => $two_rounding, '1' => $one_rounding );
                // 	arsort($roundings);
                // 	error_log( print_r( $roundings, true ) );
                // }
                $output = '';
                $output .= '<div class="ivole-summaryBox">';
                $output .= '<table id="ivole-histogramTable">';
                // five
                $output .= '<tbody>';
                $output .= '<tr class="ivole-histogramRow">';
                $output .= '<td class="ivole-histogramCell1">' . __('5 star', IVOLE_TEXT_DOMAIN) . '</td>';
                $output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $five_percent . '%"></div></div></td>';
                $output .= '<td class="ivole-histogramCell3">' . (string)$five_percent . '%</td>';

                // four
                $output .= '</tr>';
                $output .= '<tr class="ivole-histogramRow">';
                $output .= '<td class="ivole-histogramCell1">' . __('4 star', IVOLE_TEXT_DOMAIN) . '</td>';
                $output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $four_percent . '%"></div></div></td>';
                $output .= '<td class="ivole-histogramCell3">' . (string)$four_percent . '%</td>';

                // three
                $output .= '</tr>';
                $output .= '<tr class="ivole-histogramRow">';
                $output .= '<td class="ivole-histogramCell1">' . __('3 star', IVOLE_TEXT_DOMAIN) . '</td>';
                $output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $three_percent . '%"></div></div></td>';
                $output .= '<td class="ivole-histogramCell3">' . (string)$three_percent . '%</td>';

                // two
                $output .= '</tr>';
                $output .= '<tr class="ivole-histogramRow">';
                $output .= '<td class="ivole-histogramCell1">' . __('2 star', IVOLE_TEXT_DOMAIN) . '</td>';
                $output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $two_percent . '%"></div></div></td>';
                $output .= '<td class="ivole-histogramCell3">' . (string)$two_percent . '%</td>';

                // one
                $output .= '</tr>';
                $output .= '<tr class="ivole-histogramRow">';
                $output .= '<td class="ivole-histogramCell1">' . __('1 star', IVOLE_TEXT_DOMAIN) . '</td>';
                $output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $one_percent . '%"></div></div></td>';
                $output .= '<td class="ivole-histogramCell3">' . (string)$one_percent . '%</td>';

                $output .= '</tr>';
                if ('yes' !== get_option('ivole_reviews_nobranding', 'no')) {
                    $output .= '<tr class="ivole-histogramRow">';
                    $output .= '<td colspan="3" class="ivole-credits">';
                    $output .= 'Powered by <a href="https://wordpress.org/plugins/customer-reviews-woocommerce/" target="_blank">Customer Reviews Plugin</a>';
                    $output .= '</td>';
                    $output .= '</tr>';
                }
                $output .= '</tbody>';
                $output .= '</table>';
                if (get_query_var($this->ivrating)) {
                    $rating = intval(get_query_var($this->ivrating));
                    if ($rating > 0 && $rating <= 5) {
                        $filtered_comments = sprintf(esc_html(_n('Showing %1$d of %2$d review (%3$d star). ', 'Showing %1$d of %2$d reviews (%3$d star). ', $all, 'ivole')), $this->count_ratings($rating), $all, $rating);
                        $all_comments = sprintf(esc_html(_n('See all %d review', 'See all %d reviews', $all, 'ivole')), $all);
                        $output .= '<span>' . $filtered_comments . '</span><a class="ivole-seeAllReviews" href="' . esc_url(get_permalink()) . '#tab-reviews">' . $all_comments . '</a>';
                    }
                }
                $output .= '</div>';
                return $output;
            }
        }

        /**
         * Modify the comments query to constrain results to the provided categories
         */
        public function modify_comments_clauses( $clauses ) {
            global $wpdb;

            $terms = get_terms( array(
                'taxonomy' => 'product_cat',
                'include'  => $this->shortcode_atts['categories'],
                'fields'   => 'tt_ids'
            ) );

            if ( is_array( $terms ) && count( $terms ) > 0 ) {
                $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} ON {$wpdb->comments}.comment_post_ID = {$wpdb->term_relationships}.object_id";
                $clauses['where'] .= " AND {$wpdb->term_relationships}.term_taxonomy_id IN(" . implode( ',', $terms ) . ")";
            }

            return $clauses;
        }
    }

endif;
