jQuery(document).ready(function($) {
  var ivole_email_color_options1 = { palettes: ['#0f9d58','#db4437', '#607d8b', '#3f51b5', '#cddc39', '#4285f4'] };
  var ivole_email_color_options2 = { palettes: ['#000000', '#2f4f4f', '#696969', '#c0c0c0', '#dcdcdc','#ffffff'] };
  jQuery('#ivole_email_color_bg').wpColorPicker(ivole_email_color_options1);
  jQuery('#ivole_email_color_text').wpColorPicker(ivole_email_color_options2);
  jQuery('#ivole_form_color_bg').wpColorPicker(ivole_email_color_options1);
  jQuery('#ivole_form_color_text').wpColorPicker(ivole_email_color_options2);
  jQuery('#ivole_email_coupon_color_bg').wpColorPicker(ivole_email_color_options1);
  jQuery('#ivole_email_coupon_color_text').wpColorPicker(ivole_email_color_options2);
});
