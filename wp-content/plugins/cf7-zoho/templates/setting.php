<?php
  if ( ! defined( 'ABSPATH' ) ) {
     exit;
 }  ?><div class="crm_fields_table">
    <div class="crm_field">
  <div class="crm_field_cell1"><label for="vx_name"><?php _e("Account Name",'contact-form-zoho-crm'); ?></label>
  </div>
  <div class="crm_field_cell2">
  <input type="text" name="crm[name]" value="<?php echo !empty($name) ? $name : 'Account #'.$id; ?>" id="vx_name" class="crm_text">

  </div>
  <div class="clear"></div>
  </div>
                
    <div class="crm_field">
  <div class="crm_field_cell1">
  <label for="vx_dc"><?php _e('Data Center','contact-form-zoho-crm'); ?></label>
  </div>
  <div class="crm_field_cell2">
<select name="crm[dc]" class="crm_text" id="vx_dc" data-save="no" <?php if( !empty($info['access_token'])){ echo 'disabled="disabled"'; } ?> >
  <?php $envs=array(
  'com'=>__('zoho.com','contact-form-zoho-crm'),
  'eu'=>__('zoho.eu','contact-form-zoho-crm'),
  'in'=>__('zoho.in','contact-form-zoho-crm'),
  );
foreach($envs as $k=>$v){
    $sel='';
if(!empty($info['dc']) && $info['dc'] == $k){ $sel='selected="selected"'; }
echo '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
}
 ?>
 </select>
  </div>
  <div class="clear"></div>
  </div>
  <script type="text/javascript">
  jQuery(document).ready(function($){
  /*  $('#vx_dc').change(function(){
        var val=$(this).val();
        var btn=$('.sf_login');
      var url='https://accounts.zoho.'+val+'/';
      var dc=btn.attr('data-url');  
  btn.attr('href',url+dc); 
    })*/
    var elem=$('#mainform');
    var form=elem.serialize();
      $('.sf_login').click(function(e){
      var form2=elem.serialize(); 
      if(form != form2){
         e.preventDefault();
        alert('Please "Save Changes" first');  
      }
      });  
  })
  </script>
<div class="crm_field">
  <div class="crm_field_cell1"><label><?php _e('Zoho Access','contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2">
  <?php if(isset($info['access_token'])  && $info['access_token']!="") {
  ?>
  <div style="padding-bottom: 8px;" class="vx_green"><i class="fa fa-check"></i> <?php
  echo sprintf(__("Authorized Connection to %s on %s",'contact-form-zoho-crm'),'<code>Zoho</code>',date('F d, Y h:i:s A',$info['_time']));
?></div><?php
  }else{
      $ret=$link.'&'.$this->id."_tab_action=get_token&vx_action=redirect&id=".$id."&vx_nonce=".$nonce;
$dc=!empty($info['dc']) ? $info['dc'] : 'com';
$ret_dc=$ret.'&dc='.$dc;
      $auth_url='oauth/v2/auth?scope=ZohoCRM.modules.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.Read&response_type=code&client_id='.$client['client_id'].'&access_type=offline&redirect_uri='.$client['call_back'];

$ac_url=$api->ac_url();      
?>
  <a class="button button-default button-hero sf_login" data-id="<?php echo esc_html($client['client_id']) ?>" href="<?php echo $ac_url.$auth_url.'&state='.urlencode($ret_dc) ?>"  data-state="<?php echo urlencode($ret); ?>" data-url="<?php echo $auth_url ?>"  title="<?php _e("Login with Zoho",'contact-form-zoho-crm'); ?>" > <i class="fa fa-lock"></i> <?php _e("Login with Zoho",'contact-form-zoho-crm'); ?></a>
<?php } ?>
  </div>
  <div class="clear"></div>
  </div>                  
<?php if(isset($info['access_token'])  && $info['access_token']!="") { ?>
<div class="crm_field">
  <div class="crm_field_cell1"><label><?php _e("Revoke Access",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2">  <a class="button button-secondary" id="vx_revoke" href="<?php echo $link."&".$this->id."_tab_action=get_token&vx_nonce=".$nonce.'&id='.$id?>"><i class="fa fa-unlock"></i> <?php _e("Revoke Access",'contact-form-zoho-crm'); ?></a>
  </div>
  <div class="clear"></div>
  </div> 
<?php } ?>
  
   <div class="crm_field">
  <div class="crm_field_cell1"><label for="vx_custom_app_check"><?php _e("Zoho Client",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2"><div><label for="vx_custom_app_check"><input type="checkbox" name="crm[custom_app]" id="vx_custom_app_check" value="yes" <?php if($this->post('custom_app',$info) == "yes"){echo 'checked="checked"';} ?>><?php echo __('Use Own Zoho App - If you want to connect multiple Zoho accounts to multiple sites then use a separate Zoho App for each site ','contact-form-zoho-crm'); ?></label></div>
  </div>
  <div class="clear"></div>
  </div>

<div id="vx_custom_app_div" style="<?php if($this->post('custom_app',$info) != "yes"){echo 'display:none';} ?>">
     <div class="crm_field">
  <div class="crm_field_cell1"><label for="app_id"><?php _e("Client ID",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2">
     <div class="vx_tr">
  <div class="vx_td">
  <input type="password" id="app_id" name="crm[app_id]" class="crm_text" placeholder="<?php _e("Zoho Client ID",'contact-form-zoho-crm'); ?>" value="<?php echo esc_html($this->post('app_id',$info)); ?>">
  </div><div class="vx_td2">
  <a href="#" class="button vx_toggle_btn vx_toggle_key" title="<?php _e('Toggle Consumer Key','contact-form-zoho-crm'); ?>"><?php _e('Show Key','contact-form-zoho-crm') ?></a>
  
  </div></div>
  
    <div class="howto">
  <ol>
  <li><?php echo sprintf(__('Create New Client %shere%s','contact-form-zoho-crm'),'<a href="https://accounts.zoho.com/developerconsole" target="_blank">','</a>'); ?></li>
  <li><?php _e('Enter Client Name(eg. My App)','contact-form-zoho-crm'); ?></li>
  <li><?php echo sprintf(__('Enter %s or %s in Redirect URI','contact-form-zoho-crm'),'<code>https://www.crmperks.com/google_auth/</code>','<code>'.$link."&".$this->id.'_action=get_code</code>'); ?>
  </li>
<li><?php _e('Select Client Type as "Web Based"','contact-form-zoho-crm'); ?></li>
<li><?php _e('Save Application','contact-form-zoho-crm'); ?></li>
<li><?php echo __('Copy Client Id and Secret','contact-form-zoho-crm'); ?></li>
   </ol>
  </div>
  
</div>
  <div class="clear"></div>
  </div>
     <div class="crm_field">
  <div class="crm_field_cell1"><label for="app_secret"><?php _e("Client Secret",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2">
       <div class="vx_tr" >
  <div class="vx_td">
 <input type="password" id="app_secret" name="crm[app_secret]" class="crm_text"  placeholder="<?php _e("Zoho Client Secret",'contact-form-zoho-crm'); ?>" value="<?php echo esc_html($this->post('app_secret',$info)); ?>">
  </div><div class="vx_td2">
  <a href="#" class="button vx_toggle_btn vx_toggle_key" title="<?php _e('Toggle Consumer Secret','contact-form-zoho-crm'); ?>"><?php _e('Show Key','contact-form-zoho-crm') ?></a>
  
  </div></div>
  </div>
  <div class="clear"></div>
  </div>
       <div class="crm_field">
  <div class="crm_field_cell1"><label for="app_url"><?php _e("Zoho Client Redirect URL",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2"><input type="text" id="app_url" name="crm[app_url]" class="crm_text" placeholder="<?php _e("Zoho Client Redirect URL",'contact-form-zoho-crm'); ?>" value="<?php echo esc_html($this->post('app_url',$info)); ?>"> 

  </div>
  <div class="clear"></div>
  </div>
  </div>

 <div class="crm_field">
  <div class="crm_field_cell1"><label><?php _e("Test Connection",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2">      <button type="submit" class="button button-secondary" name="vx_test_connection"><i class="fa fa-refresh"></i> <?php _e("Test Connection",'contact-form-zoho-crm'); ?></button>
  </div>
  <div class="clear"></div>
  </div>
   
  <div class="crm_field">
  <div class="crm_field_cell1"><label for="vx_error_email"><?php _e("Notify by Email on Errors",'contact-form-zoho-crm'); ?></label></div>
  <div class="crm_field_cell2"><textarea name="crm[error_email]" id="vx_error_email" placeholder="<?php _e("Enter comma separated email addresses",'contact-form-zoho-crm'); ?>" class="crm_text" style="height: 70px"><?php echo isset($info['error_email']) ? $info['error_email'] : ""; ?></textarea>
  <span class="howto"><?php _e("Enter comma separated email addresses. An email will be sent to these email addresses if an order is not properly added to Zoho. Leave blank to disable.",'contact-form-zoho-crm'); ?></span>
  </div>
  <div class="clear"></div>
  </div>  
   
 
  <button type="submit" value="save" class="button-primary" title="<?php _e('Save Changes','contact-form-zoho-crm'); ?>" name="save"><?php _e('Save Changes','contact-form-zoho-crm'); ?></button>  
  </div>  