<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if(!class_exists('vxcf_zoho_api')){
    
class vxcf_zoho_api extends vxcf_zoho{
  
  public $token='' ; 
    public $info=array() ; // info
    public $url='';
    public $ac_url='https://accounts.zoho.com/';
    public $error= "";
    public $timeout= "15";

function __construct($info) {
     
    if(isset($info['data'])){ 
       $this->info= $info['data'];
       $domain='com';
       if($this->info['dc']){
       $domain=$this->info['dc'];    
       }
       $this->ac_url='https://accounts.zoho.'.$domain.'/';
    }
}
public function get_token(){

    $users=$this->get_users();
    $info=$this->info;

    if(is_array($users) && count($users)>0){
    $info['valid_token']='true';    
    }else{
        $info['zoho_error']=$users;
      unset($info['valid_token']);  
    }
return $info;
}
  /**
  * Get New Access Token from infusionsoft
  * @param  array $form_id Form Id
  * @param  array $info (optional) Infusionsoft Credentials of a form
  * @param  array $posted_form (optional) Form submitted by the user,In case of API error this form will be sent to email
  * @return array  Infusionsoft API Access Informations
  */
public function refresh_token($info=""){
  if(!is_array($info)){
  $info=$this->info;
  }

  if(!isset($info['refresh_token']) || empty($info['refresh_token'])){
   return $info;   
  }
  $client=$this->client_info(); 
  $ac_url=$this->ac_url(); 
  ////////it is oauth    
  $body=array("client_id"=>$client['client_id'],"client_secret"=>$client['client_secret'],"redirect_uri"=>$client['call_back'],"grant_type"=>"refresh_token","refresh_token"=>$info['refresh_token']);
  $re=$this->post_crm($ac_url.'oauth/v2/token','token',$body);

  if(isset($re['access_token']) && $re['access_token'] !=""){ 
  $info["access_token"]=$re['access_token'];
 // $info["refresh_token"]=$re['refresh_token'];
 // $info["org_id"]=$re['id'];
  $info["class"]='updated';
  $info["token_time"]=time(); 
  $info['valid_token']='true'; 
  }else{
      $info['valid_token']=''; 
  $info['error']=$re['error'];
  $info['access_token']="";
   $info["class"]='error';
  } 
  //api validity check
  $this->info=$info;
  //update infusionsoft info 
  //got new token , so update it in db
  $this->update_info( array("data"=> $info),$info['id']); 
  return $info; 
  }
public function handle_code(){
      $info=$this->info;
      $id=$info['id'];
 
        $client=$this->client_info();
  $log_str=array(); $token=array();
  if(isset($_REQUEST['code'])){
  $code=$this->post('code'); 
  
  if(!empty($code)){

     $ac_url=$this->ac_url(); 
  $body=array("client_id"=>$client['client_id'],"client_secret"=>$client['client_secret'],"redirect_uri"=>$client['call_back'],"grant_type"=>"authorization_code","code"=>$code);
  $token=$this->post_crm($ac_url.'oauth/v2/token','token',$body);
  }
  if(isset($_REQUEST['error'])){
   $token['error']=$this->post('error');   
  }
  if(empty($token['refresh_token'])){
      $token['access_token']='';
      $dc=!empty($info['dc']) ? $info['dc'] : 'com';
      $token['error']='Go to <a href="'.$ac_url.'u/h#sessions/userconnectedapps" target="_blank">accounts.zoho.'.$dc.' -> Sessions -> Connected Apps</a> and remove "CRM Perks" app'; 
  }
  
  }else if(!empty($info['refresh_token'])){
        $token=$this->post_crm($ac_url.'oauth/v2/token/revoke','token',array('token'=>$info['refresh_token']));
  }

  $url='';
  if(!empty($token['api_domain'])){
  $url=$token['api_domain'];  
  }

  $info['instance_url']=$url;
  $info['access_token']=$this->post('access_token',$token);
  $info['token_exp']=$this->post('expires_in_sec',$token);
  $info['client_id']=$client['client_id'];
  $info['_id']=$this->post('id',$token);
  $info['refresh_token']=$this->post('refresh_token',$token);
  $info['token_time']=time();
  $info['_time']=time();
  $info['error']=$this->post('error',$token);
  $info['api']="api";
  $info["class"]='error';
  $info['valid_token']=''; 
  if(!empty($info['access_token'])){
  $info["class"]='updated';
  $info['valid_token']='true'; 
  }

  $this->info=$info;

  $this->update_info( array('data'=> $info) , $id); //var_dump($info); die();
  return $info;
  }

public function ac_url(){
    $dc='com';
    if(!empty($this->info['dc'])){
    $dc=$this->info['dc'];    
    }
    $this->ac_url='https://accounts.zoho.'.$dc.'/';
  return $this->ac_url;  
}
public function get_crm_objects(){
$arr= $this->post_crm('settings/modules');

if(!empty($arr['modules'])){
$objects=$arr['modules'];  
  $objects_f="";
  if(is_array($objects)){
        $objects_f=array();
     foreach($objects as $object){
         if(isset($object['editable']) && $object['editable'] == true ){
    $objects_f[$object['api_name']]=$object['module_name'];   
         }
     }    
  }
 return $objects_f;   
}else if(isset($arr['error'])){
 return $arr['error'];   
}

}
public function get_crm_fields($module,$fields_type=""){

$arr=$this->post_crm('settings/fields?module='.$module);
$fields=array();
if(isset($arr['fields']) && is_array($arr['fields'])){
foreach($arr['fields'] as $field){

if( isset($field['field_read_only']) && $field['field_read_only'] === false && !in_array($field['data_type'],array('fileupload')) ){ //visible = true
            $name=$field['api_name'];
        $v=array('label'=>$field['field_label'],'name'=>$field['api_name'],'type'=>$field['data_type']);
       if(isset($field['custom_field']) && $field['custom_field'] === true){
       $v['custom']='yes';    
       }
//$v['req']=$required;
if(isset($field['length'])){
$v["maxlength"]=$field['length'];
}
       if(!empty($field['pick_list_values'])){
         $ops=$eg=array();
         foreach($field['pick_list_values'] as $op){
         $ops[]=array('value'=>$op['actual_value'],'label'=>$op['display_value']);
         $eg[]=$op['actual_value'].'='.$op['display_value'];
         }  
       $v['options']=$ops;
       $v['eg']=implode(', ',array_slice($eg,0,10));
       }  
$fields[$name]=$v;   
        }         
}
$fields['tags']=array('label'=>'Tags','name'=>'tags','type'=>'tags','maxlength'=>'0'); 
//if(in_array($module,array('Leads','Contacts'))){
$fields['vx_attachments']=array('label'=>'Attachments - Related List','name'=>'vx_attachments','type'=>'files','maxlength'=>'0');  
//}
/*    if(in_array($module,array('SalesOrders','PurchaseOrders'))){
      $fields['sub_total']=array('label'=>'Sub Total','name'=>'sub_total','type'=>'text','maxlength'=>'100');  
      $fields['grand_total']=array('label'=>'Grand Total','name'=>'grand_total','type'=>'text','maxlength'=>'100');  
      $fields['tax']=array('label'=>'Tax','name'=>'tax','type'=>'text','maxlength'=>'100');  
      $fields['adjustment']=array('label'=>'Adjustment','name'=>'adjustment','type'=>'text','maxlength'=>'100');
    }*/
/*
$arr=$this->post_crm('settings/related_lists?module='.$module);
 if(!empty($arr['related_lists'])){
     foreach($arr['related_lists'] as $field){ 
      $v=array('label'=>$field['display_label'].' - Related List','name'=>$field['api_name'],'type'=>'related_list'); 
  $fields[$field['api_name']]=$v;       
     }
 }*/   
if($fields_type =="options"){
$field_options=array();
if(is_array($fields)){
foreach($fields as $k=>$f){
if(isset($f['options']) && is_array($f['options']) && count($f['options'])>0){
$field_options[$k]=$f;         
}
}    
}
return $field_options;
} 

return $fields;    
}
else if(isset($arr['message'])){
 return $arr['message'];   
}

    }
  /**
  * Get campaigns from salesforce
  * @return array Salesforce campaigns
  */
public function get_campaigns(){ 

   $arr= $this->post_crm('Campaigns');
  ///seprating fields
  $msg='No Campaign Found';
$fields=array();
if(!empty($arr['data'])){
foreach($arr['data'] as $val){
$fields[$val['id']]=$val['Campaign_Name'];
}
   
}else if(isset($arr['message'])){
 $msg=$arr['message'];   
}

  return empty($fields) ? $msg : $fields;
}
  /**
  * Get users from zoho
  * @return array users
  */
public function get_users(){ 
$arr=$this->post_crm('users?type=AllUsers');

$users=array();    
  ///seprating fields
  $msg='No User Found';
if(!empty($arr['users'])){
if(is_array($arr['users']) && isset($arr['users'][0])){
  foreach($arr['users'] as $k=>$v){
   $users[$v['id']]=$v['full_name'];   
  }  
}
}else if(isset($arr['message'])){
 $msg=$arr['message'];   
}

return empty($users) ? $msg : $users;
}
  /**
  * Get users from zoho
  * @return array users
  */
public function get_price_books(){ 

$arr=$this->post_crm('Price_Books');

  ///seprating fields
  $msg=__('No Price Book Found','woocommerce-salesforce-crm');
$fields=array();
if(!empty($arr['data'])){
foreach($arr['data'] as $val){
$fields[$val['id']]=$val['Price_Book_Name'];
}
   
}else if(isset($arr['message'])){
 $msg=$arr['message'];   
}
  return empty($fields) ? $msg : $fields;
}

public function push_object($module,$fields,$meta){

/*    
$p='Attachments';
$p='Contacts/3703799000000209001';

$post=json_decode($json,true);
//$post=array('file'=>'@'.realpath(__DIR__.'/banner9.png'));
$post=array('attachmentUrl'=>'https://www.express.com.pk/images/NP_ISB/20181225/Sub_Images/1105997112-1.jpg','File_Name'=>'exp.jpg','Size'=>'10');
$post=array('Last_Name'=>'lewiss','URL_1'=>'http://google.com','File_Upload_1'=>array(array('entity_Id' => 3.7037990000002E+18)));
//$post=http_build_query($post);
$p='Sales_Orders/149964000000152015/Products/149964000000152001';
$p='Contacts/149964000000140007/Products/149964000000152001';
$p='Sales_Orders/149964000000152015';
$p='Sales_Orders';
$json='{"Subject":"touseefcccdd","Description":"ahmadhcccsdd","Billing_City":"houston","Billing_State":"TA","Billing_Country":"PK","Billing_Code":"","Owner":"149964000000132011","Product_Details":[{"product":{"id":"149964000000151009"},"quantity":2},{"product":{"id":"149964000000152001"},"quantity":5}]}';
$p='Products/149964000000152180/Price_Books/149964000000148008';
$post=json_encode(array('data'=>array(array('qty'=>'1'))));
$post=json_encode(array('data'=>array(array('list_price'=>558))));
//$post=json_encode(array('data'=>array(json_decode($json,true))));
$r=$this->post_crm($p,'put',$post);
var_dump($r,$this->info,$post); die();*/

//check primary key
 $extra=array();

  $debug = isset($_GET['vx_debug']) && current_user_can('manage_options');
  $event= isset($meta['event']) ? $meta['event'] : '';
  $custom_fields= isset($meta['fields']) ? $meta['fields'] : array();
  $id= isset($meta['crm_id']) ? $meta['crm_id'] : '';

  if($debug){ ob_start();}
if(isset($meta['primary_key']) && $meta['primary_key']!="" && isset($fields[$meta['primary_key']]['value']) && $fields[$meta['primary_key']]['value']!=""){    
$search=$fields[$meta['primary_key']]['value'];
$field=$meta['primary_key'];
$field_type= isset($custom_fields[$field]['type']) ? $custom_fields[$field]['type'] : '';
if(!in_array($field_type,array('email','phone'))){ $field_type='criteria'; 
$search='(('.$field.':equals:'.$search.'))';
}
    //search object
$path=$module.'/search?'.$field_type.'='.urlencode($search);
$search_response=$this->post_crm($path);
$extra["body"]=$path;
$extra["search"]=$search;
$extra["response"]=$search_response;
      
  if($debug){
  ?>
  <h3>Search field</h3>
  <p><?php print_r($field) ?></p>
  <h3>Search term</h3>
  <p><?php print_r($search) ?></p>
    <h3>POST Body</h3>
  <p><?php print_r($body) ?></p>
  <h3>Search response</h3>
  <p><?php print_r($search_response) ?></p>  
  <?php
  }
      if(is_array($search_response) && !empty($search_response['data']) ){
          $search_response=$search_response['data'];
      if( count($search_response)>5){
       $search_response=array_slice($search_response,count($search_response)-5,5);   
      }
      $extra["response"]=$search_response;
      $id=$search_response[0]['id'];
  }

}



$post=array(); $status=$action=$method=''; $send_body=true;
 $entry_exists=false;
 $link=""; $error=""; 
 $path='';
 $arr=array();
if($id == ""){
if(empty($meta['new_entry'])){
$method='post';
}else{
    $error='Entry does not exist';
}
$action="Added";  $status="1";
}
else{
 $entry_exists=true;
if($event == 'add_note'){ 
$module='Notes';
$action="Added";
$status="1"; 
$send_body=false;
$post=array('Title'=>$fields['Title']['value'],'Body'=>$fields['Body']['value'],'Parent_Id'=>$fields['ParentId']['value']);   
$arr=$this->post_note($post,$meta['related_object']);
if(isset($arr['data'][0]['details']['id'])){
$id=$arr['data'][0]['details']['id']; 
}
}
else if(in_array($event,array('delete','delete_note'))){
 $send_body=false;
     if($event == 'delete_note'){ 
   $module='Notes';
     }
     $method="delete";
     $action="Deleted";
  $status="5";  
  $path=$module.'?ids='.$id;
}
else{
    //update object
$status="2"; $action="Updated";
if(empty($meta['update'])){
$method='put';
$path=$module.'/'.$id;
}
}
  }

if(!empty($method)){
$zoho_products=$related=array();
$module_products=false;

if($send_body){
foreach($fields as $k=>$v){
   $type=isset($custom_fields[$k]['type']) ? $custom_fields[$k]['type'] : ''; 
    if( in_array($type, array('files','tags') )){
     $related[$type]=$v['value'];   
    }else if( in_array($type, array('fileupload') )){
//this field is not supported in zoho API  
    }else if($type == 'datetime'){
        // to do , change time offset from+00:00 to real
     $post[$k]=date('c',strtotime($v['value']));   // Y-m-d\TH:i:s-08:00  
    }else if($type == 'date'){
     $post[$k]=date('Y-m-d',strtotime($v['value']));  
    }else if($type == 'multiselectpicklist'){
        if(is_string($v['value'])){ $v['value']=array($v['value']); }
      $post[$k]=$v['value'];  
    }else if($type == 'boolean'){
      $post[$k]=!empty($v['value']) ? true : false;  
    }else{
    $post[$k]=$v['value']; }
}
//var_dump($post); die();
 //change owner id
  if(isset($meta['owner']) && $meta['owner'] == "1"){
   $post['Owner']=$meta['user'];   
  }

  if(!empty($meta['order_items'])){
   $order_res=$this->get_zoho_products($meta);   
  $zoho_products=$order_res['res'];
  if(is_array($order_res['extra'])){
  $extra=array_merge($extra, $order_res['extra']);
  } 
 if(is_array($zoho_products) && count($zoho_products)>0){
if(in_array($module,array('Sales_Orders','Purchase_Orders'))){
 foreach($zoho_products as $v){
$post['Product_Details'][]=array('product'=>array('id'=>$v['id']),'quantity'=>$v['qty']);   
}   
  }else{
  $module_products=true;    
  }

 }

}

///echo json_encode($post); die();
$post=array('data'=>array($post));
}

if(!empty($method)){
if(empty($path)){  $path=$module; }
$arr=$this->post_crm( $path, $method,json_encode($post));
//var_dump($arr,$post,$path); die('-------');
}
if(!empty($arr['data'])){
    if(isset($arr['data'][0]['details']['id'])){
$id=$arr['data'][0]['details']['id']; 
    }else if(isset($arr['data'][0]['message'])){
$error=$arr['data'][0]['code'].' : '.$arr['data'][0]['message'];   
$status='';       
}

}
else if(isset($arr['message'])){
$error=$arr['code'].' : '.$arr['message'];   
$status='';       
}

if(!empty($id)){
//add to campaign
if(isset($meta['add_to_camp']) && $meta['add_to_camp'] == "1"){
   $extra['Campaign Path']=$camp_path=$module.'/'.$id.'/Campaigns/'.$meta['campaign'];
   $camp_post=array('data'=>array(array('Member_Status'=>'active')));
   $extra['Add Campaign']=$this->post_crm($camp_path,'put',json_encode($camp_post));   
  }
//add tags  
if(!empty($related['tags'])){ 
if(is_array($related['tags'])){ $related['tags']=implode(',',$related['tags']); }
$camp_path=$module.'/'.$id.'/actions/add_tags?tag_names='.urlencode($related['tags']);
$extra['Add Tags']=$this->post_crm($camp_path,'post'); 
}
//add files
if(!empty($related['files'])){
 $camp_path=$module.'/'.$id.'/Attachments';    
   //     $upload = wp_upload_dir();
$is_multi=json_decode($related['files'],true);
if(!is_array($is_multi)){ $is_multi=array($related['files']); }

if(!empty($is_multi)){
foreach($is_multi as $k=>$file){
//  $file=str_replace($upload['baseurl'],$upload['basedir'],$file);
$extra['Add Files '.$k]=$this->post_crm($camp_path,'post',array('attachmentUrl'=>$file)); 

} }
 

}

if($module_products){
foreach($zoho_products as $k=>$v){
$extra['Add Product Path '.$k]=$path=$module.'/'.$id.'/Products/'.$v['id'];
$post=json_encode(array('data'=>array(array('Quantity'=>$v['qty']))) );
$extra['Add Products '.$k]=$this->post_crm($path,'put',$post);   
}
}
 
}

}
if(!empty($id)){
   $domain=!empty($this->info['dc']) ? $this->info['dc'] : 'com'; 
   // $link='https://crm.zoho.'.$domain.'/crm/EntityInfo.do?module='.$module."&id=".$id; 
    $link='https://crm.zoho.'.$domain.'/crm/tab/'.str_replace('_','',$module).'/'.$id; 
}
  if($debug){
  ?>
  <h3>Account Information</h3>
  <p><?php //print_r($this->info) ?></p>
  <h3>Data Sent</h3>
  <p><?php print_r($post) ?></p>
  <h3>Fields</h3>
  <p><?php echo json_encode($fields) ?></p>
  <h3>Response</h3>
  <p><?php print_r($response) ?></p>
  <h3>Object</h3>
  <p><?php print_r($module."--------".$action) ?></p>
  <?php
 echo  $contents=trim(ob_get_clean());
  if($contents!=""){
  update_option($this->id."_debug",$contents);   
  }
  }
       //add entry note
 if(!empty($status) && !empty($meta['__vx_entry_note']) && !empty($id)){
 $disable_note=$this->post('disable_entry_note',$meta);
   if(!($entry_exists && !empty($disable_note))){
       $entry_note=$meta['__vx_entry_note'];
       $entry_note['Parent_Id']=$id;
   

$note_response=$this->post_note($entry_note,$module);
  $extra['Note Body']=$entry_note;
  $extra['Note Response']=$note_response;
 
   }  
 }


return array("error"=>$error,"id"=>$id,"link"=>$link,"action"=>$action,"status"=>$status,"data"=>$fields,"response"=>$arr,"extra"=>$extra);
}
public function post_note($post,$module){
  $re=array('Title'=>'Note_Title','Body'=>'Note_Content');
    foreach($post as $k=>$v){
  if(isset($re[$k])){
   $post[$re[$k]]=$v;
   unset($post[$k]);   
  }
  }
     $post['se_module']=$module; 
return $this->post_crm('Notes','POST', json_encode(array('data'=>array($post))) );  
}
public function get_zoho_products($meta){ 

      $_order=self::$_order;
     $items=$_order->get_items();
     $products=array();  $order_items=array(); $sales_response=array();  $extra=array();
     if(is_array($items) && count($items)>0 ){
      foreach($items as $item_id=>$item){

          $sku=''; $qty=$unit_price=0;
          if(method_exists($item,'get_product')){
  // $p_id=$v->get_product_id();  
   $product=$item->get_product();
   $total=$item->get_total();
   $qty = $item->get_quantity();
 //  $total=$item->get_total();
   $title=$product->get_title();
   $sku=$product->get_sku();     
   $unit_price=$product->get_price();     
          }
          else{ //version_compare( WC_VERSION, '3.0.0', '<' )  , is_array($item) both work
          $line_item=$this->wc_get_data_from_item($item); 
   $p_id= !empty($line_item['variation_id']) ? $line_item['variation_id'] : $line_item['product_id'];
        $line_desc=array();
        if(!isset($products[$p_id])){
        $product=new WC_Product($p_id);
        }else{
         $product=$products[$p_id];   
        }
        $qty=$line_item['qty'];
        $products[$p_id]=$product;
        $sku=$product->get_sku(); 
        if(empty($sku) && !empty($line_item['product_id'])){ 
            //if variable product is empty , get simple product sku
            $product_simple=new WC_Product($line_item['product_id']);
            $sku=$product_simple->get_sku(); 
        }
        $unit_price=$product->get_price();
        $title=$product->get_title();
          }
      ///  var_dump($sku,$p_id); die('------die-------');
        //  
        $product_detail=array('price'=>$unit_price,'qty'=>$qty);

 $url='Products/search?criteria='.urlencode('((Product_Code:equals:'.$sku.'))');
 $search_response=$this->post_crm($url); 
 $product_id='';
if(!empty($search_response['data'][0]['id'])){
  $product_id=$search_response['data'][0]['id'];  
  $extra['Search Product - '.$sku]=$search_response['data'][0];
}else{
  $extra['Search Product - '.$sku]=$search_response;  
}

if(empty($product_id)){ //create new product
  //  
$path='Products';
$fields=array('Product_Name'=>$title,'Product_Code'=>$sku,'Unit_Price'=>$unit_price);  
if(method_exists($product,'get_stock_quantity')){
   $fields['Qty_in_Stock']=$product->get_stock_quantity();
} 
$post=json_encode(array('data'=>array($fields)));
$arr=$this->post_crm('Products','post',$post); 

///var_dump($arr,$fields); die();
$extra['Product Post - '.$sku]=$fields;
$extra['Create Product - '.$sku]=$arr;

if(isset($arr['data'][0]['details']['id'])){
$product_id=$arr['data'][0]['details']['id']; 
}

if(!empty($meta['price_book']) && !empty($product_id)){ // add to price book
$price_book=$meta['price_book'];
$path='Products/'.$product_id.'/Price_Books/'.$meta['price_book']; 
$post=array('list_price'=>(float)$unit_price); 
$post=json_encode(array('data'=>array($post)));
$arr=$this->post_crm($path,'put',$post); 

$extra['Add PriceBook - '.$sku]=$post.'----'.$path;
$extra['PriceBook Redult - '.$sku]=$arr;  
}

//var_dump($post,$product_id,$book_post); die('--------------');
}
if(!empty($product_id)){ //create order here
$product_detail['id']=$product_id;
$sales_response[$product_id]=$product_detail;
}

     }


     }
     


       return array('res'=>$sales_response,'extra'=>$extra);
  }

public function client_info(){
      $info=$this->info;
  $client_id='1000.VFO2QGIQUKMK66057CVLZ8OM1RU9JT';
  $client_secret='feddae1bd7831d4b69e2e4d26ad2057dc8d2d1685a';
  $call_back="https://www.crmperks.com/google_auth/";
  
  $secret=array('eu'=>'a4e8d2c2284766a748674911a1f5ecbb0a1d7da460','in'=>'d944e3292b8377374725017d934e301f4d2f126f98');

  if($this->id == 'vxc_zoho'){
      $client_id='1000.JIR7NH735QWJ15857WRBLPYZQ96LZJ';
  $client_secret='ee5194c9cb5876a2133a03657ef01f7490529bfff4';  
  $secret=array('eu'=>'f659dba19a084551da0d3d34080ac4b06b23e5b976','in'=>'09e03e8e5ead546bbd8932368cf8b2d0a9fdda2f7e');
  }else if($this->id == 'vxg_zoho'){
      $client_id='1000.5X3DYKDO3XDH837304FOWEEUQRIYLM';
  $client_secret='91eaa6878b6d0c77644c26a5c4c9b9da394a353e78';  
  $secret=array('eu'=>'cf65bd821349873353d3c75c747e951fb87706991a','in'=>'703fd2dd6384cdaa8fd648ba7dc63f199866fe12f0');
  }
  //custom app
  if(is_array($info)){
      
      if(!empty($info['dc']) && isset($secret[$info['dc']])){
        $client_secret=$secret[$info['dc']];  
      }
      if($this->post('custom_app',$info) == "yes" && $this->post('app_id',$info) !="" && $this->post('app_secret',$info) !="" && $this->post('app_url',$info) !=""){
     $client_id=$this->post('app_id',$info);     
     $client_secret=$this->post('app_secret',$info);     
     $call_back=$this->post('app_url',$info);     
      }
  }
  return array("client_id"=>$client_id,"client_secret"=>$client_secret,"call_back"=>$call_back);
}
public function post_crm($path,$method='get',$body=""){
$header=array();   //'content-type'=>'application/x-www-form-urlencoded' ;

if($method == 'token'){
$method='post';   

}else{
  
$url=isset($this->info['instance_url']) ? $this->info['instance_url'] : '';

$path=$url.'/crm/v2/'.$path;
$token_time=!empty($this->info['token_time']) ? $this->info['token_time'] :'';
$time=time();
$expiry=intval($token_time)+3500;   //86400
if($expiry<$time){
    $this->refresh_token(); 
}  
$access_token=!empty($this->info['access_token']) ? $this->info['access_token'] :'';

$header[]='Authorization: Zoho-oauthtoken ' .$access_token; 
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $path );
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
///curl_setopt($ch, CURLOPT_HEADER, 1);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
if($method=="put")
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
else if($method=="post")
curl_setopt($ch, CURLOPT_POST, true );
else if($method=="get")
curl_setopt($ch, CURLOPT_HTTPGET, true );  
else if($method=='delete')    
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
//curl_setopt($ch, CURLOPT_PUT, true );
if( !empty($body))
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$body=curl_exec($ch);

 return json_decode($body,true);
 
$args=array(
  'method' => strtoupper($method),
  'timeout' => $this->timeout,
  'headers' => $header,
 'body' => $body
  );
$response = wp_remote_request( $path , $args); 
$body = wp_remote_retrieve_body($response);


  if(is_wp_error($response)) { 
  $error = $response->get_error_message();
  return array('error'=>$error);
  }else{
 $body=json_decode($body,true);     
  }
  return $body;
}
  
public function get_entry($module,$id){
$arr=$this->post_crm($module.'/'.$id);
 $entry=array();
if(!empty($arr['data'][0]) && is_array($arr['data'][0])){
    $entry=$arr['data'][0];
}
return $entry;     
}
 
}
}
?>