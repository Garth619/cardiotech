<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Form {

    /**
     * Constructor function
     */
    private $cart_error = false;
    private $data = null;
    private $relations = array();
    private $price_depends = array();
    private $product = false;
    public $_token;
    public $settings = array();
    private $tax_per_unit = false;
    private $conversion_unit = false;
    private $submited_data = array();

    public function __construct() {
        $this->_token = WCPA_TOKEN;
    }

    public function get_form($form_id = false) {
        $this->data = array();
        if ($form_id) {
            $json_string = get_post_meta($form_id, WCPA_FORM_META_KEY, true);

            $this->data = json_decode($json_string);
        } else {
            $post_ids = get_posts(array('post_type' => WCPA_POST_TYPE, 'fields' => 'ids', 'posts_per_page' => -1));
            foreach ($post_ids as $id) {
                $json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);
                $json_encoded = json_decode($json_string);
                if ($json_encoded && is_array($json_encoded)) {
                    $this->data = array_unique(array_merge($this->data, $json_encoded));
                }
            }
        }
    }

    public function set_product($product) {
        if (is_object($product)) {
            $this->product = $product;
        } else {
            $this->product = wc_get_product($product);
        }
    }

    public function get_wcpa_products($xcld_drct_prchsbl = false) {
        global $wpdb;

        if (false === ( $pro_ids = get_transient(WCPA_PRODUCTS_TRANSIENT_KEY) )) {

            if ($xcld_drct_prchsbl) {
                $post_ids = get_posts(
                        array(
                            'fields' => 'ids',
                            'post_type' => WCPA_POST_TYPE,
                            'posts_per_page' => -1,
                            'meta_query' => array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'wcpa_drct_prchsble',
                                    'value' => false,
                                    'type' => 'BOOLEAN',
                                ),
                                array(
                                    'key' => 'wcpa_drct_prchsble',
                                    'compare' => 'NOT EXISTS'
                                ),
                            )
                        )
                );
            } else {
                $post_ids = get_posts(
                        array(
                            'fields' => 'ids',
                            'post_type' => WCPA_POST_TYPE,
                            'posts_per_page' => -1
                        )
                );
            }



            if ($post_ids && count($post_ids)) {

                $query = "SELECT distinct object_id from $wpdb->term_relationships where term_taxonomy_id"
                        . " in (select tr.term_taxonomy_id from $wpdb->term_relationships as tr left join $wpdb->term_taxonomy as tt on(tt.term_taxonomy_id=tr.term_taxonomy_id) where tr.object_id in (" . implode(',', $post_ids) . ")"
                        . "and  tt.taxonomy = 'product_cat')";

                $pro_ids = $wpdb->get_col($query);
                if (count($pro_ids)) {
                    $pro_ids = get_posts(
                            array(
                                'fields' => 'ids',
                                'post_type' => 'product',
                                'posts_per_page' => -1,
                                'include' => $pro_ids,
                                'meta_query' => array(
                                    'relation' => 'OR',
                                    array(
                                        'key' => 'wcpa_exclude_global_forms',
                                        'value' => false,
                                        'type' => 'BOOLEAN',
                                    ),
                                    array(
                                        'key' => 'wcpa_exclude_global_forms',
                                        'compare' => 'NOT EXISTS'
                                    ),
                                )
                            )
                    );
                }
                $exluded_ids = get_posts(
                        array(
                            'fields' => 'ids',
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'meta_query' => array(
                                array(
                                    'key' => 'wcpa_exclude_global_forms',
                                    'value' => '1',
                                    'type' => 'BOOLEAN',
                                )
                            )
                        )
                );

                $pro_ids = array_diff($pro_ids, $exluded_ids);

                $temp = array_reduce($post_ids, function($a, $b) {
                    return $a . " `meta_value` LIKE '%:$b;%' OR";
                });
                $temp .= trim($temp, 'OR');
                $pro_ids2 = $wpdb->get_col("SELECT post_id  from $wpdb->postmeta WHERE meta_key = '" . WCPA_PRODUCT_META_KEY . "' and ($temp)");

                if ($pro_ids2) {
                    $pro_ids = array_unique(array_merge($pro_ids, $pro_ids2));
                }
            } else {
                $pro_ids = array();
            }
            set_transient(WCPA_PRODUCTS_TRANSIENT_KEY, $pro_ids, 12 * HOUR_IN_SECONDS);
        }

//dividing with 60 will give the execution time in minutes otherwise seconds

        return $pro_ids;
    }

    public function get_form_ids($product_id) {
        $key_1_value = get_post_meta($product_id, 'wcpa_exclude_global_forms', true);
        $post_ids = array();
        $ml = new WCPA_Ml();
        if (empty($key_1_value)) {

            $post_ids = get_posts(
                    array(
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'ids',
                                'include_children' => false,
                                'terms' => wp_get_object_terms($product_id, 'product_cat', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids'))
                            )
                        ),
                        'fields' => 'ids',
                        'post_type' => WCPA_POST_TYPE,
                        'posts_per_page' => -1
                    )
            );
        }
        $form_ids_set2 = get_post_meta($product_id, WCPA_PRODUCT_META_KEY, true);

        if ($form_ids_set2) {
            $post_ids = array_unique(array_merge($post_ids, $form_ids_set2));
        }

        if ($ml->is_active()) {
            $post_ids = $ml->lang_object_ids($post_ids, 'post', false);
        }

        return $post_ids;
    }

    public function check_field_price_dependency($price_formula) {
        $matches = false;

        if (preg_match_all('/\{(\s)*?field\.([^}]*)}/', $price_formula, $matches)) {
            $ids = array();
            foreach ($matches[2] as $match) {
                $ele = explode('.', $match);
                if (is_array($ele) && count($ele) > 1 && in_array($ele[1], ['value', 'price'])) {
                    $ids[] = $ele[0];
                }
            }

            return array_unique($ids);
        } else {
            return false;
        }
    }

    public function get_forms_by_product($product_id = false) {


        if (($this->data === null || empty($this->data)) && $product_id) {
            $this->data = array();
            $this->cart_error = WCPA_Front_End::get_cart_error($product_id);

            $post_ids = $this->get_form_ids($product_id);

            $pric_overide_base_price = false;
            $pric_overide_base_price_if_gt_zero = false;
            $pric_cal_option_once = false;
            $pric_use_as_fee = false;
            $render_after_acb = false;
            $render_before_acb = false;

            $enable_recaptcha = false;


            if (wcpa_get_option('form_loading_order_by_date') === true) {

                if (is_array($post_ids) && count($post_ids)) {
                    $post_ids = get_posts(array(
                        'posts_per_page' => -1,
                        'include' => $post_ids,
                        'fields' => 'ids',
                        'post_type' => WCPA_POST_TYPE,
                        'posts_per_page' => -1
                    ));
                }
            }

            foreach ($post_ids as $id) {
                if (get_post_status($id) == 'publish') {

                    $json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);

                    $json_encoded = json_decode($json_string);


                    if ($pric_cal_option_once === false) {
                        $pric_cal_option_once = wcpa_get_post_meta($id, 'pric_cal_option_once', false);
                    }
                    if ($enable_recaptcha === false) {
                        $enable_recaptcha = wcpa_get_post_meta($id, 'enable_recaptcha', false);
                    }

                    if ($pric_use_as_fee === false) {
                        $pric_use_as_fee = wcpa_get_post_meta($id, 'pric_use_as_fee', false);
                    }

                    if ($pric_overide_base_price === false) {
                        $pric_overide_base_price = wcpa_get_post_meta($id, 'pric_overide_base_price', false);
                    }
                    if ($pric_overide_base_price_if_gt_zero === false) {
                        $pric_overide_base_price_if_gt_zero = wcpa_get_post_meta($id, 'pric_overide_base_price_if_gt_zero', false);
                    }


                    if ($render_after_acb === false) {
                        $render_after_acb = wcpa_get_post_meta($id, 'render_after_acb', false);
                    }
                    $cont_global = wcpa_get_post_meta($id, 'cont_use_global', true);
                    $form_rules = [
                        'pric_cal_option_once' => wcpa_get_post_meta($id, 'pric_cal_option_once', false),
                        'pric_use_as_fee' => wcpa_get_post_meta($id, 'pric_use_as_fee', false),
                        'fee_label' => ($cont_global ? wcpa_get_option('fee_label', 'Fee', true) : wcpa_get_post_meta($id, 'fee_label', wcpa_get_option('fee_label', 'Fee'))),
                        'render_after_acb' => wcpa_get_post_meta($id, 'render_after_acb', false),
                        'disp_hide_options_price' => wcpa_get_post_meta($id, 'disp_hide_options_price', false),
                    ];
                    if ($json_encoded && is_array($json_encoded)) {

                        foreach ($json_encoded as $k => $v) {

                            if (isset($v->enableCl) && $v->enableCl && isset($v->relations) && is_array($v->relations)) {

                                foreach ($v->relations as $val) {

                                    foreach ($val->rules as $k) {
                                        if (!empty($k->rules->cl_field)) {
                                            if (!isset($this->relations[$k->rules->cl_field])) {
                                                $this->relations[$k->rules->cl_field] = array();
                                            }

                                            if ($k->rules->cl_field === 'attribute' && $k->rules->cl_relation) {
                                                $atr = wc_get_attribute($k->rules->cl_relation);

                                                $term = get_term_by('id', $k->rules->cl_val->value, $atr->slug);
                                                $k->rules->cl_val = $term->slug;
                                                $k->rules->cl_relation = $atr->slug;
                                            }
                                            $this->relations[$k->rules->cl_field][] = (isset($v->elementId) ? $v->elementId : FALSE);
                                        }
                                    }
                                }
                            }
                            if ($pric_use_as_fee === false) {
                                if (isset($v->enablePrice) && $v->enablePrice && isset($v->use_as_fee) && $v->use_as_fee) {
                                    $pric_use_as_fee = true;
                                }
                            }

                            if (isset($v->enablePrice) && $v->enablePrice && isset($v->pricingType) && $v->pricingType === 'custom') {

                                if (isset($v->priceOptions) && $v->priceOptions == 'different_for_all') {

                                    foreach ($v->values as $e) {

                                        if ($matches = $this->check_field_price_dependency($e->price)) {
                                            foreach ($matches as $match) {
                                                if (!isset($this->price_depends[$match])) {
                                                    $this->price_depends[$match] = array();
                                                }
                                                if (isset($v->elementId)) {
                                                    if (!in_array($v->elementId, $this->price_depends[$match])) {
                                                        $this->price_depends[$match][] = $v->elementId;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else if (isset($v->price)) {
                                    if ($matches = $this->check_field_price_dependency($v->price)) {
                                        foreach ($matches as $match) {
                                            if (!isset($this->price_depends[$match])) {
                                                $this->price_depends[$match] = array();
                                            }
                                            if (isset($v->elementId)) {
                                                if (!in_array($v->elementId, $this->price_depends[$match])) {
                                                    $this->price_depends[$match][] = $v->elementId;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $v->form_id = $id;
                            $v->form_rules = $form_rules;
                        }

                        $this->data = array_merge($this->data, $json_encoded);
                    }
                }
            }
        }

        $this->data = apply_filters('wcpa_product_form_fields', $this->data, $product_id);

        if (empty($this->settings) && isset($id)) {
            $dis_global = wcpa_get_post_meta($id, 'disp_use_global', true);
            $cont_global = wcpa_get_post_meta($id, 'cont_use_global', true);
            $this->settings = [
                'disp_use_global' => $dis_global,
                'disp_show_field_price' => ($dis_global ? wcpa_get_option('disp_show_field_price', true) : wcpa_get_post_meta($id, 'disp_show_field_price', wcpa_get_option('disp_show_field_price', true))),
                'disp_summ_show_total_price' => ($dis_global ? wcpa_get_option('disp_summ_show_total_price', true) : wcpa_get_post_meta($id, 'disp_summ_show_total_price', wcpa_get_option('disp_summ_show_total_price', true))),
                'disp_summ_show_product_price' => ($dis_global ? wcpa_get_option('disp_summ_show_product_price', true) : wcpa_get_post_meta($id, 'disp_summ_show_product_price', wcpa_get_option('disp_summ_show_product_price', true))),
                'disp_summ_show_option_price' => ($dis_global ? wcpa_get_option('disp_summ_show_option_price', true) : wcpa_get_post_meta($id, 'disp_summ_show_option_price', wcpa_get_option('disp_summ_show_option_price', true))),
                'pric_overide_base_price' => $pric_overide_base_price,
                'pric_overide_base_price_if_gt_zero' => $pric_overide_base_price_if_gt_zero,
                'pric_cal_option_once' => $pric_cal_option_once,
                'pric_use_as_fee' => $pric_use_as_fee,
                'render_after_acb' => $render_after_acb,
                'enable_recaptcha' => ($enable_recaptcha ? $enable_recaptcha : wcpa_get_option('enable_recaptcha', false)),
                'disp_hide_options_price' => wcpa_get_post_meta($id, 'disp_hide_options_price', false),
                'cont_use_global' => $cont_global,
                'options_total_label' => ($cont_global ? wcpa_get_option('options_total_label', 'Options Price', true) : wcpa_get_post_meta($id, 'options_total_label', wcpa_get_option('options_total_label', 'Options Price'))),
                'options_product_label' => ($cont_global ? wcpa_get_option('options_product_label', 'Product Price', true) : wcpa_get_post_meta($id, 'options_product_label', wcpa_get_option('options_product_label', 'Product Price', true))),
                'total_label' => ($cont_global ? wcpa_get_option('total_label', 'Total', true) : wcpa_get_post_meta($id, 'total_label', wcpa_get_option('total_label', 'Total'))),
                'fee_label' => ($cont_global ? wcpa_get_option('fee_label', 'Fee', true) : wcpa_get_post_meta($id, 'fee_label', wcpa_get_option('fee_label', 'Fee')))
            ];
        }
    }

    public function eval_relation($rules) {
        if (!isset($rules->cl_field) || empty($rules->cl_field)) {
            return false;
        }
        if ($rules->cl_relation === '0') {
            return false;
        }
        if ($rules->cl_field === 'quantity') {
            $field = 'quantity';
        } else if ($rules->cl_field === 'attribute') {
            $field = 'attribute';
        } else {
            $field = array_filter($this->data, function($e) use($rules) {
                if (isset($e->elementId) && !empty($e->elementId) && $rules->cl_field === $e->elementId) {
                    return true;
                } else {
                    return false;
                }
            });
        }


        if (is_array($field)) {
            $field = reset($field);
        } else if ($field !== 'quantity' && $field !== 'attribute') {
            return false;
        }
        if ($field) {
            $val = array();
            $is_visible = true;
            if (isset($field->enableCl) && $field->enableCl) {
                if (isset($field->cl_status) && $field->cl_status === 'visible') {
                    $is_visible = true;
                } else if (isset($field->cl_status) && $field->cl_status === 'hidden') {
                    $is_visible = false;
                } else if ($field->cl_rule === 'show') {
                    $is_visible = false;
                } else {
                    $is_visible = true;
                }
            }

            if ($is_visible) {
                if ($field === 'quantity') {
                    if (isset($_REQUEST['quantity'])) {
                        $val[] = (int) $_REQUEST['quantity'];
                    }
                } else if ($field === 'attribute') {
                    $name = 'attribute_' . $rules->cl_relation;
                    if (isset($_REQUEST[$name])) {
                        $val[] = $_REQUEST[$name];
                    } else {
                        return false;
                    }
                } else if (in_array($field->type, ['text', 'color', 'hidden'])) {
                    if (isset($_REQUEST[$field->name])) {
                        $val[] = strtolower($_REQUEST[$field->name]);
                    }
                } else if (in_array($field->type, ['file'])) {
                    if (isset($field->ajax_upload) && $field->ajax_upload === true && isset($_REQUEST[$field->name . '_ajax']) && !empty($_REQUEST[$field->name . '_ajax'])) {
                        $temp = explode('||', $_REQUEST[$field->name . '_ajax']);
                        $val[] = strtolower($temp[1]);
                    } else if ($_FILES[$field->name]["error"] != 4) {
                        $val[] = strtolower($_FILES[$field->name]["name"]);
                    }
                } else if (in_array($field->type, ['number'])) {
                    if (isset($_REQUEST[$field->name])) {
                        $val[] = (float) $_REQUEST[$field->name];
                    }
                } else if (in_array($field->type, ['textarea'])) {
                    if (isset($_REQUEST[$field->name])) {
                        $val[] = strtolower($_REQUEST[$field->name]);
                    }
                } else if (in_array($field->type, ['checkbox-group', 'radio-group', 'select', 'image-group', 'color-group']) && isset($_REQUEST[$field->name])) {
                    if (is_array($_REQUEST[$field->name])) {
                        $_values = $_REQUEST[$field->name];

                        if (in_array($rules->cl_relation, ['contains', 'not_contains', 'starts_with', 'ends_with'])) {
                            array_walk($_values, function(&$a, $b) {
                                strtolower(str_replace('WCPAOTH ', '', $a));
                            }); // using this array_walk method to preserve the keys
                            $val = $_values;
                        } else {
                            foreach ($_values as $l => $v) {
                                if (substr($v, 0, 8) === "WCPAOTH ") {
                                    $val[] = 'other';
                                } else {
                                    $val[] = strtolower($v);
                                }
                            }
                        }
                    } else {
                        if (in_array($rules->cl_relation, ['contains', 'not_contains', 'starts_with', 'ends_with'])) {
                            $val[] = strtolower(str_replace('WCPAOTH ', '', $_REQUEST[$field->name]));
                        } else {
                            if (substr($_REQUEST[$field->name], 0, 8) === "WCPAOTH ") {
                                $val[] = 'other';
                            } else {
                                $val[] = strtolower($_REQUEST[$field->name]);
                            }
                        }
                    }
                } else if (in_array($field->type, ['date', 'datetime-local'])) {
                    if (isset($_REQUEST[$field->name])) {
                        if (in_array($rules->cl_relation, ['contains', 'not_contains', 'starts_with', 'ends_with'])) {
                            $val[] = strtolower($_REQUEST[$field->name]);
                        } else {
                            $val[] = strtotime($_REQUEST[$field->name]);
                        }
                    }
                } else if (in_array($field->type, ['time'])) {
                    if (isset($_REQUEST[$field->name])) {
                        if (in_array($rules->cl_relation, ['contains', 'not_contains', 'starts_with', 'ends_with'])) {
                            $val[] = strtolower($_REQUEST[$field->name]);
                        } else {
                            $val[] = strtotime($_REQUEST[$field->name]);
                        }
                    }
                }
            }
            $rel_val = false;
            if ($field === 'attribute') {
                $rel_val = isset($rules->cl_val->value) ? $rules->cl_val->value : $rules->cl_val;
                if (in_array($rel_val, $val)) {
                    return true;
                } else {
                    return false;
                }
            }

            if (count($val) === 0) {
                switch ($rules->cl_relation) {
                    case 'is_empty':
                        return true;
                    case 'is_not_empty':
                        return false;
                    default:
                        return false;
                }
            }


            if (isset($rules->cl_val) && $field == 'quantity') {
                $rel_val = (float) (((isset($rules->cl_val->value)) ? $rules->cl_val->value : $rules->cl_val));
            } else if (isset($rules->cl_val) && $field->type == 'image-group') {
                $rel_val = strtolower(((isset($rules->cl_val->i)) ? $rules->cl_val->i : $rules->cl_val));
            } else if ($field->type == 'datetime-local' || $field->type == 'date' || $field->type == 'time') {
                if (in_array($rules->cl_relation, ['contains', 'not_contains', 'starts_with', 'ends_with'])) {
                    $rel_val = strtolower($rules->cl_val);
                } else {
                    $rel_val = strtotime($rules->cl_val);
                }
            } else if (isset($rules->cl_val) && $field->type == 'number') {
                $rel_val = (float) (((isset($rules->cl_val->value)) ? $rules->cl_val->value : $rules->cl_val));
            } else if (isset($rules->cl_val)) {
                $rel_val = strtolower((((isset($rules->cl_val->value)) ? $rules->cl_val->value : $rules->cl_val)));
            }

            switch ($rules->cl_relation) {
                case 'is':

                    if (in_array($rel_val, $val)) {
                        return true;
                    } else {
                        return false;
                    }
                case 'is_not':
                    if (in_array($rel_val, $val)) {
                        return false;
                    } else {
                        return true;
                    }
                case 'is_empty':
                    if (count($val) === 0 || reset($val) === '') {
                        return true;
                    } else {
                        return false;
                    }
                case 'is_not_empty':
                    if (count($val) === 0 || reset($val) === '') {
                        return false;
                    } else {
                        return true;
                    }
                case 'is_greater':
                    foreach ($val as $v) {
                        if ($v > $rel_val) {
                            return true;
                        }
                    }
                    return false;
                case 'is_lessthan':
                    foreach ($val as $v) {
                        if ($v >= $rel_val) {
                            return false;
                        }
                    }
                    return true;
                case 'is_greater_or_eqaul':
                    foreach ($val as $v) {
                        if ($v >= $rel_val) {
                            return true;
                        }
                    }
                    return false;
                case 'is_lessthan_or_eqal':
                    foreach ($val as $v) {
                        if ($v > $rel_val) {
                            return false;
                        }
                    }
                    return true;
                case 'contains':
                    foreach ($val as $v) {
                        if (strpos($v, $rel_val) !== false) {
                            return true;
                        }
                    }
                    return false;

                case 'not_contains':
                    foreach ($val as $v) {
                        if (strpos($v, $rel_val) !== false) {
                            return false;
                        }
                    }
                    return true;
                case 'starts_with':
                    foreach ($val as $v) {
                        if (strpos($v, $rel_val) === 0) {
                            return true;
                        }
                    }
                    return false;
                case 'ends_with':
                    foreach ($val as $v) {
                        $strlen = strlen($v);
                        $testlen = strlen($rel_val);
                        if ($testlen <= $strlen) {
                            if (substr_compare($v, $rel_val, $strlen - $testlen, $testlen) === 0) {
                                return true;
                            }
                        }
                    }
                    return false;
                case 'date_is':
                case 'time_is':
                    if (in_array($rel_val, $val)) {
                        return true;
                    } else {
                        return false;
                    }

                case 'date_is_before':
                case 'time_is_before':
                    foreach ($val as $v) {
                        if ($v >= $rel_val) {
                            return false;
                        }
                    }
                    return true;
                case 'date_is_after':
                case 'time_is_after':
                    foreach ($val as $v) {
                        if ($v > $rel_val) {
                            return true;
                        }
                    }
                    return false;
            }
        } else {
            return false;
        }

        return false;
    }

    public function process_cl_logic($product_id) {

        $this->get_forms_by_product($product_id);
        $processed_ids = array();
        foreach ($this->data as $l => $v) {
            $processed_ids[] = isset($v->elementId) ? $v->elementId : false;
            if (isset($v->enableCl) && $v->enableCl && isset($v->relations) && is_array($v->relations)) {
                $evel_str = '';

                foreach ($v->relations as $val) {
                    if (is_array($val->rules) && count($val->rules)) {
                        $evel_str .= '(';
                        foreach ($val->rules as $k) {
                            $evel_str .= '(';
                            if ($this->eval_relation($k->rules)) {
                                $evel_str .= ' true ';
                            } else {
                                $evel_str .= ' false ';
                            }
                            $evel_str .= ') ' . (($k->operator !== false) ? $k->operator : '') . ' ';
                        }

                        if (count($val->rules) > 0) {
                            preg_match_all('/\(.*\)/', $evel_str, $match);
                            $evel_str = $match[0][0] . ' ';
                        }

                        $evel_str .= ') ' . (($val->operator !== false) ? $val->operator : '') . ' ';
                    }
                }
                if (count($v->relations) > 0) {
                    preg_match_all('/\(.*\)/', $evel_str, $match);
                    $evel_str = $match[0][0] . ' ';
                }

                $evel_str = str_replace(['and', 'or'], ['&&', '||'], $evel_str);

                $result = eval('return ' . $evel_str . ';');
                $status = (isset($this->data[$l]->cl_status) ? $this->data[$l]->cl_status : false);

                if ($result === true) {
                    if ($v->cl_rule === 'show') {
                        $this->data[$l]->cl_status = 'visible';
                    } else {
                        $this->data[$l]->cl_status = 'hidden';
                    }
                } else {
                    if ($v->cl_rule === 'show') {
                        $this->data[$l]->cl_status = 'hidden';
                    } else {
                        $this->data[$l]->cl_status = 'visible';
                    }
                }

                if (isset($this->relations[$v->elementId]) && $status !== $this->data[$l]->cl_status) {
                    foreach ($this->relations[$v->elementId] as $id) {
                        if (in_array($id, $processed_ids)) {
                            //go back and iterate again
                            $this->process_cl_logic($product_id);
                        }
                    }
                    //start processing logic from start again if this element is related with any other logic
                }
            }
        }
    }

    private function find_submited_data_by_id($element_id) {
        if (count($this->submited_data)) {
            foreach ($this->submited_data as $data) {
                if ($element_id == $data['form_data']->elementId) {
                    return $data;
                }
            }
        }

        return false;
    }

    private function find_element_by_id($element_id) {

        if (count($this->data)) {
            foreach ($this->data as $v) {
                if ($element_id == $v->elementId) {
                    return $v;
                }
            }
        }

        return false;
    }

    public function ajax_upload($details) {

        $response = [['status' => false, 'message' => '']];
        if (isset($_FILES['wcpa_file']["error"]) && $_FILES['wcpa_file']["error"] != 4) {
            $status = true;
            $error_message = '';
            $product_id = $details->product_id;
            $this->get_forms_by_product($product_id);
            $v = $this->find_element_by_id($details->element_id);
            if ($v === false || $v->type !== 'file') {
                $response = ['status' => false, 'message' => _('Invalid')];
                return $response;
            }
            if ($_FILES['wcpa_file']["error"] != UPLOAD_ERR_OK && $_FILES['wcpa_file']["error"] != UPLOAD_ERR_NO_FILE) {
                if ($_FILES['wcpa_file']["error"] == UPLOAD_ERR_INI_SIZE) {
                    $error_message = __('The uploaded file exceeds the maximum upload limit');
                } else if (in_array($_FILES['wcpa_file']["error"], array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE))) {
                    $error_message = __('The uploaded file exceeds the maximum upload limit');
                } else {
                    $error_message = __('The uploaded file error');
                }
            } else if (isset($_FILES['wcpa_file']) && $_FILES['wcpa_file']["error"] === UPLOAD_ERR_OK) {

                if (isset($v->uploadsize) && $v->uploadsize && ($_FILES['wcpa_file']["size"] > ($v->uploadsize * 1024 * 1024))) {
                    $status = FALSE;
                    $error_message = (sprintf(__('File exceeds maximum upload size limit of %s MB'), $v->uploadsize));
                }
//                $supported_extions = false;
//                if (isset($v->exts_supported) && !empty($v->exts_supported)) {
//                    $supported_extions = preg_split("/[\s,]+/", $v->exts_supported);
//                    if (is_array($supported_extions)) {
//                        $supported_extions = array_filter(array_map('trim', $supported_extions));
//                        if (count($supported_extions) == 0) {
//                            $supported_extions = false;
//                        }
//                    } else {
//                        $supported_extions = false;
//                    }
//                }

                $validate = wp_check_filetype_and_ext($_FILES['wcpa_file']['tmp_name'], $_FILES['wcpa_file']['name']);

                if (!$validate['ext']) {
                    $status = FALSE;
                    $error_message = __('The uploaded file type is not supported');
                }

                if (isset($v->exts_supported) && !$this->validate_file_with_config($v->exts_supported, $_FILES['wcpa_file']['name'])) {
                    $status = FALSE;
                    $error_message = __('The uploaded file type is not supported');
                }
//                if ($supported_extions !== false) {
//                    $ext = pathinfo($_FILES['wcpa_file']['name'], PATHINFO_EXTENSION);
//                    if (!in_array($ext, $supported_extions)) {
//                        $status = FALSE;
//                        $error_message = __('The uploaded file type is not supported');
//                    }
//                }
                if ($status === false) {
                    $response = ['status' => $status, 'message' => $error_message];
                } else {
                    $file = $this->handle_upload_ajax($v);
                    if ($file['status'] === false) {
                        $response = ['status' => false, 'message' => $file['error']];
                    } else {
                        $response = ['status' => true, 'message' => '', 'file' => $file['file']];
                    }
                }

                return $response;
            }
        }
        $response = [['status' => false, 'message' => $_FILES['wcpa_file']["error"]]];
        return $response;
    }

    public function is_recaptcha_valid() {

        // Make sure this is an acceptable type of submissions
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
            try {
                $url = 'https://www.google.com/recaptcha/api/siteverify';
                $data = ['secret' => wcpa_get_option('recaptcha_secret_key', ''),
                    'response' => $captcha];
                $options = [
                    'http' => [
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method' => 'POST',
                        'content' => http_build_query($data)
                    ]
                ];
                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                return json_decode($result)->success;
            } catch (Exception $e) {
                return null;
            }
        }
        // Not a POST request, set a 403 (forbidden) response code.
        else {
            return false;
        }
    }

    public function validate_form_data($product_id = false) {

        $this->get_forms_by_product($product_id);
        $status = true;
        if (isset($this->settings['enable_recaptcha']) && $this->settings['enable_recaptcha'] === true) {
            if ($this->is_recaptcha_valid() !== true) {
                $status = FALSE;
                $this->add_cart_error(__('Please verify you are not a bot', 'wcpa-text-domain'));
                WCPA_Front_End::set_cart_error($product_id, !$status);
                return $status;
            }
        }

        $this->process_cl_logic($product_id);


        foreach ($this->data as $v) {
            if (!isset($v->cl_status) || $v->cl_status === 'visible') {
                if ($v->type != 'file' && isset($v->required) && $v->required && (!isset($_REQUEST[$v->name]) || $_REQUEST[$v->name] == "")) {
                    $status = FALSE;
                    $this->add_cart_error(sprintf(__('Field %s is required', 'wcpa-text-domain'), $v->label));
                }
                if ($v->type == 'file') {
                    if (isset($v->ajax_upload) && $v->ajax_upload === true && isset($_REQUEST[$v->name . '_ajax']) && !empty($_REQUEST[$v->name . '_ajax'])) {
                        $temp = explode('||', $_REQUEST[$v->name . '_ajax']);
                        if (isset($v->required) && $v->required && (empty($temp[0]))) {
                            $status = FALSE;
                            $this->add_cart_error(sprintf(__('Field %s is required'), $v->label));
                        }
                    } else {
                        if (isset($v->required) && $v->required && (!isset($_FILES[$v->name]) || $_FILES[$v->name]["error"] == UPLOAD_ERR_NO_FILE)) {
                            $status = FALSE;
                            $this->add_cart_error(sprintf(__('Field %s is required'), $v->label));
                        } else if (isset($_FILES[$v->name]) && $_FILES[$v->name]["error"] != UPLOAD_ERR_OK && $_FILES[$v->name]["error"] != UPLOAD_ERR_NO_FILE) {
                            $status = FALSE;
                            if ($_FILES[$v->name]["error"] == UPLOAD_ERR_INI_SIZE) {
                                $error_message = __('The uploaded file exceeds the maximum upload limit for field %s');
                            } else if (in_array($_FILES[$v->name]["error"], array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE))) {
                                $error_message = __('The uploaded file exceeds the maximum upload limit for field %s');
                            } else {
                                $error_message = __('The uploaded file error for field %s');
                            }

                            $this->add_cart_error(sprintf($error_message, $v->label));
                        } else if (isset($_FILES[$v->name]) && $_FILES[$v->name]["error"] === UPLOAD_ERR_OK) {
                            if (isset($v->uploadsize) && $v->uploadsize && ($_FILES[$v->name]["size"] > ($v->uploadsize * 1024 * 1024))) {
                                $status = FALSE;
                                $this->add_cart_error(sprintf(__('Field %s exceeds maximum upload size limit of %s MB'), $v->label, $v->uploadsize));
                            }
                            $validate = wp_check_filetype_and_ext($_FILES[$v->name]['tmp_name'], $_FILES[$v->name]['name']);
                            if (!$validate['ext']) {
                                $status = FALSE;
                                $this->add_cart_error(sprintf(__('The uploaded file type is not supported for field %s'), $v->label));
                            }

                            if (isset($v->exts_supported) && !$this->validate_file_with_config($v->exts_supported, $_FILES[$v->name]['name'])) {
                                $status = FALSE;
                                $this->add_cart_error(sprintf(__('The uploaded file type is not supported for field %s'), $v->label));
                            }
                        }
                    }
                }
            }
        }

        WCPA_Front_End::set_cart_error($product_id, !$status);

        return $status;
    }

    private function add_cart_error($message) {
        wc_add_notice($message, 'error');
    }

    public function findKey($array, $keySearch) {

        foreach ($array as $key => $item) {

            if (!empty($item) && is_string($item)) {
                parse_str($item, $item);
            }

            if ($key == $keySearch) {

                return $array;
            } elseif (is_array($item) && ($data = $this->findKey($item, $keySearch)) !== false) {

                return $data;
            }
        }
        return false;
    }

    public function find_meta_by_name($name, $meta_data) {

        $arr = array_filter($meta_data, function($v) use($name) {
            return $v['name'] === $name;
        });

        if ($arr !== false) {
            return reset($arr);
        } else {
            return false;
        }
    }

    public function order_again_item_data($item) {
        $product_id = $item->get_product_id();
        $this->get_forms_by_product($product_id);
        $this->set_product($product_id);
        $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);
        $submited_data = array();
        foreach ($this->data as $k => $v) {

            $form_data = clone $v;
            unset($form_data->values); //avoid saving large number of data
            unset($form_data->className); //avoid saving no use data
            unset($form_data->relations); //avoid saving no use data

            if (!in_array($v->type, array('header', 'separator'))) {
                $meta = $this->find_meta_by_name($v->name, $meta_data);
                if (is_array($meta['value']) && in_array($v->type, ['select', 'checkbox-group', 'radio-group', 'image-group', 'color-group'])) {
                    //check the options are selectable
                    $value_data = array(); // $meta['value'];

                    foreach ($meta['value'] as $j => $_v) {
                        $flag = false;
                        if (is_array($_v)) {
                            foreach ($v->values as $newkey => $_v2) {
                                if ((isset($_v2->value) && $_v2->value === $_v['value']) || (isset($_v2->image) && $_v2->image === $_v['value'])) {
                                    $flag = true;
                                    break;
                                }
                            }
                        }
                        if ($flag === false) {
                            if (isset($v->other) && $v->other && !isset($_v['value']['other'])) {
                                $value_data['other'] = $_v;
                                $value_data['other']['i'] = 'other';
                                $value_data['other']['label'] = 'Other';
                            }
                        } else {
                            $value_data[$newkey] = $_v;
                            $value_data[$newkey]['i'] = $newkey;
                        }
                    }
                    if ($v->type !== 'checkbox-group' && (!isset($v->multiple) || !$v->multiple)) {//no multi option
                        if (count($value_data) > 1 && isset($value_data['other'])) {
                            unset($value_data['other']);
                        }

                        $value_data = array_slice($value_data, 0, 1, true);
                    }
                } else {
                    $value_data = $meta['value'];
                }
                if (isset($meta['value'])) {
                    $submited_data[] = array(
                        'type' => $v->type,
                        'name' => $v->name,
                        'label' => (isset($v->label)) ? $v->label : '',
                        'value' => $value_data,
                        'price' => $this->element_price($v, $product_id, $value_data, $value_data),
                        'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                        'form_data' => $form_data
                    );
                }
            }
        }
        return $submited_data;
    }

    public function submited_data($product_id, $variation_id) {

        $this->get_forms_by_product($product_id);

        $this->process_cl_logic($product_id);

        if ($variation_id && $variation_id !== 0) {
            $product_id = $variation_id;
        }
        $this->set_product($product_id);

        $this->submited_data = array();
        if (count($this->data)) {

            if (!isset($_POST['wcpa_field_key_checker']) && is_array($_POST)) {

                $item = $this->findKey($_POST, 'wcpa_field_key_checker');

                if (is_array($item)) {
                    $_POST = array_merge($item, $_POST);
                    $_REQUEST = array_merge($item, $_REQUEST);
                }
            }
        }

        $hide_empty = wcpa_get_option('hide_empty_data', false);
        $zero_as_empty = false;
        if ($hide_empty) {
            $zero_as_empty = apply_filters('wcpa_zero_as_empty', false);
        }


        foreach ($this->data as $k => $v) {

            $form_data = clone $v;
            unset($form_data->values); //avoid saving large number of data
            unset($form_data->className); //avoid saving no use data
            unset($form_data->relations); //avoid saving no use data


            if (!in_array($v->type, array('separator')) && (!isset($v->cl_status) || $v->cl_status == 'visible')) {

                if ($v->type == 'file') {
                    if (isset($v->ajax_upload) && $v->ajax_upload === true && isset($_REQUEST[$v->name . '_ajax']) && !empty($_REQUEST[$v->name . '_ajax'])) {


                        $temp = explode('||', $_REQUEST[$v->name . '_ajax']);

                        $uplaoded_file = $this->move_file($v, $temp);
                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $uplaoded_file,
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => (isset($uplaoded_file['url']) && !empty($uplaoded_file['url'])) ? $this->element_price($v, $product_id) : false, 'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                            'form_data' => $form_data
                        );
                    } else {
                        if (isset($_FILES[$v->name]["error"]) && $_FILES[$v->name]["error"] != 4) {
                            $uplaoded_file = $this->handle_upload($v);
                            $this->submited_data[] = array(
                                'type' => $v->type,
                                'name' => $v->name,
                                'label' => (isset($v->label)) ? $v->label : '',
                                'value' => $uplaoded_file,
                                'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                                'price' => (isset($uplaoded_file['url']) && !empty($uplaoded_file['url'])) ? $this->element_price($v, $product_id) : false, 'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                                'form_data' => $form_data
                            );
                        }
                    }
                } else if (in_array($v->type, array('paragraph', 'header'))) {
                    if (isset($v->show_in_checkout) && $v->show_in_checkout == true) {
                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->elementId,
                            'label' => WCPA_EMPTY_LABEL,
                            'value' => (isset($v->label)) ? $v->label : '',
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                            'form_data' => $form_data
                        );
                    } else {
                        continue;
                    }
                } else if (isset($_REQUEST[$v->name]) && !($hide_empty && $_REQUEST[$v->name] === '') && !($hide_empty && $zero_as_empty && ($_REQUEST[$v->name] === 0 || $_REQUEST[$v->name] === '0') )
                ) {

                    if (in_array($v->type, ['select', 'radio-group'])) {
                        $values = $_REQUEST[$v->name]; // $this->sanitize_values($v);
                        $values_data = array();
                        if (!is_array($values)) {
                            $values = array($values);
                        }
                        foreach ($values as $l => $val) {
                            $item = FALSE;
                            foreach ($v->values as $j => $_v) {
                                if ($_v->value === $val) {
                                    $item = $_v;
                                    break;
                                }
                            }
                            if ($item === FALSE) {
                                $item = new stdClass;
                                $val = str_replace('WCPAOTH ', '', $val);
                                $item->label = 'Other'; // no i18n applied here, will show at displying end
                                $j = 'other';
                            }

                            $values_data[$j] = array(
                                'i' => $j,
                                'value' => $this->sanitize_values($val),
                                'label' => isset($item->label) ? $this->sanitize_values($item->label) : FALSE);
                        }

                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $values_data,
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id, $values_data),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id, $values_data),
                            'form_data' => $form_data
                        );
                    } else if (in_array($v->type, ['checkbox-group'])) {
                        $values = $this->sanitize_values($v);
                        $values_data = array();
                        if (!is_array($values)) {
                            $values = array($values);
                        }
                        foreach ($values as $l => $val) {

                            $item = isset($v->values[$l]) ? $v->values[$l] : FALSE;
                            if ($item === FALSE) {
                                $item = new stdClass;
                                $val = str_replace('WCPAOTH ', '', $val);
                                $item->label = 'Other'; // no i18n applied here, will show at displying end
                            }
                            $values_data[$l] = array('i' => $l, 'value' => $val, 'label' => $item->label);
                        }

                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $values_data,
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                            'form_data' => $form_data
                        );
                    } else if (in_array($v->type, ['image-group'])) {
                        $values = $this->sanitize_values($v);

                        $values_data = array();
                        if (!is_array($values)) {
                            $values = array($values);
                        }
                        if ((isset($v->multiple) && $v->multiple)) {
                            $is_multiple = true;
                        } else {
                            $is_multiple = false;
                        }
                        if ($is_multiple) {
                            foreach ($values as $l => $val) {
                                $item = isset($v->values[$l]) ? $v->values[$l] : FALSE;
                                if ($item === FALSE) {
                                    $item = new stdClass;
                                    $item->label = 'Other'; // no i18n applied here, will show at displying end
                                    $val = str_replace('WCPAOTH ', '', $val);
                                }
                                $values_data[$l] = array(
                                    'i' => $l,
                                    'value' => $val,
                                    'label' => isset($item->label) ? $item->label : FALSE,
                                    'image' => isset($item->image) ? $item->image : FALSE);
                            }
                        } else {
                            foreach ($values as $l => $val) {
                                $item = FALSE;
                                foreach ($v->values as $j => $_v) {
                                    if ((string) $j === $val) {//
                                        $item = $_v;
                                        break;
                                    }
                                }
                                if ($item === FALSE) {
                                    $item = new stdClass;
                                    $val = str_replace('WCPAOTH ', '', $val);
                                    $item->label = 'Other'; // no i18n applied here, will show at displying end
                                    $j = 'other';
                                }

                                $values_data[$j] = array(
                                    'i' => $j,
                                    'value' => $val,
                                    'label' => isset($item->label) ? $item->label : FALSE,
                                    'image' => isset($item->image) ? $item->image : FALSE
                                );
                            }
                        }

                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $values_data,
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id, $values_data),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                            'form_data' => $form_data
                        );
                    } else if (in_array($v->type, ['placeselector'])) {
                        $value = array(
                            'formated' => $this->sanitize_values($v),
                            'splited' => array(),
                            'cords' => array()
                        );
                        $splited = ['street_number', 'route', 'locality', 'administrative_area_level_1', 'postal_code', 'country'];
                        foreach ($splited as $fl_name) {
                            if (isset($_REQUEST[$v->name . '_' . $fl_name])) {
                                $value['splited'][$fl_name] = $this->sanitize_values($_REQUEST[$v->name . '_' . $fl_name]);
                            }
                        }
                        if (isset($_REQUEST[$v->name . '_lat'])) {
                            $value['cords']['lat'] = $this->sanitize_values($_REQUEST[$v->name . '_lat']);
                        }
                        if (isset($_REQUEST[$v->name . '_lng'])) {
                            $value['cords']['lng'] = $this->sanitize_values($_REQUEST[$v->name . '_lng']);
                        }
                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $value,
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                            'form_data' => $form_data
                        );
                    } else if (in_array($v->type, ['color-group'])) {
                        $values = $this->sanitize_values($v);

                        $values_data = array();
                        if (!is_array($values)) {
                            $values = array($values);
                        }
                        if ((isset($v->multiple) && $v->multiple)) {
                            $is_multiple = true;
                        } else {
                            $is_multiple = false;
                        }
                        if ($is_multiple) {
                            foreach ($values as $l => $val) {
                                $item = isset($v->values[$l]) ? $v->values[$l] : FALSE;
                                if ($item === FALSE) {
                                    $item = new stdClass;
                                    $item->label = 'Other';
                                    $val = str_replace('WCPAOTH ', '', $val);
                                }
                                $values_data[$l] = array(
                                    'i' => $l,
                                    'value' => $val,
                                    'label' => isset($item->label) ? $item->label : FALSE,
                                    'color' => isset($item->color) ? $item->color : FALSE);
                            }
                        } else {
                            foreach ($values as $l => $val) {
                                $item = FALSE;
                                foreach ($v->values as $j => $_v) {
                                    if ((string) $_v->value === $val) {//
                                        $item = $_v;
                                        break;
                                    }
                                }
                                if ($item === FALSE) {
                                    $item = new stdClass;
                                    $val = str_replace('WCPAOTH ', '', $val);
                                    $item->label = 'Other';
                                    $j = 'other';
                                }

                                $values_data[$j] = array(
                                    'i' => $j,
                                    'value' => $val,
                                    'label' => isset($item->label) ? $item->label : FALSE,
                                    'color' => isset($item->color) ? $item->color : FALSE
                                );
                            }
                        }

                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $values_data,
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id, $values_data),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id, $values_data),
                            'form_data' => $form_data
                        );
                    } else {

                        $this->submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $this->sanitize_values($v),
                            'is_fee' => (isset($v->use_as_fee) && $v->use_as_fee) ? true : false,
                            'price' => $this->element_price($v, $product_id),
                            'quanity_depend' => $this->is_quantity_dependent($v, $product_id),
                            'form_data' => $form_data
                        );
                    }
                }
            }
        }

        $this->price_depends_update($product_id);

        return $this->submited_data;
    }

    public function price_depends_update($product_id) {
        $counter = false;

        foreach ($this->submited_data as $k => $data) {

            if ($data['price'] === 'dependency' || (is_array($data['price']) && in_array('dependency', $data['price'], true))) {

                if (isset($data['form_data']->elementId)) {
                    $v = $this->find_element_by_id($data['form_data']->elementId);

                    $this->submited_data[$k]['price'] = $this->element_price($v, $product_id, $data['value'], $data['value'], false, true);
                    $this->submited_data[$k]['quanity_depend'] = $this->is_quantity_dependent($v, $product_id);
                }
            }
        }

        if ($counter) {
            $this->price_depends_update($product_id);
        }
    }

    public function is_quantity_dependent($v, $product_id, $values_data = false, $value = false) {
        if (!isset($v->enablePrice) || !$v->enablePrice) {
            return false;
        }

        if ($v->pricingType === 'custom') {
            $formula = $this->element_price($v, $product_id, $values_data, $value, true);

            if (is_array($formula)) {
                foreach ($formula as $f) {
                    if (strpos($f, '{quantity}') !== false) {
                        return $formula;
                    }
                }
            } else if (strpos($formula, '{quantity}') !== false) {
                return $formula;
            }
        }
        return false;
    }

    public function element_price($v, $product_id, $values_data = false, $value = false, $return_formula = false, $repeat = false) {
        if (!isset($v->enablePrice) || !$v->enablePrice) {
            return false;
        }
        if (!$this->product) {
            $this->set_product($product_id);
        }
        $product_price = $this->product->get_price('edit');
        if (!isset($v->price)) {
            $v->price = 0;
        }

        switch ($v->type) {
            case 'text':
            case 'date':
            case 'number':
            case 'color':
            case 'time':
            case 'datetime-local':
            case 'textarea':
                $value = (($value === false) ? trim($_REQUEST[$v->name]) : $value);
                return $this->calculate_price($v->pricingType, $value, $v->price, $product_price, $return_formula, $repeat);
            case 'paragraph':
            case 'header':
                $value = 1;
                return $this->calculate_price($v->pricingType, $value, $v->price, $product_price, $return_formula, $repeat);
            case 'file':

                return $this->calculate_price($v->pricingType, true, $v->price, $product_price, $return_formula, $repeat);
            case 'placeselector':
                $value = (($value === false) ? trim($_REQUEST[$v->name]) : $value);
                return array($this->calculate_price($v->pricingType, $value, $v->price, $product_price, $return_formula, $repeat));

            case 'select':
                if ($values_data === FALSE) {
                    $value = (($value === false) ? trim($_REQUEST[$v->name]) : $value);
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                } else {
                    $value = $values_data;
                }

                $elem_price = array();
                $price = 0;
                foreach ($value as $k => $val) {
                    if ($v->priceOptions === 'different_for_all') {
                        foreach ($v->values as $l => $e) {
                            if ($l === $k) {
                                $price = $e->price;
                                break;
                            }
                        }
//                            $price = $val->price;
                    } else if ($v->priceOptions === 'fixed_for_all') {
                        $price = $v->price;
                    }

                    $elem_price[$k] = $this->calculate_price($v->pricingType, (isset($val['value']) ? $val['value'] : $val), $price, $product_price, $return_formula, $repeat);
                }
                return $elem_price;

            case 'radio-group':
                if ($values_data === FALSE) {
                    $value = (($value === false) ? trim($_REQUEST[$v->name]) : $value);
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                } else {
                    $value = $values_data;
                }


                $price = 0;
                foreach ($value as $l => $val) {
                    if (isset($v->values[$l])) {
                        $price = $v->values[$l]->price;
                    }
                }
                if ($v->priceOptions == 'different_for_all') {
                    $price = $price;
                } else if ($v->priceOptions == 'fixed_for_all') {
                    $price = $v->price;
                }


                return array($l => $this->calculate_price($v->pricingType, (isset($val['value']) ? $val['value'] : $val), $price, $product_price, $return_formula, $repeat));

            case 'checkbox-group':
                $elem_price = false;
                $value = (($value === false) ? $_REQUEST[$v->name] : $value);
                if (is_array($value)) {
                    $elem_price = array();
                    foreach ($v->values as $k => $val) {
                        if ($v->priceOptions == 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions == 'fixed_for_all') {
                            $price = $v->price;
                        }

                        if (isset($value[$k])) {
                            $elem_price[$k] = $this->calculate_price($v->pricingType, $val->value, $price, $product_price, $return_formula, $repeat);
                        }
                    }
                    if ($v->priceOptions == 'fixed_for_all') { // other option has price if it is fixed for all
                        $price = $v->price;
                        if (isset($value['other'])) {
                            $elem_price['other'] = $this->calculate_price($v->pricingType, $value['other'], $price, $product_price, $return_formula, $repeat);
                        }
                    }
                }

                return $elem_price;
            case 'color-group':
                $elem_price = false;
                if ($values_data === FALSE) {
                    $value = (($value === false) ? ($_REQUEST[$v->name]) : $value);
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                } else {
                    $value = $values_data;
                }



                $elem_price = array();

                if (is_array($value)) {
                    $elem_price = array();
                    foreach ($v->values as $k => $val) {
                        if ($v->priceOptions == 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions == 'fixed_for_all') {
                            $price = $v->price;
                        }

                        if (isset($value[$k])) {
                            $elem_price[$k] = $this->calculate_price($v->pricingType, $val->value, $price, $product_price, $return_formula, $repeat);
                        }
                    }
                    if ($v->priceOptions == 'fixed_for_all') { // other option has price if it is fixed for all
                        $price = $v->price;
                        if (isset($value['other'])) {
                            $elem_price['other'] = $this->calculate_price($v->pricingType, $value['other']['value'], $price, $product_price, $return_formula, $repeat);
                        }
                    }
                }


                return $elem_price;
            case 'image-group':
                $elem_price = false;

                if ($values_data === FALSE) {
                    $value = (($value === false) ? ($_REQUEST[$v->name]) : $value);
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                } else {
                    $value = $values_data;
                }

                $elem_price = array();
                $price = 0;
                foreach ($value as $k => $val) {
                    if ($v->priceOptions == 'different_for_all') {
                        foreach ($v->values as $l => $e) {
                            if ($l === $k) {
                                $price = $e->price;
                                break;
                            }
                        }
//                            $price = $val->price;
                    } else if ($v->priceOptions == 'fixed_for_all') {
                        $price = $v->price;
                    }

                    $elem_price[$k] = $this->calculate_price($v->pricingType, 1, $price, $product_price, $return_formula, $repeat);
                }
                return $elem_price;
        }
    }

    public function calculate_price($pricingType, $value, $price, $product_price, $return_formula = false, $repeat = false) {
        if ($pricingType !== 'custom') {
            if (($dec_sep = wc_get_price_decimal_separator())) {
                $price = floatval(str_replace($dec_sep, '.', $price));
            }
            if (!is_numeric($price)) {
                return 0;
            }
        }

        $elem_price = 0;


        switch ($pricingType) {
            case 'per_car':
                if ($value) {
                    $elem_price = strlen($value) * $price;
                }
                break;
            case 'fixed':
                if ($value || $value == '0' || $value === 0) {
                    $elem_price = $price * 1;
                } else {
                    $elem_price = 0;
                }

                break;
            case 'multiply':
                if ($value) {
                    $elem_price = (is_numeric($value) ? $value : 1) * $price; //added + sign to convert to int/float value
                } else {
                    $elem_price = 0;
                }
                if ($elem_price < 0) {
                    $elem_price = 0; // not allow to set -ve, use custom formula for setting negative
                }

                break;
            case 'custom':

                $elem_price = $this->process_custom_formula($value, $price, $product_price, $return_formula, $repeat);
                if ($return_formula) {
                    return $elem_price;
                }

                break;
            case 'percentage':
                if ($value) {
                    $elem_price = ($price * $product_price) / 100;
                } else {
                    $elem_price = 0;
                }
                break;
        }


        return $elem_price;
    }

    public function process_custom_formula($value, $price, $product_price, $return_formula, $repeat = false) {
        if (!is_string($value) && isset($value['value'])) {
            $value = $value['value'];
        }
        $str_length = strlen($value);
        $formula = str_replace(['{value}', '{product_price}', '{quantity}', '{value.length}'], [$value, $product_price, 1, $str_length], $price);

        if (preg_match_all('/\{(\s)*?field\.([^}]*)}/', $formula, $matches)) {


            foreach ($matches[2] as $k => $match) {


                $ele = explode('.', $match);
                if (is_array($ele) && count($ele) > 1 && in_array($ele[1], ['value', 'price'])) {
                    $sub_data = $this->find_submited_data_by_id($ele[0]);

                    if ($sub_data === false) {
                        if ($repeat) {// if the itreation is repeating, and the elemet is not available, need to set as zero
                            $formula = str_replace($matches[0][$k], 0, $formula);
                        } else {

                            return 'dependency';
                        }
                    } else {
                        if ($ele[1] == 'price') {
                            if (isset($sub_data['form_data']->cl_status) && $sub_data['form_data']->cl_status === 'hidden') {
                                $formula = str_replace($matches[0][$k], 0, $formula);
                            } else if (!isset($sub_data['price']) || $sub_data['price'] === false) { // no pricing enabled , set elem_price as 0
                                $formula = str_replace($matches[0][$k], 0, $formula);
                            } else if (isset($sub_data['price']) && ($sub_data['price'] === 'dependency') || (is_array($sub_data['price']) && in_array('dependency', $sub_data['price'], true))) {
                                return 'dependency';
                            } else if (isset($sub_data['price']) && $sub_data['price'] !== false) {
                                if (is_array($sub_data['price'])) {
                                    $p = array_sum($sub_data['price']);
                                } else {
                                    $p = $sub_data['price'];
                                }

                                $formula = str_replace($matches[0][$k], $p, $formula);
                            }
                        } else if ($ele[1] == 'value') {
                            if (isset($sub_data['form_data']->cl_status) && $sub_data['form_data']->cl_status === 'hidden') {
                                $formula = str_replace($matches[0][$k], 0, $formula);
                            } else if (!isset($sub_data['value']) || $sub_data['value'] === false || $sub_data['value'] === null || $sub_data['value'] === '') { // no pricing enabled , set elem_price as 0
                                $formula = str_replace($matches[0][$k], 0, $formula);
                            } else if (isset($sub_data['value']) && ($sub_data['value'] === 'dependency') || (is_array($sub_data['value']) && in_array('dependency', $sub_data['value'], true))) {
                                return 'dependency';
                            } else if (isset($sub_data['value']) && $sub_data['value'] !== false) {
                                if (is_array($sub_data['value'])) {
                                    if (count($sub_data['value']) == 1) {
                                        $p_temp = array_values($sub_data['value'])[0];
                                        $p_temp = is_array($p_temp) ? $p_temp['value'] : $p_temp;
                                        if (is_numeric($p_temp)) {
                                            $p = (float) $p_temp;
                                        } else {
                                            $p = $p_temp;
                                        }
                                    } else {
                                        $p_sum = 0.0;
                                        $_i = -1;
                                        foreach ($sub_data['value'] as $_p) {
                                            $_i++;

                                            if (is_array($_p)) {
                                                if (is_numeric($_p['value'])) {
                                                    $p_sum += (float) $_p['value'];
                                                } else if ($_i == 0) {
                                                    $p_sum = $_p['value'];
                                                    break;
                                                }
                                            } else {
                                                if (is_numeric($_p)) {
                                                    $p_sum += (float) $_p;
                                                } else if ($_i == 0) {
                                                    $p_sum = $_p;
                                                    break;
                                                }
                                            }
                                        }
                                        $p = $p_sum;
                                    }
                                } else {
                                    $p = $sub_data['value'];
                                }
                                if (is_numeric($p)) {
                                    $formula = str_replace($matches[0][$k], $p, $formula);
                                } else {

                                    $formula = str_replace($matches[0][$k], "'" . $p . "'", $formula);
                                }
                            }
                        }

                        // break; commented out this break, not sure why it required, it causing error if there are multiple field reference in forumula
                    }
                } else {
                    $formula = str_replace($matches[0][$k], 0, $formula); //replace with zero if the matching string has issues to avoid eval error
                }
            }
        }


        try {

            $elem_price = eval('return ' . $formula . ';');
            if ($return_formula) { // this formula returns for calculate price when quanity updated
                return str_replace(['{value}', '{product_price}', '{value.length}'], [$value, $product_price, $str_length], $price);
            }
        } catch (Throwable $t) {
            if ($return_formula) {
                return false;
            }
            $elem_price = 0;
        }
        if ($elem_price == null || $elem_price == FALSE) {
            $elem_price = 0;
        }
        return $elem_price;
    }

    /*
      Sanitize and return user input data based on the type of field
     */

    public function sanitize_values($v) {
        if (!is_object($v)) {
            return sanitize_text_field($v);
        } else if ((isset($v->name))) {

            if (is_array($_REQUEST[$v->name])) {

                $_values = $_REQUEST[$v->name];
                array_walk($_values, function(&$a, $b) {

                    sanitize_text_field($a);
                }); // using this array_walk method to preserve the keys
                return $_values;
            } else if ($v->type == 'textarea') {
                return sanitize_textarea_field($_REQUEST[$v->name]);
            } else {
                return sanitize_text_field($_REQUEST[$v->name]);
            }
        }
    }

    public function upload_dir($upload) {

        $customer = new WCPA_Customer();
        $file_directory = $customer->upload_directory_base();

        $upload['subdir'] = '/' . $file_directory;
        $upload['path'] = $upload['basedir'] . '/' . $file_directory;
        $upload['url'] = $upload['baseurl'] . '/' . $file_directory;

        return $upload;
    }

    public function upload_dir_temp($upload) {

        $customer = new WCPA_Customer();
        $file_directory = $customer->upload_directory_base(true);
        $upload['subdir'] = '/' . $file_directory;
        $upload['path'] = $upload['basedir'] . '/' . $file_directory;
        $upload['url'] = $upload['baseurl'] . '/' . $file_directory;

        return $upload;
    }

    public function handle_upload_ajax($v) {
        if (!function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $uploadedfile = $_FILES['wcpa_file'];
        $uploadedfile_name = $_FILES['wcpa_file']["name"];
        $upload_overrides = array('test_form' => false);
        add_filter('upload_dir', array($this, 'upload_dir_temp'));
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        remove_filter('upload_dir', array($this, 'upload_dir_temp'));
        if ($movefile && !isset($movefile['error'])) {
            $file_name = explode('/' . WCPA_UPLOAD_DIR . '/', $movefile['file']);
            $movefile['file'] = $file_name[1];
            return ['status' => true, 'file' => array_merge($movefile, array('file_name' => $uploadedfile_name))];
        } else {
            /**
             * Error generated by _wp_handle_upload()
             * @see _wp_handle_upload() in wp-admin/includes/file.php
             */
            return ['status' => false, 'error' => $movefile['error']];

            //echo $movefile['error'];
        }
    }

    public function validate_file_with_config($exts, $file_name) {
        $supported_extions = false;
        if (!empty($exts)) {
            $supported_extions = preg_split("/[\s,]+/", $exts);
            if (is_array($supported_extions)) {
                $supported_extions = array_filter(array_map('trim', $supported_extions));
                if (count($supported_extions) == 0) {
                    $supported_extions = false;
                }
            } else {
                $supported_extions = false;
            }
        }
        if ($supported_extions !== false) {
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if (!in_array($ext, $supported_extions)) {
                return false;
            }
        }
        return true;
    }

    public function move_file($v, $file) {
        if (!function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }


        $uploadedfile_name = $file[1];

        $customer = new WCPA_Customer();
        $file_directory = $customer->upload_directory_base();
        $upload = wp_upload_dir();
        $uploadedfile = $upload['basedir'] . '/' . WCPA_UPLOAD_DIR . '/' . $file[0];


        $upload['subdir'] = '/' . $file_directory;
        $upload['path'] = $upload['basedir'] . '/' . $file_directory;
        $upload['url'] = $upload['baseurl'] . '/' . $file_directory;


        $wp_filetype = wp_check_filetype_and_ext($uploadedfile, $uploadedfile_name);
        $ext = empty($wp_filetype['ext']) ? '' : $wp_filetype['ext'];
        $type = empty($wp_filetype['type']) ? '' : $wp_filetype['type'];
        $proper_filename = empty($wp_filetype['proper_filename']) ? '' : $wp_filetype['proper_filename'];
        // Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
        if ($proper_filename) {
            $uploadedfile_name = $proper_filename;
        }
       
        if ((!$type || !$ext)) {
            $this->add_cart_error(sprintf(__('File %s could not be uploaded.'), $v->label));
            return FALSE;
        }

        $filename = wp_unique_filename($upload['path'], $uploadedfile_name);
        $new_file = $upload['path'] . "/$filename";
        if (!is_dir($upload['path'])) {
            wp_mkdir_p($upload['path']);
        }

        $move_new_file = @copy($uploadedfile, $new_file);

        if (false === $move_new_file) {
            $this->add_cart_error(sprintf(__('File %s could not be uploaded.'), $v->label));
            return FALSE;
        }
        @unlink($uploadedfile);

        $stat = stat(dirname($new_file));
        $perms = $stat['mode'] & 0000666;
        @ chmod($new_file, $perms);
        // Compute the URL.
        $url = $upload['url'] . "/$filename";

        return array(
            'file' => $new_file,
            'url' => $url,
            'type' => $type,
            'file_name' => $uploadedfile_name
        );
    }

    public function handle_upload($v) {

        if (!function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }


        $uploadedfile = $_FILES[$v->name];
        $uploadedfile_name = $_FILES[$v->name]["name"];

        $upload_overrides = array('test_form' => false);
        add_filter('upload_dir', array($this, 'upload_dir'));
        if (!is_uploaded_file($uploadedfile['tmp_name'])) {
            return false;
        }
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);


        remove_filter('upload_dir', array($this, 'upload_dir'));

        if ($movefile && !isset($movefile['error'])) {
            return array_merge($movefile, array('file_name' => $uploadedfile_name));
        } else {
            /**
             * Error generated by _wp_handle_upload()
             * @see _wp_handle_upload() in wp-admin/includes/file.php
             */
            $this->add_cart_error($movefile['error']);
            return FALSE;
            //echo $movefile['error'];
        }
    }

    private function default_value($v) {

        $default_value = '';
        switch ($v->type) {
            case 'text':
            case 'date':
            case 'number':
            case 'color':
            case 'textarea':
            case 'time':
            case 'datetime-local':
                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) { // using get if there is any value passed using url/get method
                    $default_value = $this->sanitize_values($v);
                } else if (isset($v->value)) {
                    $default_value = $v->value;
                }



                break;
            case 'file':
                $ajax_upload = ( isset($v->ajax_upload) && $v->ajax_upload) ? true : false;
                if ($ajax_upload) {
                    if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name . '_ajax'])) { // if there is a validation error, it has persist the user entered values,
                        $default_value = $this->sanitize_values($_POST[$v->name . '_ajax']);
                    }
                }
                break;
            case 'select':
                $default_value = array();
                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) {
                    $default_value = $this->sanitize_values($v);
                } else if ($v->values && !(isset($_REQUEST['add-to-cart']) && $this->cart_error)) { // if it is direct product page load, not add-to-cart has set
                    foreach ($v->values as $k => $val) {
                        if (isset($val->selected)) {
                            $default_value[$k] = $val->value;
                        }
                    }
                }

                break;
            case 'checkbox-group':
            case 'radio-group':

                $default_value = array();
                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) {
                    $default_value = $this->sanitize_values($v);
                } else if ($v->values && !(isset($_REQUEST['add-to-cart']) && $this->cart_error)) { // if it is direct product page load, not add-to-cart has set
                    foreach ($v->values as $k => $val) {
                        if (isset($val->selected)) {
                            $default_value[$k] = $val->selected;
                        }
                    }
                }

                break;
            case 'image-group':

                $default_value = array();
                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) {
                    $default_value = $this->sanitize_values($v);
                } else if ($v->values && !(isset($_REQUEST['add-to-cart']) && $this->cart_error)) { // if it is direct product page load, not add-to-cart has set
                    foreach ($v->values as $k => $val) {
                        if (isset($val->selected)) {
                            $default_value[$k] = $val->selected;
                        }
                    }
                }
                break;

            case 'color-group':
                $default_value = array();
                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) {
                    $default_value = $this->sanitize_values($v);
                } else if ($v->values && !(isset($_REQUEST['add-to-cart']) && $this->cart_error)) { // if it is direct product page load, not add-to-cart has set
                    foreach ($v->values as $k => $val) {
                        if (isset($val->selected)) {
                            $default_value[$k] = $val->selected;
                        }
                    }
                }
                break;
            case 'placeselector':
                $default_value = array(
                    'formated' => '',
                    'splited' => array(),
                    'cords' => array()
                );
                $splited = ['street_number', 'route', 'locality', 'administrative_area_level_1', 'postal_code', 'country'];

                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value['formated'] = $this->sanitize_values($v);
                    foreach ($splited as $fl_name) {
                        if (isset($_POST[$v->name . '_' . $fl_name])) {
                            $default_value['splited'][$fl_name] = $_POST[$v->name . '_' . $fl_name];
                        } else {
                            $default_value['splited'][$fl_name] = '';
                        }
                    }
                    if (isset($_POST[$v->name . '_lat'])) {
                        $default_value['cords']['lat'] = $_POST[$v->name . '_lat'];
                    } else {
                        $default_value['cords']['lat'] = '';
                    }
                    if (isset($_POST[$v->name . '_lng'])) {
                        $default_value['cords']['lng'] = $_POST[$v->name . '_lng'];
                    } else {
                        $default_value['cords']['lng'] = '';
                    }
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) {
                    $default_value['formated'] = $this->sanitize_values($v);
                    foreach ($splited as $fl_name) {
                        if (isset($_POST[$v->name . '_' . $fl_name])) {
                            $default_value['splited'][$fl_name] = $_POST[$v->name . '_' . $fl_name];
                        } else {
                            $default_value['splited'][$fl_name] = '';
                        }
                    }
                    if (isset($_POST[$v->name . '_lat'])) {
                        $default_value['cords']['lat'] = $_POST[$v->name . '_lat'];
                    } else {
                        $default_value['cords']['lat'] = '';
                    }
                    if (isset($_POST[$v->name . '_lng'])) {
                        $default_value['cords']['lng'] = $_POST[$v->name . '_lng'];
                    } else {
                        $default_value['cords']['lng'] = '';
                    }
                } else {
                    $default_value['formated'] = isset($v->value) ? $v->value : '';
                    foreach ($splited as $fl_name) {
                        $default_value['splited'][$fl_name] = '';
                    }
                    $default_value['cords']['lat'] = '';

                    $default_value['cords']['lng'] = '';
                }

                break;
        }
        return $default_value;
    }

    public function get_relation_data($v, $val = false) {
        $related = '';
        $rules = '';
        $price_related = '';

        if (isset($v->elementId) && isset($this->relations[$v->elementId])) {
            $related = 'data-related=\'' . json_encode($this->relations[$v->elementId]) . '\'';
        }
        if (isset($v->elementId) && isset($this->price_depends[$v->elementId])) {
            $price_related = 'data-price_depends=\'' . json_encode($this->price_depends[$v->elementId]) . '\'';
        }
        if (isset($v->elementId) && isset($v->enableCl) && $v->enableCl) {

            $rules = 'data-rules=\'' . json_encode(['rules' => $v->relations, 'action' => $v->cl_rule]) . '\'';
        }



        return ['related' => $related, 'rules' => $rules, 'price_related' => $price_related];
    }

    public function get_price_data($v, $val = false) {
        $data = false;
        //per character => per_car
        //percentage of total amount => percentage
        // multiply by number  => multiply

        if (isset($v->enablePrice) && $v->enablePrice && (isset($v->price) || isset($val->price))) {

            if ($this->conversion_unit === false) {
                $view_price = $this->product->get_regular_price('view');
                $edit_price = $this->product->get_regular_price('edit');

                if ($view_price && $edit_price) {
                    $this->conversion_unit = $view_price / $edit_price;
                } else {
                    $this->conversion_unit = 1;
                }
            }

            if ($this->tax_per_unit === false) {
                $this->tax_per_unit = wcpa_get_price_shop($this->product, array(
                    'qty' => 1,
                    'price' => 1,
                ));
            }
            $is_fee = false;
            if (isset($v->form_rules['pric_use_as_fee']) && $v->form_rules['pric_use_as_fee'] === true) {
                $is_fee = true;
            } else {
                $is_fee = (isset($v->use_as_fee) && $v->use_as_fee) ? true : false;
            }

            if (in_array($v->type, array('select', 'checkbox-group', 'radio-group', 'image-group', 'color-group'))) {
                if ($v->priceOptions == 'different_for_all') {
                    $price = $val->price;
                } else if ($v->priceOptions == 'fixed_for_all') {
                    $price = $v->price;
                }
                if ($v->pricingType !== 'percentage' && $v->pricingType !== 'custom') {
                    $price = wcpa_get_price_shop($this->product, array(
                        'qty' => 1,
                        'price' => $price,
                    ));

//                    $price = $this->conversion_unit * $price;
                    $price = apply_filters('raw_woocommerce_price', $price);
                }

                $data = 'data-price=\'' . json_encode(array('priceoptions' => $v->priceOptions,
                            'type' => $v->type,
                            'value' => $price,
                            'pricingType' => $v->pricingType,
                            'taxpu' => $this->tax_per_unit,
                            'con_unit' => $this->conversion_unit,
                            'is_fee' => $is_fee)) . '\'';
            } else {
                $price = $v->price;

                if ($v->pricingType !== 'percentage' && $v->pricingType !== 'custom') {// in such cases $price will be string or percentage value, not be actual price
                    $price = wcpa_get_price_shop($this->product, array(
                        'qty' => 1,
                        'price' => $price,
                    ));
//                    $price = $this->conversion_unit * $price;
                    $price = apply_filters('raw_woocommerce_price', $price);
                }

                $data = 'data-price=\'' . json_encode(array(
                            'value' => $price, 'type' => $v->type,
                            'pricingType' => $v->pricingType,
                            'taxpu' => $this->tax_per_unit,
                            'is_fee' => $is_fee,
                            'con_unit' => $this->conversion_unit)) . '\'';
            }
        }
        return $data;
    }

    public function render($after_acb = false) {

        $qnty_related = '';
        $attr_related = '';

        $product_data = array();




        if (isset($this->relations['quantity'])) {
            $qnty_related = 'data-qntyrelated=\'' . json_encode($this->relations['quantity']) . '\'';
        }
        if (isset($this->relations['attribute'])) {
            $attr_related = 'data-attrrelated=\'' . json_encode($this->relations['attribute']) . '\'';
        }



        if (!$this->product) {
            global $product;
            $this->product = $product;
        }

        $has_price = false;
        if (count($this->data) === 0) {
            return;
        }

        $product_data['wc_product_price'] = apply_filters('raw_woocommerce_price', wcpa_get_price_shop($this->product));
        $product_data['product_title'] = '';
        $product_data['is_variable'] = $this->product->is_type('variable') ? true : FALSE;

        $rules = [
            'pric_overide_base_price' => isset($this->settings['pric_overide_base_price']) ? $this->settings['pric_overide_base_price'] : false,
            'pric_overide_base_price_if_gt_zero' => isset($this->settings['pric_overide_base_price_if_gt_zero']) ? $this->settings['pric_overide_base_price_if_gt_zero'] : false,
            'pric_cal_option_once' => isset($this->settings['pric_cal_option_once']) ? $this->settings['pric_cal_option_once'] : false,
            'pric_use_as_fee' => isset($this->settings['pric_use_as_fee']) ? $this->settings['pric_use_as_fee'] : false,
        ];

        echo '<div class="wcpa_form_outer" ' . $qnty_related . ' ' . $attr_related . ' data-product=\'' . wp_json_encode($product_data) . '\' data-rules=\'' . json_encode($rules) . '\'>';
        $row_open = false;
        $col_counter = 6;
        $col = 6;
        echo '<input type="hidden" name="wcpa_field_key_checker" value="wcpa" />';
        foreach ($this->data as $v) {


            if (isset($v->col)) {
                $col = (int) $v->col;
            }
            $col_counter += $col;
            if ($col_counter > 6) {
                if ($row_open) {
                    echo '</div>';
                    echo '<div class="wcpa_row">';
                } else {
                    echo '<div class="wcpa_row">';
                    $row_open = true;
                }
                $col_counter = $col;
            }

            $parent_class = 'wcpa_form_item wcpa_type_' . $v->type . ' ';
            if (isset($v->className)) {
                $parent_class .= ' ' . $v->className . '_parent ';
            }
            if ($col < 6) {
                $parent_class .= 'wcpa-col-' . $col . ' ';
            }

            $inline_css = '';
            $relation_action = '';
            if (isset($v->enableCl) && $v->enableCl) {
                if ($v->cl_rule == 'show') {
                    $inline_css = 'display:none;';
                }
            }
            if ($v->type == 'hidden') {
                $inline_css = 'display:none;';
            }
            if (!empty($inline_css)) {
                $inline_css = 'style="' . $inline_css . '"';
            }
            $id_text = '';
            if (isset($v->elementId) && !empty($v->elementId)) {
                $id_text .= 'id="' . $v->elementId . '"';
            }
            $relation_data = $this->get_relation_data($v);

            if (!empty($relation_data['related'])) {
                $parent_class .= 'wcpa_has_relation ';
            }
            if (!empty($relation_data['rules'])) {
                $parent_class .= 'wcpa_has_rules ';
            }
            if (isset($relation_data['price_related']) && !empty($relation_data['price_related'])) {
                $parent_class .= 'wcpa_has_price_related ';
            }
            echo '<div class="' . $parent_class . '" ' . $inline_css . ' ' . $relation_data['related'] . ''
            . ' ' . $relation_data['rules'] . '  ' . $relation_data['price_related'] . '   ' . $id_text . ' data-type="' . $v->type . '" >';

            switch ($v->type) {
                case 'text':
                    $this->render_text($v);
                    break;
                case 'hidden':
                    $this->render_hidden($v);
                    break;
                case 'color':
                    $this->render_color($v);
                    $this->enqueue_colorpicker();
                    break;
                case 'number':
                    $this->render_number($v);
                    break;
                case 'date':
                    $this->render_date($v);

                    $this->enqueue_detetimepicker();
                    break;
                case 'time':
                    $this->render_time($v);
                    $this->enqueue_detetimepicker();
                    break;
                case 'datetime-local':
                    $this->render_datetime($v);
                    $this->enqueue_detetimepicker();
                    break;
                case 'checkbox-group':
                    $this->render_checkbox($v);

                    break;
                case 'radio-group':
                    $this->render_radio($v);
                    break;
                case 'header':
                    $this->render_header($v);
                    break;
                case 'file':
                    $this->render_file($v);
                    break;
                case 'paragraph':
                    $this->render_paragraph($v);
                    break;
                case 'separator':
                    $this->render_separator($v);
                    break;
                case 'select':
                    $this->render_select($v);
                    break;
                case 'textarea':
                    $this->render_textarea($v);
                    break;
                case 'image-group':
                    $this->render_image($v);
                    break;
                case 'color-group':
                    $this->render_colorgroup($v);
                    break;
                case 'placeselector':
                    $this->render_place($v);
                    $this->enqueue_googlemap();
                    break;
            }
            //$this->{'render_' . $v->type}($v);
            if (isset($v->enablePrice) && $v->enablePrice) {
                $has_price = true;
                $visible = ($this->settings['disp_show_field_price'] ? '' : 'style="display:none"');
                echo '<span class="wcpa_priceouter" ' . $visible . ' >' . wcpa_price(0) . '</span>';
            }
            echo '</div>';
        }
        if ($row_open) {
            echo '</div>';
            $row_open = false;
        }

        if ($this->settings['enable_recaptcha'] && $this->settings['enable_recaptcha'] === true) {
            echo '<div class="wcpa_row">';
            echo '<div class="g-recaptcha" data-sitekey="' . wcpa_get_option('recaptcha_site_key', '') . '"></div>';
            echo '</div>';
            $this->enqueue_recaptcha();
        }


        echo '</div>';
        if ($has_price) {
            $this->price_summary_box();
        }
    }

    public function price_summary_box() {
        if (!$this->product) {
            global $product;
            $this->product = $product;
        }

        echo '<div class="wcpa_price_summary">
            <ul>';
        if ($this->settings['disp_summ_show_option_price'] && $this->settings['pric_overide_base_price'] === false && $this->settings['pric_overide_base_price_if_gt_zero'] === false) {
            echo'<li class="wcpa_options_total">
                <span>' . $this->settings['options_total_label'] . ':'
            . ' </span><span class="wcpa_price_outer ">' . wcpa_price(0) . '</span></li>';
        }
        if ($this->settings['disp_summ_show_product_price'] && $this->settings['pric_overide_base_price'] === false && $this->settings['pric_overide_base_price_if_gt_zero'] === false) {

            echo' <li class="wcpa_product_total">
                <span>' . $this->settings['options_product_label'] . ':'
            . ' </span><span class="wcpa_price_outer ">' . wcpa_price(wcpa_get_price_shop($this->product)) . '</span></li>';
        }
        if ($this->settings['disp_summ_show_total_price']) {
            echo' <li class="wcpa_total">
                <span>' . $this->settings['total_label'] . ': </span><span class="wcpa_price_outer ">' . wcpa_price(0) . '</span></li>';
        }
        echo' </ul>
        </div>';
    }

    public function label_section($v) {
        $name = (isset($v->name)) ? $v->name : '';
        $label = '';
        if (isset($v->label) && $v->label) {

            echo '<label for="' . $name . '">';
            $label .= $v->label;

            if (isset($v->required) && $v->required) {
                $label .= '<span class="required_ast">*</span>';
            }
            if (isset($v->desc_type) && $v->desc_type) {
                $label .= '<span class="wcpa_tooltip_icon" >?';
                if (isset($v->description)) {
                    $label .= '<span class="wcpa_tooltip">' . $v->description;
                    $label .= '</span>';
                }
                $label .= '</span>';
            }

            $label = apply_filters('wcpa_field_label', $label, $v);
            echo $label;
            echo '</label>';
        }
        if ((!isset($v->desc_type) || !$v->desc_type) && isset($v->description)) {
            echo '<span class="wcpa_helptext">' . $v->description;
            echo '</span>';
        }
    }

    public function render_file($v) {
        $data = '';
        $maxlength = '';
        $txt_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? $v->placeholder : '';
        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }


        $name = (isset($v->name)) ? $v->name : '';
        $ajax_upload_data = '';
        $ajax_upload = ( isset($v->ajax_upload) && $v->ajax_upload) ? true : false;
        $custom_style = ( isset($v->custom_style) && $v->custom_style) ? true : false;
        if ($ajax_upload) {
            $className .= 'wcpa_ajax_upload wcpa_file_custom ';
            $ajax_upload_data = 'data-details=\'' . json_encode(['element_id' => $v->elementId, 'name' => $name, 'product_id' => $this->product->get_id(), 'required' => (isset($v->required)) ? true : false]) . '\'';
        } else if ($custom_style) {
            $className .= 'wcpa_file_custom ';
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }
//        if ($v->label) {
//            echo '<label for="' . $name . '">' . $v->label;
//            if (isset($v->required) && $v->required) {
//                echo '<span class="required_ast">*</span>';
//            }
//            echo '</label>';
//        }
        $this->label_section($v);
        if (isset($v->maxlength)) {
            $maxlength = 'maxlength="' . $v->maxlength . '"';
        }


        if ($ajax_upload) {
            $default_value = $this->default_value($v);
            if (!empty($default_value)) {
                $txt_required = '';
                $temp = explode('||', $default_value);
                $placeholder = $temp[1];
            }
            echo '<label class="wcpa_file_outer" for="' . $name . '"><span class="wcpa_file_name">' . $placeholder . '</span><span class="wcpa_file_wrap" >' . __('Choose File') . '<input type="file"  id="' . $name . '"  ' . $className . ' name="' . $name . '"  ' . $maxlength . ' ' . $txt_required . ' ' . $price_data . '  ' . $ajax_upload_data . ' /></span></label>';
            echo '<input type="hidden" class="wcpa_file_hidden"  name="' . $name . '_ajax"  value="' . $default_value . '" />';
            echo '<div class="wcpa_progress" style="' . (empty($default_value) ? 'width:0;' : 'width:100%;display:block;') . '" ></div>';
            echo '<div class="wcpa_upload_status"></div>';
        } else if ($custom_style) {
            echo '<label class="wcpa_file_outer" for="' . $name . '"><span class="wcpa_file_name">' . $placeholder . '</span><span class="wcpa_file_wrap" >' . __('Choose File') . '<input type="file"  id="' . $name . '"  ' . $className . ' name="' . $name . '"  ' . $maxlength . ' ' . $txt_required . ' ' . $price_data . ' /></span></label>';
        } else {
            echo '<input type="file"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '"  ' . $maxlength . ' ' . $txt_required . ' ' . $price_data . '  ' . $ajax_upload_data . ' />';
        }
    }

    public function render_text($v) {
        $data = '';
        $maxlength = '';
        $txt_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';
        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';


        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }


        $name = (isset($v->name)) ? $v->name : '';
        $this->label_section($v);


        if (isset($v->maxlength)) {
            $maxlength = 'maxlength="' . $v->maxlength . '"';
        }
        echo '<input type="' . $v->subtype . '"'
        . '  id="' . $name . '" '
        . '' . $placeholder . ''
        . ' ' . $className . ''
        . ' name="' . $name . '"'
        . ' value="' . $this->default_value($v) . '" '
        . '' . $maxlength . ' '
        . '' . $txt_required . ''
        . ' ' . $price_data . '  />';
    }

    public function render_color($v) {
        $data = '';
        $maxlength = '';
        $txt_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';
        $className = 'wcpa_colorpicker ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $name = (isset($v->name)) ? $v->name : '';
        $this->label_section($v);

        if (isset($v->maxlength)) {
            $maxlength = 'maxlength="' . $v->maxlength . '"';
        }
        echo '<input type="color"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $maxlength . ' ' . $txt_required . ' ' . $price_data . '   />';
    }

    public function render_date($v) {
        $required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';


        $name = (isset($v->name)) ? $v->name : '';
        $className = 'wcpa_datepicker ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        $config_data = '';
        if (isset($v->date_pic_conf) && $v->date_pic_conf) {
            $config_data = 'data-dpconf=\'' . $v->date_pic_conf . '\'';
        }
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $this->label_section($v);

        echo '<input autocomplete="off" type="text" ' . $price_data . ' id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $required . ' ' . $config_data . ' />';
    }

    public function render_time($v) {
        $required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';


        $name = (isset($v->name)) ? $v->name : '';
        $className = 'wcpa_timepicker ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        $config_data = '';
        if (isset($v->date_pic_conf) && $v->date_pic_conf) {
            $config_data = 'data-dpconf=\'' . $v->date_pic_conf . '\'';
        }
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $this->label_section($v);

        echo '<input autocomplete="off" type="text" ' . $price_data . ' id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $required . ' ' . $config_data . ' />';
    }

    public function render_datetime($v) {
        $required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';


        $name = (isset($v->name)) ? $v->name : '';
        $className = 'wcpa_datetimepicker ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        $config_data = '';
        if (isset($v->date_pic_conf) && $v->date_pic_conf) {
            $config_data = 'data-dpconf=\'' . $v->date_pic_conf . '\'';
        }
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $this->label_section($v);

        echo '<input autocomplete="off" type="text" ' . $price_data . ' id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $required . ' ' . $config_data . ' />';
    }

    public function render_number($v) {
        $num_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $max = (isset($v->max)) ? 'max="' . $v->max . '"' : '';
        $min = (isset($v->min)) ? 'min="' . $v->min . '"' : '';
        $name = (isset($v->name)) ? $v->name : '';
        $step = (isset($v->step)) ? 'step="' . $v->step . '"' : 'step="any"';

        $this->label_section($v);

        echo '<input type="' . $v->type . '"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $max . ' ' . $min . ' ' . $step . ' ' . $num_required . ' ' . $price_data . ' />';
    }

    public function render_textarea($v) {
        $maxlength = '';
        $maxlength = (isset($v->maxlength)) ? 'maxlength="' . $v->maxlength . '"' : '';
        $title = (isset($v->title)) ? 'title="' . $v->title . '"' : '';
        $txtarea_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';

        $name = (isset($v->name)) ? $v->name : '';
        $rows = (isset($v->rows)) ? 'rows="' . $v->rows . '"' : '';

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }


        $this->label_section($v);

        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }

        echo '<textarea id="' . $name . '"  ' . $rows . ' ' . $placeholder . ' ' . $className . ' name="' . $name . '" ' . $maxlength . ' ' . $title . ' ' . $txtarea_required . ' ' . $price_data . ' >' . $this->default_value($v) . '</textarea>';
    }

    public function render_place($v) {

        $required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';

        $name = (isset($v->name)) ? $v->name : '';

        $className = 'wcpa_google_place ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $this->label_section($v);

        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
            $price_data = $this->get_price_data($v);
        }
        $default_value = $this->default_value($v);
        echo '<input type="text" id="' . $name . '"   ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $default_value['formated'] . '" ' . $required . ' ' . $price_data . ' >';
        if (isset($v->showaddfields) && $v->showaddfields) {
            echo '<div class="wcpa_place_compons"><table>
            <tr>
              <td class="label">' . __('Street address', 'wcpa-text-domain') . '</td>
              <td class="slimField"><input type="text" class="street_number" name="' . $name . '_street_number" value="' . $default_value['splited']['street_number'] . '"  disabled="true"></td>
              <td class="wideField" colspan="2"><input type="text"  class="route" name="' . $name . '_route" value="' . $default_value['splited']['route'] . '" disabled="true"></td>
            </tr>
            <tr>
              <td class="label">' . __('City', 'wcpa-text-domain') . '</td>
              <td  colspan="3"><input type="text"  class="locality" name="' . $name . '_locality" value="' . $default_value['splited']['locality'] . '" disabled="true"></td>
            </tr>
            <tr>
              <td class="label">' . __('State', 'wcpa-text-domain') . '</td>
              <td class="slimField"><input type="text"    class="administrative_area_level_1" name="' . $name . '_administrative_area_level_1" value="' . $default_value['splited']['administrative_area_level_1'] . '" disabled="true"></td>
              <td class="label postal_code">' . __('Zip code', 'wcpa-text-domain') . '</td>
              <td class="wideField"><input  type="text"  class="postal_code"  name="' . $name . '_postal_code" value="' . $default_value['splited']['postal_code'] . '"   disabled="true"></td>
            </tr>
            <tr>
              <td class="label">' . __('Country', 'wcpa-text-domain') . '</td>
              <td  colspan="3"><input  type="text"  class="country" name="' . $name . '_country" value="' . $default_value['splited']['country'] . '" disabled="true"></td>
            </tr>
          </table></div>';
        }
        $cord_display = 'style="display:none"';
        if (isset($v->showmap) && $v->showmap) {
            $cord_display = '';
        }
        echo '<div class="wcpa_cords wcpa_place_compons" ' . $cord_display . '><table> <tr>
        <td class="label">' . __('Latitude:', 'wcpa-text-domain') . '</td>
        <td ><input type="text" class="wcpa_lat" name="' . $name . '_lat"  value="' . $default_value['cords']['lat'] . '" readonly="readonly"></td>
         <td class="label">' . __('Longitude:', 'wcpa-text-domain') . '</td>
        <td ><input  type="text" class="wcpa_lng"  name="' . $name . '_lng"  value="' . $default_value['cords']['lng'] . '"   readonly="readonly"></td>
      </tr></table></div>';
        if (isset($v->showmap) && $v->showmap) {
            $map_height = '';
            if (isset($v->map_height) && $v->map_height > 10) {
                $map_height = 'style="height:' . $v->map_height . 'px"';
            }
            echo '<div class="wcpa_map" ' . $map_height . '></div>';
        }
    }

    public function render_checkbox($v) {
        $name = (isset($v->name)) ? $v->name : '';
        $this->label_section($v);
        $chk_required = (isset($v->required)) ? 'required="required"' : '';


        $className = 'checkbox-group ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        if (isset($v->inline) && $v->inline) {
            $className .= 'checkbox-inline ';
        }
        $price_data = '';
        if (isset($v->required) && $v->required) {
            $className .= 'wcpa_required ';
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $default_value = $this->default_value($v);

        if (!wcpa_empty($default_value) && $default_value !== false) {
            $chk_required = '';
        }

        if ($v->values && !empty($v->values)) {
            echo '<div ' . $className . '>';
            foreach ($v->values as $k => $val) {


                $is_selected = (isset($default_value[$k]) && $default_value[$k] !== FALSE) ? true : false;

                $chkd = ($is_selected) ? 'checked="checked"' : '';
                $price = '';
                // if ($v->enablePrice && $v->priceOptions == 'different_for_all') {
                //     $price = ' ( ' . wcpa_price($val->price, true) . ')';
                // }
                $option_class = '';
                if (isset($v->enablePrice) && $v->enablePrice) {
                    $option_class .= 'wcpa_has_price ';
                }
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $label = $val->label;
                if (isset($v->enablePrice) && $v->enablePrice) {
                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        if ($v->priceOptions == 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions == 'fixed_for_all') {
                            $price = $v->price;
                        }
                        $price = wcpa_get_price_shop($this->product, array(
                            'qty' => 1,
                            'price' => $price
                        ));
                        $label = $val->label . ' (' . wcpa_price($price, 1) . ')';
                    }

                    $price_data = $this->get_price_data($v, $val);
                }

                echo '<div class="wcpa_checkbox">
                    <input name="' . $name . '[' . $k . ']" ' . $option_class . ' id="' . $name . '_' . $k . '" value="' . $val->value . '" type="checkbox" ' . $chkd . ' ' . $price_data . ' ' . $chk_required . ' " >
                            
<label for="' . $name . '_' . $k . '"><span class="wcpa_check"></span>' . $label . '</label>';

                if (isset($v->options_tooltip) && $v->options_tooltip && isset($val->tooltip) && $val->tooltip != '') {
                    echo'<span class="wcpa_tooltip_icon" >?';
                    echo '<span class="wcpa_tooltip">' . $val->tooltip . '</span>';
                    echo '</span>';
                }
                echo'</div>';
                $chk_required = '';
            }
            if (isset($v->other) && $v->other) {
                $option_class = '';
                $price_data = false;
                $label = __('Other', 'wcpa-text-domain');
                if (isset($v->enablePrice) && $v->enablePrice && $v->priceOptions == 'fixed_for_all') {

                    $price = $v->price;
                    $option_class .= 'wcpa_has_price ';
                    $price = wcpa_get_price_shop($this->product, array(
                        'qty' => 1,
                        'price' => $price
                    ));
                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        $label = __('Other', 'wcpa-text-domain') . '(' . wcpa_price($price, 1) . ')';
                    }
                    $price_data = $this->get_price_data($v);
                }
                $checked = '';
                $other_val = '';
                $is_selected = isset($default_value['other']) ? true : false;

                if ($is_selected) {
                    $checked = 'checked="checked"';
                    $other_val = str_replace('WCPAOTH ', '', $default_value['other']);
                }

                //$option_class key used for adding .wcpa_has_price class
                $option_class .= 'wcpa_other';
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                echo '<div class="wcpa_checkbox">
                    <input name="' . $name . '[other]" ' . $option_class . '" '
                . 'id="' . $name . '_other" value="' . (($other_val == '') ? '' : 'WCPAOTH ' . $other_val) . '" type="checkbox"  ' . $price_data . ' ' . $checked . '>
                    <label for="' . $name . '_other"><span class="wcpa_check"></span>' . $label . '</label>
                        <input type="text" name="' . $name . '_other_val" class="wcpa_other_val" value="' . $other_val . '" >
                    </div>';
            }
            echo '</div>';
        }
    }

    public function render_image($v) {
        $name = (isset($v->name)) ? $v->name : '';
        $this->label_section($v);
        if ((isset($v->multiple) && $v->multiple)) {
            $is_multiple = true;
        } else {
            $is_multiple = false;
        }

        $className = 'image-group ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        if (isset($v->inline) && $v->inline) {
            $className .= 'image-inline ';
        }
        $check_type = 'radio';
        if ($is_multiple) {
            $className .= 'wcpa_multiselect ';
            $check_type = 'checkbox';
        }
        if (isset($v->required) && $v->required) {
            $className .= 'wcpa_required ';
        }
        if (isset($v->img_selection_type) && $v->img_selection_type) {
            $className .= 'wcpa_sel_type_' . $v->img_selection_type . ' ';
        } else {
            $className .= 'wcpa_sel_type_outline ';
        }

        if (isset($v->disp_type) && $v->disp_type) {
            $className .= 'wcpa_disp_type_' . $v->disp_type . ' ';
        } else {
            $className .= 'wcpa_disp_type_default ';
        }
        if (isset($v->img_preview) && $v->img_preview) {
            $className .= 'wcpa_img_preview ';
        }


        $chk_required = (isset($v->required)) ? 'required="required"' : '';

        $price_data = '';

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }


        $default_value = $this->default_value($v);

        if (!wcpa_empty($default_value) && $default_value !== false) {
            $chk_required = '';
        }

        $is_field_checked = FALSE;
        if ($v->values && !empty($v->values)) {
            echo '<div ' . $className . '>';
            foreach ($v->values as $k => $val) {

                if (is_array($default_value)) {
                    $is_selected = (isset($default_value[$k]) && $default_value[$k] !== FALSE) ? true : false;
                } else {
                    $is_selected = ($default_value === (string) $k) ? true : false;
                }

                if (!$is_field_checked && $is_selected) {
                    $is_field_checked = true;
                }
                $checked = ($is_selected !== FALSE) ? 'checked="checked"' : '';
                $price = '';

                $option_class = '';
                if (isset($v->enablePrice) && $v->enablePrice) {
                    $option_class .= 'wcpa_has_price ';
                }
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $label = $val->label;
                if (isset($v->enablePrice) && $v->enablePrice) {

                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        if ($v->priceOptions === 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions === 'fixed_for_all') {
                            $price = $v->price;
                        }
                        $price = wcpa_get_price_shop($this->product, array(
                            'qty' => 1,
                            'price' => $price
                        ));
                        $label = $val->label . ' (' . wcpa_price($price, 1) . ')';
                    }
                    $price_data = $this->get_price_data($v, $val);
                }
                $field_name = (($check_type === 'radio') ? $name : $name . '[' . $k . ']');
                $img_size_style = ((isset($v->disp_size_img) && $v->disp_size_img > 0) ? 'style="width:' . $v->disp_size_img . 'px"' : '');
                echo '<div class="wcpa_image" >
                    <input type="' . $check_type . '" id="' . $name . '_' . $k . '" ' . $checked . ' name="' . $field_name . '" value="' . $k . '" ' . $option_class . '  ' . $price_data . ' ' . $chk_required . ' >
                   
                    <img src="' . $val->image . '" alt="' . $val->label . '" ' . $img_size_style . '  for="' . $name . '_' . $k . '"  />
                    <label for="' . $name . '_' . $k . '"  >' . $label . '</label></div>';

                $chk_required = '';
            }
            if (isset($v->other) && $v->other) {
                $option_class = '';
                $label = __('Other', 'wcpa-text-domain');
                if (isset($v->enablePrice) && $v->enablePrice && $v->priceOptions == 'fixed_for_all') {
                    $price = $v->price;
                    $price = wcpa_get_price_shop($this->product, array(
                        'qty' => 1,
                        'price' => $price
                    ));
                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        $label = __('Other', 'wcpa-text-domain') . '(' . wcpa_price($price, 1) . ')';
                    }

                    $price_data = $this->get_price_data($v);
                    $option_class .= 'wcpa_has_price ';
                } else {
                    $price_data = '';
                }
                $checked = '';
                $other_val = '';

                if (!wcpa_empty($default_value) && !$is_field_checked) {
                    $is_selected = true;
                } else {
                    $is_selected = false;
                }

                // $is_selected = isset($default_value['other']) ? $default_value['other'] : false;

                if ($is_selected === true && !wcpa_empty($default_value)) {
                    $checked = 'checked="checked"';
                    if (is_array($default_value)) {
                        $other_val = str_replace('WCPAOTH ', '', $default_value['other']);
                    } else {
                        $other_val = str_replace('WCPAOTH ', '', $default_value);
                    }
                }

                //$option_class key used for adding .wcpa_has_price class
                $option_class .= 'wcpa_other';
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $field_name = (($check_type == 'radio') ? $name : $name . '[other]');
                echo '<div class="wcpa_image_other">
                    <div class="wcpa_' . $check_type . '">
                    <input name="' . $field_name . '" ' . $option_class . '" '
                . 'id="' . $name . '_other" value="' . (($other_val === '') ? '' : 'WCPAOTH ' . $other_val) . '"  type="' . $check_type . '"  ' . $price_data . ' ' . $checked . '>
                 <label for="' . $name . '_other"><span class="wcpa_check"></span>' . $label . '</label>';

                echo '<input type="text" name="' . $name . '_other_val" class="wcpa_other_val" value="' . $other_val . '" >
                   </div> </div>';
            }
            echo '</div>';
        }
    }

    public function render_colorgroup($v) {
        $name = (isset($v->name)) ? $v->name : '';
        $this->label_section($v);
        if ((isset($v->multiple) && $v->multiple)) {
            $is_multiple = true;
        } else {
            $is_multiple = false;
        }

        $className = 'color-group ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        if (isset($v->inline) && $v->inline) {
            $className .= 'color-inline ';
        }
        $check_type = 'radio';
        if ($is_multiple) {
            $className .= 'wcpa_multiselect ';
            $check_type = 'checkbox';
        }
        if (isset($v->required) && $v->required) {
            $className .= 'wcpa_required ';
        }
        $chk_required = (isset($v->required)) ? 'required="required"' : '';

        if (isset($v->selection_type) && $v->selection_type) {
            $className .= 'wcpa_sel_type_' . $v->selection_type . ' ';
        }
        $price_data = '';

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $default_value = $this->default_value($v);

        if (!wcpa_empty($default_value) && $default_value !== false) {
            $chk_required = '';
        }
        $is_field_checked = FALSE;
        if ($v->values && !empty($v->values)) {
            echo '<div ' . $className . '>';
            $is_lable_empty = true;

            foreach ($v->values as $k => $val) {
                if ($val->label !== '') {
                    $is_lable_empty = false;
                    break;
                }
            }

            foreach ($v->values as $k => $val) {

                if (is_array($default_value)) {
                    $is_selected = (isset($default_value[$k]) && $default_value[$k] !== FALSE) ? true : false;
                } else {
//                    $is_selected = ($default_value === (string) $k) ? true : false;
                    $is_selected = ($default_value === $this->sanitize_values($val->value)) ? true : false;
                }

                if (!$is_field_checked && $is_selected) {
                    $is_field_checked = true;
                }
                $checked = ($is_selected !== FALSE) ? 'checked="checked"' : '';
                $price = '';

                $option_class = '';
                if (isset($v->enablePrice) && $v->enablePrice) {
                    $option_class .= 'wcpa_has_price ';
                }

                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $label = $val->label;
                if (isset($v->enablePrice) && $v->enablePrice) {

                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        if ($v->priceOptions === 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions === 'fixed_for_all') {
                            $price = $v->price;
                        }
                        $price = wcpa_get_price_shop($this->product, array(
                            'qty' => 1,
                            'price' => $price
                        ));
                        $label = $val->label . ' (' . wcpa_price($price, 1) . ')';
                    }
                    $price_data = $this->get_price_data($v, $val);
                }
                $field_name = (($check_type === 'radio') ? $name : $name . '[' . $k . ']');
                $size = '';
                if (isset($v->disp_size) && $v->disp_size > 10) {
                    $size .= 'height:' . $v->disp_size . 'px;';
                    if (isset($v->show_label_inside) && $v->show_label_inside) {
                        $size .= 'min-width:' . $v->disp_size . 'px;line-height:' . ($v->disp_size - 2) . 'px;';
                    } else {
                        $size .= 'width:' . $v->disp_size . 'px;';
                    }
                }

                echo '<div class="wcpa_color" >
                        <input type="' . $check_type . '" id="' . $name . '_' . $k . '" ' . $checked . ' name="' . $field_name . '" value="' . $val->value . '" ' . $option_class . '  ' . $price_data . ' ' . $chk_required . ' >
                        <label for="' . $name . '_' . $k . '">';
                $chk_required = '';
                if (isset($v->show_label_inside) && $v->show_label_inside) {
                    echo '<span class="label_inside disp_' . $v->disp_type . ' ' . wcpa_colorClass($val->color) . ' ' . ((isset($v->adjust_width) && $v->adjust_width) ? 'wcpa_adjustwidth' : '') . '"'
                    . 'style="background-color:' . $val->color . ';' . $size . '" title="' . $val->label . '" for="' . $name . '_' . $k . '" >'
                    . '' . $label . '</span>';
                } else {
                    echo '<span class="disp_' . $v->disp_type . ' ' . wcpa_colorClass($val->color) . '  " style="background-color:' . $val->color . ';' . $size . '" title="' . $val->label . '" for="' . $name . '_' . $k . '" ></span>'
                    . $label . ' ' . (($label === '' && !$is_lable_empty) ? '&nbsp;' : '');
                }

                echo'</label></div>';
            }
            if (isset($v->other) && $v->other) {
                $option_class = '';
                $label = __('Other', 'wcpa-text-domain');
                if (isset($v->enablePrice) && $v->enablePrice && $v->priceOptions == 'fixed_for_all') {
                    $price = $v->price;
                    $price = wcpa_get_price_shop($this->product, array(
                        'qty' => 1,
                        'price' => $price
                    ));
                    if ($v->pricingType === 'fixed') {
                        $label = __('Other', 'wcpa-text-domain') . '(' . wcpa_price($price, 1) . ')';
                    }

                    $price_data = $this->get_price_data($v);
                    $option_class .= 'wcpa_has_price ';
                } else {
                    $price_data = '';
                }
                $checked = '';
                $other_val = '';

                if (!wcpa_empty($default_value) && !$is_field_checked) {
                    $is_selected = true;
                } else {
                    $is_selected = false;
                }

                // $is_selected = isset($default_value['other']) ? $default_value['other'] : false;

                if ($is_selected === true && !wcpa_empty($default_value)) {
                    $checked = 'checked="checked"';
                    if (is_array($default_value)) {
                        $other_val = str_replace('WCPAOTH ', '', $default_value['other']);
                    } else {
                        $other_val = str_replace('WCPAOTH ', '', $default_value);
                    }
                }

                //$option_class key used for adding .wcpa_has_price class
                $option_class .= 'wcpa_other';
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $field_name = (($check_type == 'radio') ? $name : $name . '[other]');
                echo '<div class="wcpa_color_other">
                    <div class="wcpa_' . $check_type . '">
                    <input name="' . $field_name . '" ' . $option_class . '" '
                . 'id="' . $name . '_other" value="' . (($other_val === '') ? '' : 'WCPAOTH ' . $other_val) . '"  type="' . $check_type . '"  ' . $price_data . ' ' . $checked . '>
                 <label for="' . $name . '_other"><span class="wcpa_check"></span>' . $label . '</label>';

                echo '<input type="text" name="' . $name . '_other_val" class="wcpa_other_val" value="' . $other_val . '" >
                   </div> </div>';
            }
            echo '</div>';
        }
    }

    public function render_radio($v) {

        $name = (isset($v->name)) ? $v->name : '';
        $this->label_section($v);
        $default_value = $this->default_value($v);
        $chk_required = (isset($v->required)) ? 'required="required"' : '';
        $className = 'radio-group ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (isset($v->inline) && $v->inline) {
            $className .= 'radio-inline ';
        }
        if (isset($v->required) && $v->required) {
            $className .= 'wcpa_required ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }
        $price_data = '';
        $is_field_checked = FALSE;
        if (!wcpa_empty($default_value) && $default_value !== false) {
            $chk_required = '';
        }

        if ($v->values && !empty($v->values)) {
            echo '<div ' . $className . ' >';
            foreach ($v->values as $k => $val) {
                if (is_array($default_value)) {
                    $is_selected = (isset($default_value[$k]) && $default_value[$k] !== FALSE) ? true : false;
                } else {
                    $is_selected = ($default_value === $this->sanitize_values($val->value)) ? true : false;
                }

                if (!$is_field_checked && $is_selected) {
                    $is_field_checked = true;
                }
                $price = '';
                $option_class = '';
                if (isset($v->enablePrice) && $v->enablePrice) {
                    $option_class .= 'wcpa_has_price ';
                }
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $label = $val->label;
                if (isset($v->enablePrice) && $v->enablePrice) {

                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        if ($v->priceOptions == 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions == 'fixed_for_all') {
                            $price = $v->price;
                        }
                        $price = wcpa_get_price_shop($this->product, array(
                            'qty' => 1,
                            'price' => $price
                        ));
                        $label = $val->label . ' (' . wcpa_price($price, 1) . ')';
                    }

                    $price_data = $this->get_price_data($v, $val);
                }


                echo ' <div class="wcpa_radio">
                    <input name="' . $name . '" ' . $option_class . ' id="' . $name . '_' . $k . '"'
                . ' value="' . $val->value . '" ' . $price_data . '  type="radio" ' . (($is_selected !== false) ? 'checked="checked"' : '') . ' ' . $chk_required . ' >
                    <label for="' . $name . '_' . $k . '"><span class="wcpa_check"></span>' . $label . '</label>';
                if (isset($v->options_tooltip) && $v->options_tooltip && isset($val->tooltip) && $val->tooltip != '') {
                    echo'<span class="wcpa_tooltip_icon" >?';
                    echo '<span class="wcpa_tooltip">' . $val->tooltip . '</span>';
                    echo '</span>';
                }
                echo '</div>';
                $chk_required = ''; //requied need to set only for first item
            }
            if (isset($v->other) && $v->other) {
                $option_class = '';
                $label = __('Other', 'wcpa-text-domain');
                if (isset($v->enablePrice) && $v->enablePrice && $v->priceOptions == 'fixed_for_all') {
                    $price = $v->price;
                    $price = wcpa_get_price_shop($this->product, array(
                        'qty' => 1,
                        'price' => $price
                    ));
                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        $label = __('Other', 'wcpa-text-domain') . '(' . wcpa_price($price, 1) . ')';
                    }
                    $price_data = $this->get_price_data($v);
                    $option_class .= 'wcpa_has_price ';
                } else {
                    $price_data = '';
                }
                $checked = '';
                $other_val = '';

                if (!wcpa_empty($default_value) && !$is_field_checked) {
                    $is_selected = true;
                } else {
                    $is_selected = false;
                }


                if ($is_selected === true) {
                    $checked = 'checked="checked"';
                    $other_val = str_replace('WCPAOTH ', '', $default_value);
                }

                //$option_class key used for adding .wcpa_has_price class
                $option_class .= 'wcpa_other';
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }

                echo ' <div class="wcpa_radio">
                    <input name="' . $name . '" ' . $option_class . ' id="' . $name . '_other" '
                . ' ' . $price_data . ' '
                . 'value="' . (($other_val === '') ? '' : 'WCPAOTH ' . $other_val) . '" type="radio"  ' . $checked . '>
                    <label for="' . $name . '_other"><span class="wcpa_check"></span>' . $label . '</label>
                        
                 <input type="text" name="' . $name . '_other_val" class="wcpa_other_val" value="' . $other_val . '" >

                    </div>';
            }
            echo '</div>';
        }
    }

    public function render_select($v) {

        if ((isset($v->multiple) && $v->multiple)) {
            $is_multiple = true;
        } else {
            $is_multiple = false;
        }
        $name = (isset($v->name)) ? $v->name : '';
        if ($is_multiple) {
            $name = $name . '[]';
        }


        $data = '';
        $default_value = $this->default_value($v);

        $sel_required = (isset($v->required)) ? 'required="required"' : '';
        $multiple = ($is_multiple) ? 'multiple="true"' : '';

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }
        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
        }
        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $this->label_section($v);

        if ($v->values && !empty($v->values)) {


            echo '<div class="select" ><select  name="' . $name . '" ' . $className . ' ' . $multiple . ' ' . $sel_required . '>';
            if (isset($v->placeholder) && $v->placeholder != '') {

                echo '<option value="" >' . $v->placeholder . '</option>';
            }
            foreach ($v->values as $k => $val) {
                $label = $val->label;
                if (isset($v->enablePrice) && $v->enablePrice) {
                    if ($v->pricingType === 'fixed' && $this->settings['disp_hide_options_price'] === false) {
                        if ($v->priceOptions === 'different_for_all') {
                            $price = $val->price;
                        } else if ($v->priceOptions === 'fixed_for_all') {
                            $price = $v->price;
                        }
                        $price = wcpa_get_price_shop($this->product, array(
                            'qty' => 1,
                            'price' => $price
                        ));
                        $label = $val->label . ' (' . wcpa_price($price, 1) . ')';
                    }

                    $price_data = $this->get_price_data($v, $val);
                }


                if (is_array($default_value)) {
                    $is_selected = in_array($val->value, $default_value);
                } else {
                    $is_selected = ($default_value === $this->sanitize_values($val->value)) ? true : false;
                }

                // $selectedchk = ($is_selected) ? 'selected="selected"' : '';
                // $selectedchk = ($val->selected)?'selected="selected"':'';

                $selectedchk = ($is_selected) ? 'selected="selected"' : '';


                $option_print = '<option ' . $price_data . ' value="' . $val->value . '" ' . $selectedchk . '>' . $label . '</option>';
                $option_print = apply_filters('wcpa_select_option_print', $option_print, $label, $val->value);
                echo $option_print;
            }
            echo '</select>';
            if (!$is_multiple) {
                echo '<div class="select_arrow"></div>';
            }
            echo '</div>';
        }
    }

    public function render_hidden($v) {
        $name = (isset($v->name)) ? $v->name : '';
        $value = (isset($v->value)) ? $v->value : '';

        echo '<input type="hidden"  id="' . $name . '"  name="' . $name . '" value="' . $value . '"/>';
    }

    public function render_header($v) {

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }


        if ($v->subtype == 'h1') {
            echo '<h1 ' . $className . '>' . $v->label . '</h1>';
        } else if ($v->subtype == 'h2') {
            echo '<h2 ' . $className . '>' . $v->label . '</h2>';
        } else if ($v->subtype == 'h3') {
            echo '<h3 ' . $className . '>' . $v->label . '</h3>';
        }
    }

    public function render_paragraph($v) {

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        $price_data = '';
        if (isset($v->enablePrice) && $v->enablePrice) {
            $className .= 'wcpa_has_price ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $price_data = $this->get_price_data($v);

        if ($v->subtype == 'address') {
            echo '<address ' . $className . ' ' . $price_data . '>' . do_shortcode(nl2br($v->label)) . '</address>';
        } else if ($v->subtype == 'blockquote') {
            echo '<blockquote ' . $className . ' ' . $price_data . '>' . do_shortcode(nl2br($v->label)) . '</blockquote>';
        } else if ($v->subtype == 'output') {
            echo '<output ' . $className . ' ' . $price_data . '>' . do_shortcode(nl2br($v->label)) . '</output>';
        } else if ($v->subtype == 'canvas') {
            echo '<canvas ' . $className . ' ' . $price_data . '>' . do_shortcode(nl2br($v->label)) . '</canvas>';
        } else {
            echo '<p ' . $className . ' ' . $price_data . '>' . do_shortcode(nl2br($v->label)) . '</p>';
        }
    }

    public function render_separator($v) {

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }
        $style = '';
        if (isset($v->height) && $v->height > 0) {
            $style .= 'style="height:' . $v->height . 'px"';
        }
        echo '<div ' . $className . ' ' . $style . '>&nbsp;</div>';
    }

    public function enqueue_recaptcha() {
        wp_enqueue_script($this->_token . '-recaptcha');
    }

    public function enqueue_googlemap() {
        wp_enqueue_script($this->_token . '-googlemapplace');
    }

    public function enqueue_detetimepicker() {

        wp_enqueue_script($this->_token . '-datetime');
    }

    public function enqueue_colorpicker() {
        wp_enqueue_script($this->_token . '-colorpicker');
    }

    public function __call($name, $arguments) {
        return null;
    }

}
