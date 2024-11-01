<?PHP
/*
Plugin Name: Zerby Login Widget
Plugin URI: http://www.zerbynet.fr/wordpress/plugins/plugin-widget-de-connexion-a-ladmin-wordpress-7
Description: Authentification Widget
Version: 0.4
Author: Emmanuel Grognet
Author URI: http://www.zerbynet.fr
*/
/*  
    Copyright 2009  Grognet Emmanuel (email : manu@zerbynet.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
?>
<?PHP
// Initialisation
function Zerby_init_widget () {
if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) return;
    load_plugin_textdomain('Zerby_Login_Widget', 'wp-content/plugins/zerby-login-widget');
	register_sidebar_widget('Zerby Login Widget', 'Zerby_Login_Show');
	register_widget_control('Zerby Login Widget', 'Zerby_Login_Control');
}

// Contenu du widget
function Zerby_Login_Content () {
//traduction
$ident =        __('Username', 'Zerby_Login_Widget');
$pass  =        __('Password', 'Zerby_Login_Widget');
$remember =     __('Remember Me', 'Zerby_Login_Widget');
$submit =       __('Log In', 'Zerby_Login_Widget');
$pass_forget =  __('Lost your password ?', 'Zerby_Login_Widget');
$go_admin =     __('Site admin', 'Zerby_Login_Widget');
$logout =       __('Log out', 'Zerby_Login_Widget');
$register =		__('Register', 'Zerby_Login_Widget');
$URL=get_bloginfo('url');
$URL_Redirect=get_permalink();

if (!is_user_logged_in()) {
    if(get_option("users_can_register")=="1") {
        $registerlink=<<<FOZ
<p id="nav">
<a href="$URL/wp-login.php?action=register" title="$register">$register</a>
</p>
FOZ;
    }
$content=<<<FOZ
<form name="loginform" id="loginform" action="$URL/wp-login.php" method="post">
	<p>
   		<label>$ident<br />
		<input type="text" name="log" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label>$pass<br />
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
	</p>
	<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" />$remember</label></p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" value="$submit" tabindex="100" />
		<input type="hidden" name="redirect_to" value="$URL_Redirect" />
		<input type="hidden" name="testcookie" value="1" />
	</p>
</form>
<p id="nav">
<a href="$URL/wp-login.php?action=lostpassword" title="$pass_forget">$pass_forget</a>
</p>
$registerlink
FOZ;
}
else {
$loginout=wp_logout_url($URL_Redirect);
if (function_exists(sf_pm_tag)){
    $tabinbox=sf_pm_tag(false, true);
    if ($tabinbox['count']>=0){
        $inbox="<p><a href=\"{$tabinbox['url']}\">{$tabinbox['count']} message";
        if ($tabinbox['count']>1)$inbox.="s";
        $inbox.="</a></p>";
    }
}
$content=<<<FOZ
$inbox
<p><a href="$URL/wp-admin" title="$go_admin">$go_admin</a></p>
<p><a href="$loginout" title="$logout">$logout</a></p>
FOZ;
}
return $content;
}
// Affichage
function Zerby_Login_Show ($args) {
	extract($args);
	$options = get_option("Zerby_Login_Widget");
	if (!is_array( $options )) {
		$options = array('title' => 'Zerbynet Login'); 
	}
	
	//affichage.
	echo $before_widget;
	echo $before_title;
	echo $options['title'];
	echo $after_title;
	echo Zerby_Login_Content();
	echo $after_widget;
}

// administration
function Zerby_Login_Control () {
//traduction
$titre =        __('Title', 'Zerby_Login_Widget');

// Recup options
$options = get_option("Zerby_Login_Widget");

if (!is_array( $options )) {
	$options = array('title' => $titre);
	}
//Mise a jour des options
if ($_POST['Zerby-Login-Widget-Submit']) {
	$options['title'] = htmlspecialchars($_POST['Zerby-Login-WidgetTitle']);
	update_option("Zerby_Login_Widget", $options);
	}
//options
$form=<<<FOZ
<p>
    <label for="Zerby-Login-WidgetTitle">$titre</label>
    <input type="text" id="Zerby-Login-WidgetTitle" name="Zerby-Login-WidgetTitle" value="{$options['title']}" /><br/>
    <input type="hidden" id="Zerby-Login-Widget-Submit" name="Zerby-Login-Widget-Submit" value="1" />
</p>
FOZ;
echo $form;
}

//Affichage hors Widget
function Zerby_Show_in_Page () {
echo Zerby_Login_Content();
}

// GO !
add_action("plugins_loaded", "Zerby_init_widget");
add_shortcode("Show-ZerbyLogin", "Zerby_Show_in_Page");

?>
