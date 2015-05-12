<?php
//JOOMLA DB SETTINGS
define("JOOMLA_DB_HOSTNAME", "");//Joomla's DB Host
define("JOOMLA_DB_NAME", "");//Joomla's DB Name
define("JOOMLA_DB_USERNAME", "");//Joomla's DB Username
define("JOOMLA_DB_PASSWORD", "");//Joomla's DB Password
define("JOOMLA_DB_PREFIX", ""); //Joomla database prefix - INCLUDE UNDERSCORE

define("JOOMLA_LOGOUT_URL", "http://kfnwebsolutions.com/SandBox/Geek/index.php?option=com_users&task=logout");
define("JOOMLA_LOGIN_URL", "http://kfnwebsolutions.com/SandBox/Geek/index.php?option=com_users&task=login");

define("JOOMLA_USER_TYPE", 1); //User type that should be given to automatically created users. 1 = admin

class JoomlaSSO extends Base_Controller{

function CheckLogin(){

    //Skip check if user is already logged into IP
    if ($this->session->userdata('user_type') > 0){
    return true;
    }

    //Catch Logout and send to Joomla
    if ($_COOKIE['JA_Logged'] == 1 && !$this->session->userdata('user_type')){
    setcookie('JA_Logged', 0);
    header("Location: " . JOOMLA_LOGOUT_URL);
    exit;
    }


    $Link = mysqli_connect(JOOMLA_DB_HOSTNAME, JOOMLA_DB_USERNAME, JOOMLA_DB_PASSWORD, JOOMLA_DB_NAME);
    if (!$Link){
        die("Unable to connect to Joomla DB");
    }
    $JUsername = "";
    $SQL_GetSessions = "SELECT * FROM `" . JOOMLA_DB_PREFIX . "session`";
    $R_GetSessions = mysqli_query($Link, $SQL_GetSessions);
    while ($row = mysqli_fetch_assoc($R_GetSessions))
    {
        foreach ($_COOKIE as $Key => $C){
            if ($row['session_id'] == $C){
                //found session, valid user login
                $JUsername = $row['username'];
                break 2;
            }
        }
    }

    if ($JUsername == ""){
        header("location: " . JOOMLA_LOGIN_URL);
        exit;
    }

    $SQL_GetUser = "SELECT * FROM `" . JOOMLA_DB_PREFIX . "users` WHERE `username`='$JUsername' LIMIT 1;";
    $R_GetUser = mysqli_query($Link, $SQL_GetUser);
    if (mysqli_num_rows($R_GetUser) != 1){
        die("IMPOSSIBLE ERROR HAS OCCURRED.");
    }
    $JUser = mysqli_fetch_assoc($R_GetUser);
    $this->db->where('user_email', $JUser['email']);
    $query = $this->db->get('ip_users');
    $user = $query->row();
    // Check if the user exists
    if (empty($user)) {
    $this->load->model('users/mdl_users');
        $this->load->library('crypt');
        $password = md5(rand(1,9999)); //password ignored for SSO
        $user_psalt = $this->crypt->salt();
        $user_password = $this->crypt->generate_password($password, $user_psalt);

    $NewID = $this->mdl_users->save(NULL, array('user_id' => '', 'user_type'=>JOOMLA_USER_TYPE, 'user_active'=>1, 'user_name' => $JUser['username'], 'user_email'=>$JUser['email'], 'user_password'=>$user_password, 'user_psalt'=>$user_psalt));
    }else{

        $session_data = array(
            'user_type' => $user->user_type,
            'user_id' => $user->user_id,
            'user_name' => $user->user_name
        );

        $this->session->set_userdata($session_data);
        setcookie("JA_Logged", 1);
        if ($this->session->userdata('user_type') == 1) {
            redirect('dashboard');
        } elseif ($this->session->userdata('user_type') == 2) {
            redirect('guest');
        }
    }
}

}
?>