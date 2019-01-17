<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Order_Meta {

    /**
     * Check if price has to be display in cart and checkout
     * @var type 
     * @var boolean
     * @access private
     * @since 3.4.2
     */
    private $show_price = false;

    private function wcpa_meta_by_meta_id($item, $meta_id) {
        $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);

        if (is_array($meta_data) && count($meta_data)) {

            foreach ($meta_data as $v) {

                if (isset($v['meta_id']) && ($meta_id == $v['meta_id'])) {
                    return $v;
                }
            }
        } else {
            return false;
        }
        return false;
    }

    public function order_meta_plain($v, $show_price = true) {
        if ($v['type'] == 'file') {
            if (isset($v['value']['url']) && $v['value']['url']) {
                if ($v['price'] && $show_price) {
                    return $v['value']['file_name'] . ' | ' . $v['value']['url'] . ' | (' . wcpa_price($v['price'], 1) . ')';
                } else {
                    return $v['value']['file_name'] . ' | ' . $v['value']['url'];
                }
            } else {
                return '';
            }
        } else if ($v['type'] == 'image-group') {
            if ($v['price'] && $show_price) {
                return implode("\n", array_map(function($a, $b) {
                            if ($a['i'] === 'other') {
                                if ($b) {
                                    return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'] . ' | (' . wcpa_price($b, 1) . ')';
                                } else {
                                    return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'];
                                }
                            } else {
                                if ($b) {
                                    return $a['label'] . ' | ' . $a['image'] . ' | (' . wcpa_price($b, 1) . ')';
                                } else {
                                    return $a['label'] . ' | ' . $a['image'];
                                }
                            }
                        }, $v['value'], $v['price']));
            } else {
                return implode("\n", array_map(function($a) {
                            if ($a['i'] === 'other') {
                                return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'];
                            } else {
                                return $a['label'] . ' | ' . $a['image'];
                            }
                        }, $v['value']));
            }
        } else if ($v['type'] == 'color-group') {
            if ($v['price'] && $show_price) {
                return implode("\n", array_map(function($a, $b) {
                            if ($a['i'] === 'other') {
                                if ($b) {
                                    return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'] . ' | (' . wcpa_price($b, 1) . ')';
                                } else {
                                    return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'];
                                }
                            } else {
                                if ($b) {
                                    return $a['label'] . ' | ' . $a['value'] . ' | ' . $a['color'] . ' | (' . wcpa_price($b, 1) . ')';
                                } else {
                                    return $a['label'] . ' | ' . $a['value'] . ' | ' . $a['color'];
                                }
                            }
                        }, $v['value'], $v['price']));
            } else {
                return implode("\n", array_map(function($a) {
                            if ($a['i'] === 'other') {
                                return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'];
                            } else {
                                return $a['label'] . ' | ' . $a['value'] . ' | ' . $a['color'];
                            }
                        }, $v['value']));
            }
        } else if ($v['type'] == 'placeselector') {
            $display = '';

            if (!empty($v['value']['formated'])) {
                $display = $v['value']['formated'] . "\n";
                if (!empty($v['value']['splited']['street_number'])) {
                    $display .= __('Street address:', 'wcpa-text-domain') . ' ' . $v['value']['splited']['street_number'] . ' ' . $v['value']['splited']['route'] . "\n";
                }
                if (!empty($v['value']['splited']['locality'])) {
                    $display .= __('City:', 'wcpa-text-domain') . ' ' . $v['value']['splited']['locality'] . "\n";
                }
                if (!empty($v['value']['splited']['administrative_area_level_1'])) {
                    $display .= __('State:', 'wcpa-text-domain') . ' ' . $v['value']['splited']['administrative_area_level_1'] . "\n";
                }
                if (!empty($v['value']['splited']['postal_code'])) {
                    $display .= __('Zip code:', 'wcpa-text-domain') . ' ' . $v['value']['splited']['postal_code'] . "\n";
                }
                if (!empty($v['value']['splited']['country'])) {
                    $display .= __('Country:', 'wcpa-text-domain') . ' ' . $v['value']['splited']['country'] . "\n";
                }
                if (isset($v['value']['cords']['lat']) && !empty($v['value']['cords']['lat'])) {
                    $display .= __('Latitude:', 'wcpa-text-domain') . ' ' . $v['value']['cords']['lat'] . "\n";
                    $display .= __('Longitude:', 'wcpa-text-domain') . ' ' . $v['value']['cords']['lng'] . "\n";
                }
            }
            return $display;
        } else if (is_array($v['value'])) {
            $is_ver_1_data = TRUE;
            $first = current($v['value']);

            if (isset($first['i'])) {
                $is_ver_1_data = FALSE;
            }
            if ($is_ver_1_data) {
                if ($v['price'] && $show_price) {
                    return implode("\n", array_map(function($a, $b) {
                                if ($b !== null && $b !== false) {
                                    return $a . ' (' . wcpa_price($b, 1) . ')';
                                } else {
                                    return $a;
                                }
                            }, $v['value'], $v['price']));
                } else {
                    return implode("\n", $v['value']);
                }
            } else {
                if (($v['price']) && $show_price) {

                    return implode("\n", array_map(function($a, $b) {
                                if (($a['i'] === 'other')) {

                                    return __($a['label'] . ':', 'wcpa-text-domain') . ' ' . $a['value'] . (($b !== null && $b !== false) ? ' (' . wcpa_price($b, 1) . ')' : '');
                                } else {
                                    return $a['label'] . (($b !== null && $b !== false) ? ' (' . wcpa_price($b, 1) . ')' : '');
                                }
//                                if ($b) {
//                                    return (($a['i'] === 'other') ? __($a['label'] . ':', 'wcpa-text-domain') . ' ' : '') . $a['label'] . ' (' . wcpa_price($b, 1) . ')';
//                                } else {
//                                    return (($a['i'] === 'other') ? __('Other:', 'wcpa-text-domain') . ' ' : '') . $a['label'];
//                                }
                            }, $v['value'], $v['price']));
                } else {
                    return trim(array_reduce($v['value'], function($a, $b) {
                                if ($b['i'] === 'other') {
                                    return $a . "\n" . __($b['label'] . ':', 'wcpa-text-domain') . $b['value'];
                                } else {
                                    return $a . "\n" . $b['label'];
                                }
                            }), "\n");
                }
            }
        } else {
            if ($v['price'] && $show_price) {
                return $v['value'] . ' (' . wcpa_price($v['price'], 1) . ')';
            } else {
                return $v['value'];
            }
        }
    }

    public function display_item_meta($html, $item, $args) {

        $html = str_replace('<strong class="wc-item-meta-label">' . WCPA_EMPTY_LABEL . ':</strong>', '', $html);
        return str_replace(WCPA_EMPTY_LABEL . ':', '', $html);
    }

    public function display_meta_value($display_value, $meta=null, $item=null) {
		
		if($item!=null && $meta!==null){
			$wcpa_data = $this->wcpa_meta_by_meta_id($item, $meta->id);
		}else{
			$wcpa_data = false;
		}
        

        if ($wcpa_data) {

            $this->show_price = wcpa_get_option('show_price_in_order', true);

            if ($this->show_price == false) {// dont compare with === , $show_price will be 1 for true and 0 for false
                $meta->value = $display_value = $this->order_meta_plain($wcpa_data, false);
            }

            switch ($wcpa_data['type']) {
                case 'text':
                case 'date':
                case 'number':
                case 'time':
                case 'datetime-local':
                case 'header':
                    return $display_value;
                case 'textarea':
                    return nl2br($meta->value);
                case 'paragraph':
                    return do_shortcode(nl2br($meta->value));
                case 'color':
                    return '<span style="color:' . $meta->value . ';font-size: 20px;
            padding: 0;
    line-height: 0;">&#9632;</span>' . $meta->value;
                case 'select':
                case 'checkbox-group':
                case 'radio-group':

                    return $display_value; //str_replace(', ', '<br>', $meta->value);
                case 'file':
                    return $this->display_meta_value_file($wcpa_data['value']);
                case 'image-group':

                    return $this->display_meta_value_image($wcpa_data);
                case 'color-group':
                    return $this->display_meta_value_colorgroup($wcpa_data);
                case 'placeselector':

                    if (!empty($wcpa_data['value']['formated'])) {
                        $display = $wcpa_data['value']['formated'] . '<br>';
                        if (!empty($wcpa_data['value']['splited']['street_number'])) {
                            $display .= __('Street address:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['splited']['street_number'] . ' ' . $wcpa_data['value']['splited']['route'] . ' <br>';
                        }
                        if (!empty($wcpa_data['value']['splited']['locality'])) {
                            $display .= __('City:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['splited']['locality'] . '<br>';
                        }
                        if (!empty($wcpa_data['value']['splited']['administrative_area_level_1'])) {
                            $display .= __('State:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['splited']['administrative_area_level_1'] . '<br>';
                        }
                        if (!empty($wcpa_data['value']['splited']['postal_code'])) {
                            $display .= __('Zip code:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['splited']['postal_code'] . '<br>';
                        }
                        if (!empty($wcpa_data['value']['splited']['country'])) {
                            $display .= __('Country:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['splited']['country'] . '<br>';
                        }
                        if (isset($wcpa_data['value']['cords']['lat']) && !empty($wcpa_data['value']['cords']['lat'])) {
                            $display .= __('Latitude:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['cords']['lat'] . '<br>';
                            $display .= __('Longitude:', 'wcpa-text-domain') . ' ' . $wcpa_data['value']['cords']['lng'] . '<br>';
                            $display .= '<a href="https://www.google.com/maps/?q=' . $wcpa_data['value']['cords']['lat'] . ',' . $wcpa_data['value']['cords']['lng'] . '" target="_blank">' . __('View on map', 'wcpa-text-domain') . '</a> <br>';
                        }
                        return $display;
                    } else {
                        return $display_value;
                    }


                default:
                    return $display_value;
            }
        } else {
            return $display_value;
        }
    }

    public function display_meta_value_file($value) {
        $display = '';


        if (isset($value['url'])) {
            $display .= '<a href="' . $value['url'] . '"  target="_blank" download="' . $value['file_name'] . '">';
            if (in_array($value['type'], array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'))) {
                $display .= '<img class="wcpa_img" src="' . $value['url'] . '" />';
            } else {
                $display .= '<img class="wcpa_icon" src="' . wp_mime_type_icon($value['type']) . '" />';
            }
            $display .= $value['file_name'] . '</a>';
        }

        return $display;
    }

    public function display_meta_value_colorgroup($value) {
        $style = array();
        $display = '<div class="wcpa_color_group acd" >';
        if (is_array($value['value'])) {

            foreach ($value['value'] as $k => $v) {
                if ($k === 'other') {
                    $display .= '<div class="wcpa_cart_colorgroup">' . __($v['label'] . ':', 'wcpa-text-domain') . ' ' . $v['value'] . '';
                } else {
                    $display .= '<div class="wcpa_cart_colorgroup">';

                    $disp_size = 30;
                    if (isset($value['form_data']->disp_size) && $value['form_data']->disp_size > 10) {
                        $disp_size = $value['form_data']->disp_size;
                    }


                    $style['height'] = $disp_size . 'px';
                    if (isset($value['form_data']->show_label_inside) && $value['form_data']->show_label_inside) {
                        $style['min-width'] = $disp_size . 'px';
                        $style['line-height'] = ($disp_size - 2) . 'px';
                    } else {
                        $style['width'] = $disp_size . 'px';
                    }

                    $display .= '<span style="color:' . $v['color'] . ';font-size: 20px;
            padding: 0;
    line-height: 0;">&#9632;</span>' . $v['value'] . ' | ' . $v['label'] . ' ';
                }

                if ($this->show_price && $value['price'] && is_array($value['price'])) {
                    if (isset($value['price'][$k]) && $value['price'][$k] !== FALSE) {
                        $display .= '<span class="wcpa_cart_price">(' . wcpa_price($value['price'][$k]) . ')</span>';
                    }
                } else {
                    if ($value['price'] !== FALSE && $this->show_price) {
                        $display .= ' <span class="wcpa_cart_price">(' . wcpa_price($value['price']) . ')</span>';
                    }
                }


                $display .= '</div>';
            }
        }
        $display .= '</div>';

        return $display;
    }

    public function display_meta_value_image($value) {
        $display = '<div class="wcpa_image_group">';


        foreach ($value['value'] as $k => $v) {


            if (isset($v['image']) && $v['image'] !== false) {
                $display .= '<p class="wcpa_image">';
                $display .= '<img src="' . $v['image'] . '" />';
                $display .= $v['label'];
            } else if ($v['i'] === 'other') {
                $display .= '<p class="wcpa_image">';
                $display .= __('Other:', 'wcpa-text-domain') . ' ' . $v['label'];
            }
            if ($this->show_price && $value['price'] && is_array($value['price'])) {
                if (isset($value['price'][$k]) && $value['price'][$k] !== FALSE) {
                    $display .= '<span class="wcpa_cart_price">(' . wcpa_price($value['price'][$k]) . ')</span>';
                }
            } else {
                if ($this->show_price && $value['price'] !== FALSE) {
                    $display .= ' <span class="wcpa_cart_price">(' . wcpa_price($value['price']) . ')</span>';
                }
            }
        }
        $display .= '</div>';

        return $display;
    }

    public function checkout_order_processed($order_id, $posted_data, $order) {
        $items = $order->get_items();
        if (is_array($items)) {
            foreach ($items as $item_id => $item) {
                $this->update_order_item($item, $order_id);
            }
        }
    }

    public function update_order_item($item, $order_id) {
        $meta_data = $item->get_meta_data();
        $wcpa_meta_data = $item->get_meta(WCPA_ORDER_META_KEY);
        foreach ($meta_data as $meta) {
            $data = (object) $meta->get_data();

            if (($matches = $this->check_wcpa_meta($data)) !== false) {
                if (isset($wcpa_meta_data[$matches[1]])) {
                    $wcpa_meta_data_item = $wcpa_meta_data[$matches[1]];
                   
                    $item->update_meta_data($wcpa_meta_data_item['label'], $data->value, $data->id);
                    $wcpa_meta_data[$matches[1]]['meta_id'] = $data->id;
//                    $wcpa_meta_key_info[$data->id] = $wcpa_meta_data_item['type'];
                }
            }
        }

        $wcpa_meta_data = apply_filters('wcpa_order_meta_data', $wcpa_meta_data, $item, $order_id);

        $item->update_meta_data(WCPA_ORDER_META_KEY, $wcpa_meta_data);
        $item->save_meta_data();
    }

    public function checkout_create_order_line_item($item, $cart_item_key, $values) {

        if (empty($values[WCPA_CART_ITEM_KEY])) {
            return;
        }
        $meta_data = array();
        $i = 0;
        $save_price = wcpa_get_option('show_price_in_order_meta', true);
        foreach ($values[WCPA_CART_ITEM_KEY] as $v) {
            $meta_data[$i] = $v;

            if (!in_array($v['type'], array('separator'))) {
                if ($save_price === false) {
                    $item->add_meta_data('WCPA_id_' . $i, $this->order_meta_plain($v, false));
                } else {
                    $item->add_meta_data('WCPA_id_' . $i, $this->order_meta_plain($v));
                }
            }
            $i++;
        }
        $item->add_meta_data(WCPA_ORDER_META_KEY, $meta_data);
    }

    private function check_wcpa_meta($meta) {

        preg_match("/WCPA_id_(.*)/", $meta->key, $matches);
        if ($matches && count($matches)) {
            return $matches;
        } else {
            return false;
        }
    }

    // admin side */

    public function order_item_line_item_html($item_id, $item, $product) {
        $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);

        WCPA_Backend::view('order-meta-line-item', ['meta_data' => $meta_data, 'order' => $item->get_order(), 'item_id' => $item_id]);
    }

    /**
     * To hide showing default meat values in backend order data. As it is displaying in other way already
     */
    public function order_item_get_formatted_meta_data($formatted_meta, $item) {

        if (did_action('woocommerce_before_order_itemmeta') > 0) {
            $meta_data = $item->get_meta('_wcpa_meta_key_info');
            foreach ($formatted_meta as $meta_id => $v) {
                if ($this->wcpa_meta_by_meta_id($item, $meta_id)) {
                    unset($formatted_meta[$meta_id]);
                }
            }
        }


        return $formatted_meta;
    }

    public function sanitize_values($value, $type) {
        if (is_array($value)) {
            array_walk($value, function(&$a, $b) {
                sanitize_text_field($a);
            }); // using this array_wal method to preserve the keys
            return $value;
        } else if ($type == 'textarea') {
            return sanitize_textarea_field($value);
        } else {
            return sanitize_text_field($value);
        }
    }

    public function before_save_order_items($order_id, $items) {
        $save_price = wcpa_get_option('show_price_in_order_meta', true);
        if (isset($items['wcpa_meta'])) {
            $wcpa_meta = $items['wcpa_meta'];
            if (isset($wcpa_meta['value']) && is_array($wcpa_meta['value'])) {
                foreach ($wcpa_meta['value'] as $item_id => $data) {
                    if (!$item = WC_Order_Factory::get_order_item(absint($item_id))) {
                        continue;
                    }

                    //$meta_data = wc_get_order_item_meta($item_id, WCPA_ORDER_META_KEY, true);
                    $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);


//                    $meta_info = $item->get_meta('_wcpa_meta_key_info');
                    //$meta_info = wc_get_order_item_meta('_wcpa_meta_key_info');
                    foreach ($meta_data as $k => $v) {
                        $meta_id = $meta_data[$k]['meta_id'];
                        $is_ver_1_data = true;
                        if (is_array($meta_data[$k]['value'])) {
                            $first = current($meta_data[$k]['value']);

                            if (isset($first['i'])) {
                                $is_ver_1_data = false;
                            }
                        }

                        if (isset($data[$k])) {
                            $meta_value_temp = array('type' => false, 'value' => false, 'price' => FALSE);
                            //sanitization has to do
                            if ($v['type'] == 'file') {
                                if ($meta_data[$k]['value']['url'] !== trim($data[$k])) { // check if has made any changes to file value/url
                                    $meta_data[$k]['value']['url'] = trim(sanitize_text_field($data[$k]));
                                    $meta_data[$k]['value']['path'] = FALSE;
                                    $meta_data[$k]['value']['file_name'] = wp_basename($data[$k]);
                                    $file_type = wp_check_filetype($data[$k]);
                                    $meta_value_temp['value'] = $meta_data[$k]['value'];
                                    $meta_value_temp['type'] = $v['type'];
                                    $meta_data[$k]['value']['type'] = $file_type['type'];
                                }
                            } else if ($v['type'] == 'placeselector') {

                                $meta_data[$k]['value']['formated'] = $this->sanitize_values($data[$k]['formated'], $v['type']);
                                $splited = ['street_number', 'route', 'locality', 'administrative_area_level_1', 'postal_code', 'country'];
                                foreach ($splited as $fl_name) {
                                    if (isset($data[$k][$fl_name])) {
                                        $meta_data[$k]['value']['splited'][$fl_name] = $this->sanitize_values($data[$k][$fl_name], $v['type']);
                                    }
                                }
                                if (isset($data[$k]['lat'])) {
                                    $meta_data[$k]['value']['cords']['lat'] = $this->sanitize_values($data[$k]['lat'], $v['type']);
                                }
                                if (isset($data[$k]['lng'])) {
                                    $meta_data[$k]['value']['cords']['lng'] = $this->sanitize_values($data[$k]['lng'], $v['type']);
                                }
                                $price = array();
                                $price[] = (isset($wcpa_meta['price'][$item_id][$k]) && $wcpa_meta['price'][$item_id][$k]) ?
                                        $wcpa_meta['price'][$item_id][$k] :
                                        false;
                            } else if (is_array($data[$k])) {
                                $meta_value_temp['value'] = array();
                                $price = array();
                                if ($v['type'] == 'image-group') {
                                    foreach ($meta_data[$k]['value'] as $m => $val) {
                                        if (isset($data[$k][$m])) {

                                            $meta_data[$k]['value'][$m]['label'] = $this->sanitize_values($data[$k][$m]['label'], $v['type']);

                                            $meta_data[$k]['value'][$m]['value'] = $this->sanitize_values($data[$k][$m]['value'], $v['type']);
                                            $file_type = wp_check_filetype($meta_data[$k]['value'][$m]['value']);

                                            if (in_array($file_type['type'], array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'))) {
                                                $meta_data[$k]['value'][$m]['image'] = $meta_data[$k]['value'][$m]['value']; // $this->sanitize_values($data[$k][$m]['value'], $v['type']);
                                            } else {
                                                $meta_data[$k]['value'][$m]['image'] = FALSE;
                                            }
                                            $price[$m] = (isset($wcpa_meta['price'][$item_id][$k][$m]) && $wcpa_meta['price'][$item_id][$k][$m]) ?
                                                    $wcpa_meta['price'][$item_id][$k][$m] :
                                                    false;
                                        } else {
                                            unset($meta_data[$k]['value'][$m]);
                                        }
                                    }
                                } else if ($is_ver_1_data) {

                                    $meta_data[$k]['value'] = $this->sanitize_values($data[$k], $v['type']);
                                    $meta_value_temp['value'] = $meta_data[$k]['value'];
                                    $meta_value_temp['type'] = $v['type'];
                                    $meta_value = $this->order_meta_plain($meta_value_temp, $save_price);
                                    $item->update_meta_data($v['label'], $meta_value, $meta_id);
                                    $price = (isset($wcpa_meta['price'][$item_id][$k]) && $wcpa_meta['price'][$item_id][$k]) ?
                                            $wcpa_meta['price'][$item_id][$k] :
                                            false;
                                } else {
                                    foreach ($meta_data[$k]['value'] as $m => $val) {
                                        if (isset($data[$k][$m])) {
                                            $meta_data[$k]['value'][$m]['label'] = $this->sanitize_values($data[$k][$m]['label'], $v['type']);
                                            $meta_data[$k]['value'][$m]['value'] = $this->sanitize_values($data[$k][$m]['value'], $v['type']);
                                            $price[$m] = (isset($wcpa_meta['price'][$item_id][$k][$m]) && $wcpa_meta['price'][$item_id][$k][$m]) ?
                                                    $wcpa_meta['price'][$item_id][$k][$m] :
                                                    false;
                                        } else {
                                            unset($meta_data[$k]['value'][$m]);
                                        }
                                    }
                                }
                            } else {
                                if ($v['type'] !== 'paragraph' && $v['type'] !== 'header') {
                                    $meta_data[$k]['value'] = $this->sanitize_values($data[$k], $v['type']);
                                }

                                $price = (isset($wcpa_meta['price'][$item_id][$k]) && $wcpa_meta['price'][$item_id][$k]) ?
                                        $wcpa_meta['price'][$item_id][$k] :
                                        false;
                            }

                            $meta_value_temp['value'] = $meta_data[$k]['value'];
                            $meta_value_temp['type'] = $v['type'];
                            $meta_data[$k]['price'] = $price;
                            $meta_value_temp['price'] = $price;

                            $meta_value = $this->order_meta_plain($meta_value_temp, $save_price);

                            $item->update_meta_data($v['label'], $meta_value, $meta_id);
                            //wc_update_order_item_meta($item_id, $v['label'], $meta_value);
                        } else {
                            $item->delete_meta_data_by_mid($meta_id);
                            unset($meta_data[$k]);
                        }
                    }
                    $item->update_meta_data(WCPA_ORDER_META_KEY, $meta_data);
                    $item->save();
//                    wc_update_order_item_meta($item_id, WCPA_ORDER_META_KEY, $meta_data);
                }
            }
        }
    }

}
