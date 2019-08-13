<?php
/*
Controller name: Respond
Controller description: Comment/trackback submission methods
*/

class JSON_API_Respond_Controller {
  
  function submit_comment() {
    global $json_api;
    nocache_headers();
    if (empty($_REQUEST['post_id'])) {
      $json_api->error("No post specified. Include 'post_id' var in your request.");
    } else if (empty($_REQUEST['name']) ||
               empty($_REQUEST['email']) ||
               empty($_REQUEST['content'])) {
      $json_api->error("Please include all required arguments (name, email, content).");
    } else if (!is_email($_REQUEST['email'])) {
      $json_api->error("Please enter a valid email address.");
    }
    $pending = new JSON_API_Comment();
    return $pending->handle_submission();
  }
 
	
	public function register_typeform(){

	global $json_api, $WishListMemberInstance;	  

	$inputJSON = file_get_contents('php://input');
$json_input= json_decode( $inputJSON, TRUE ); //convert JSON into array
$data_array = $json_input;
	
	
//echo '<pre>';
//print_r($data_array['form_response']['definition']['fields']);
//echo '</pre>';
	
//echo '<pre>';
//print_r($data_array['form_response']['answers']);
//echo '</pre>';
		
	
	
	foreach($data_array['form_response']['answers'] as $k){
		
		if($k['field']['id']==56083034) $username= sanitize_user( strtolower(str_replace(' ', '.', $k['text'])));
		elseif($k['field']['id']==56083040) $email= sanitize_email($k['email']);
		
	}
		
	
               				
				while(username_exists($username)){		        
				$i++;
				$username = $username.'.'.$i;			     
	
					}
 
 if ($json_api->query->display_name) $display_name = sanitize_text_field( $json_api->query->display_name );

$user_pass = sanitize_text_field( $_REQUEST['user_pass'] );

//Add usernames we don't want used

$invalid_usernames = array( 'admin' );

//Do username validation

	if ( !validate_username( $username ) || in_array( $username, $invalid_usernames ) ) {

  $json_api->error("Username is invalid.");
  
        }

    elseif ( username_exists( $username ) ) {

    $json_api->error("Username already exists.");

           }			

	else{


	if ( !is_email( $email ) ) {
   	 $json_api->error("E-mail address is invalid.");
             }
    elseif (email_exists($email)) {

	 $json_api->error("E-mail address is already in use.");

          }			

else {

	//Everything has been validated, proceed with creating the user

//Create the user

if( !isset($_REQUEST['user_pass']) ) {
	 $user_pass = wp_generate_password();
	 $_REQUEST['user_pass'] = $user_pass;
}

 $_REQUEST['user_login'] = $username;
 $_REQUEST['user_email'] = $email;

$allowed_params = array('user_login', 'user_email', 'user_pass', 'display_name', 'user_nicename', 'user_url', 'nickname', 'first_name',
                         'last_name', 'description', 'rich_editing', 'user_registered', 'jabber', 'aim', 'yim',
						 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front'
                   );


foreach($_REQUEST as $field => $value){
		
	if( in_array($field, $allowed_params) ) $user[$field] = trim(sanitize_text_field($value));
	
    }


//$user_id = wp_insert_user( $user );
$user_id = 	register_new_user( $username, $email );

/*
if( isset($_REQUEST['user_pass']) && $_REQUEST['notify']=='no') {
	$notify = '';	
  }elseif($_REQUEST['notify']!='no') $notify = $_REQUEST['notify'];

*/
//if($user_id) wp_new_user_notification( $user_id, '',$notify );  

			}
		} 

if($user_id && $reference)  update_user_meta(  $user_id, 'reference', $reference);


	$expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user_id, true);

	$cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');

 return array( 
          "cookie" => $cookie,	
		  "user_id" => $user_id	
		  ); 		  

  } 	
	
}