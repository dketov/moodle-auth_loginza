<?php
/**
 * @author Dmitry Ketov <dketov@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Loginza Authentication
 * see http://loginza.ru for protocol and API details
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->libdir.'/filelib.php');

class auth_plugin_loginza extends auth_plugin_base {

  function auth_plugin_loginza() {
    $this->authtype = 'loginza';
    $this->config = get_config('auth/loginza');
  }

  function config_form($config, $err, $user_fields) {
    include('config.html');
  }

  function process_config($config) {
    set_config('id', trim($config->id), 'auth/loginza');
    set_config('skey', trim($config->skey), 'auth/loginza');

    return true;
  }

  function loginpage_hook() {
    global $frm, $SESSION, $CFG;

    $token = $_POST['token'];

    if($token) {
      $url = new moodle_url('http://loginza.ru/api/authinfo', array('token' => $token));

      if(!empty($this->config->id)) {
        $url->param('id', $this->config->id);
      }

      if(!empty($this->config->skey)) {
        $url->param('sig', md5($token . $this->config->skey));
      }

      $r = download_file_content($url->out(false));
      $d = json_decode($r);

      if(!$d->error_type) {
        $frm->username = $this->username($d); // fake username
        $frm->password = $this->password($d); // fake password
	$SESSION->auth_plugin_loginza = $d;
      }
    }

    $CFG->nolastloggedin = true;
  }

  function username($loginzauser)
  {
    return 'loginza-user-' . md5($loginzauser->identity);
  }

  function password($loginzauser)
  {
    return md5($loginzauser->provider);
  }

  function is_internal() {
    return false;
  }

  function user_login($username, $password) {
    global $SESSION;

    $d = $SESSION->auth_plugin_loginza;

    return ($username === $this->username($d)) and ($password === $this->password($d));
  }

  function get_userinfo($username) {
    global $SESSION;

    $d = $SESSION->auth_plugin_loginza;

    return array(
      'firstname' => $d->name->first_name,
      'lastname' => $d->name->last_name,
      'country' => $d->address->home->country,
      'lang' => $d->language,
      'email' => $d->email,
      'url' => $d->web->default,
      //'picture' => $d->photo, // FIXME: photo is URL
      //'?' => $d->dob, // date of birthday
      //'?' => $d->gender,
    );

    unset($SESSION->auth_plugin_loginza);
  }

  function can_change_password() {
    return false;
  }

  function loginpage_idp_list($wantsurl) {
    $idps = array();

    $idps[] = array(
                'url'  => new moodle_url('https://loginza.ru/api/widget',
                                          array('token_url' => 'http://dl.spbstu.ru/login/index.php')),
                'icon' => new pix_icon('sign_in_button_gray', 
                                       get_string('auth_loginzabutton', 'auth_loginza'), 'auth_loginza'),
                'name' => ''
    );

    return $idps;
  }
}
