<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {header("Location:../error.php");}else{
$yes = "";
$tr= "";
$vip="";
$admin_menu= "";
$loggedwith = "";
if(isset($_SESSION['dt_username']) && isset($_SESSION['dt_password'])){
	$check_top = mssql_fetch_array(mssql_query("Select MAX(login) as login from [DTweb_Login_Logs] where account = '".$_SESSION['dt_username']."'"));
	$select = mssql_fetch_array(mssql_query("Select logout from [DTweb_Login_Logs] where account = '".$_SESSION['dt_username']."' and login='".$check_top['login']."'"));
	if(($check_top['login'] == null) || ($select['logout'] != 0)){
	mssql_query("Insert into [DTweb_Login_Logs] (account,login,logout,ip) VALUES ('".$_SESSION['dt_username']."','".time()."','0','".ip()."')");
	}
	$check_admin_sessions = mssql_query("Select * from DTweb_GM_Accounts");
	for($i=0;$i < mssql_num_rows($check_admin_sessions); $i++){
		$admins = mssql_fetch_array($check_admin_sessions);
		if(isset($_SESSION['admin_user']) && isset($_SESSION['admin_ip'])){
		if(($_SESSION['admin_user'] == $admins['name']) && ($_SESSION['admin_ip'] == $admins['ip'] )){
			$selecta = mssql_fetch_array(mssql_query("Select * from DTweb_GM_Accounts where name='".$_SESSION['admin_user']."' and ip = '".$_SESSION['admin_ip']."'"));
		    $yes = "<table class='form'><form name=".form_enc()." method='post'><input class='button' style='width:80px;height:20px;font-size:9pt;' type='submit' name='adminsback' value='".phrase_switch_back."'></form></table>";
		    $loggedwith = "<span style='font-size:12pt;'>".phrase_you_have_switched."</span> ";
		}
	  }
	}
	
	$is_admin = check_admin($_SESSION['dt_username']);
	if($is_admin != false){
		if(isset($_GET['p'])){
			switch($_GET['p']){
			case "characters": case "resetcharacter": case  "addstats" : case "pkclear" : case  "resetstats" : case  "grandreset":
			$char= "in"; $adm=""; $acc = "";$vip="";break;
			case "bank" : case  "accdetails" : case  "auction" : case  "storage" : case  "warehouse" :case  "lotto" : case  "buyjewels" : case  "market" : case  "votes": case "jewels":
			$char= ""; $adm=""; $acc = "in";$vip="";break;
			case "addbox": case "addnews" : case "general" : case  "bans" : case  "warnings" : case  "accountedit" : case  "logs": case  "auctioned":
			$char= ""; $acc = ""; $adm="in";$vip=""; break;
        	case "buycredits" : case "buyvip" : case "changeclass" : case "itemrafinery" : case "randomitem" : case "itemupgr" : case "changecharname" :
			$char = ""; $acc =""; $vip = "in"; $adm=""; break;		
			default: $char= ""; $acc = ""; $adm="";$vip=""; break;
		  }
		}
		else{
			$char= ""; $acc = ""; $adm=""; $vip="";
		}
		  if($is_admin[1] <> 666){
			  $tr = '<li><a onclick="window.location.href=\'?p=addbox\'">'.phrase_add_box.'</a></li>';
		  }
            $admin_menu = '
			<div class="panel panel-default nav ">
               <div class="panel-heading tdTitle">
                 <h4 class="panel-title">
                   <a data-toggle="collapse" class="accordion-toggle collapsed " data-parent="#accordion" href="#collapse3">
                   '.phrase_admin_settigns.'</a>
                 </h4>
               </div>
               <div id="collapse3" class="panel-collapse collapse '.$adm.'">
                 <div class="panel-body">
	             <ul class="style4 side_menu afix" >				    
				    <li><a onclick="window.location.href=\'?p=general\'">'.phrase_general_config.'</a></li>
                   	<li><a onclick="window.location.href=\'?p=addnews\'">'.phrase_add_news.'</a></li>
                   	<li><a onclick="window.location.href=\'?p=bans\'">'.phrase_add_ban.'/'.phrase_warning.'</a></li>
                   	<li><a onclick="window.location.href=\'?p=accountedit\'">'.phrase_account_edit.'</a></li>
					'.$tr.'
                   	<li><a onclick="window.location.href=\'?p=logs\'">'.phrase_logs.'</a></li>
                   </ul>   
	             </div>
               </div>
             </div>';
	}
	else{
		if(isset($_GET['p'])){
		    switch($_GET['p']){
		    	case "characters": case "resetcharacter": case  "addstats" : case "pkclear" : case  "resetstats" : case  "grandreset":
		    	$char= "in"; $acc = ""; $vip = "";break;
		    	case "bank" : case  "accdetails" :  case  "storage" : case  "warehouse" : case  "logs" : case  "lotto" : case  "buyjewels" : case  "auction" : case  "market" : case  "votes": case "jewels":
		    	$char= ""; $acc = "in";$vip = ""; break;
				case "buycredits" : case "buyvip" : case "changeclass" : case "itemrafinery" : case "randomitem" : case "itemupgr" : case "changecharname" :
				$char = ""; $acc =""; $vip = "in"; break;
		    	default: $char= ""; $acc = ""; $vip = "";break;
		    }
		}
		else{
			$char= ""; $acc = "";
		}
	}
	if(isset($_POST['logout'])){
		mssql_query("Update [DTweb_Login_Logs] set logout='".time()."' where login = '".$check_top['login']."' and account = '".$_SESSION['dt_username']."'");
		unset($_SESSION['dt_username']);
	    unset($_SESSION['dt_password']);
	    session_destroy();
		home();
	}
	if(isset($_POST['adminsback'])){
		$_SESSION['dt_username'] = $_SESSION['admin_user'];
		unset($_SESSION['admin_user']);
		unset($_SESSION['admin_ip']);
		header("Location:".$_SESSION['location']."");
		unset($_SESSION['location']);
	}
	if(isset($_SESSION['dt_username'])){
		
		$usersa =$_SESSION['dt_username'];
	}
	else{
		$usersa = "";
	}
 echo '
<div class="panel-group nav" id="accordion">   
<span class="welcome">'.phrase_welcome.'</span><span class="welcome_user">'.$yes.$loggedwith.$usersa.'</span> 
'.$admin_menu.'   
<div class="panel panel-default nav">
    <div class="panel-heading tdTitle">
      <h4 class="panel-title">
        <a data-toggle="collapse" class="accordion-toggle collapsed " data-parent="#accordion" href="#collapse1">
       '.phrase_account_settings.'</a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse '.$acc.'">
      <div class="panel-body">
	  <ul class=" style4 side_menu afix" >
        	<li><a onclick="window.location.href=\'?p=accdetails\'">'.phrase_account_details.'</a></li>
			<li><a onclick="window.location.href=\'?p=lotto\'">'.phrase_lotto.'</a></li>
        	<li><a onclick="window.location.href=\'?p=bank\'">'.phrase_zen_bank.'</a></li>
			<li><a onclick="window.location.href=\'?p=buyjewels\'">'.phrase_buy_jewels.'</a></li>
			<li><a onclick="window.location.href=\'?p=jewels\'">'.phrase_jewel_bank.'</a></li>
			<li><a onclick="window.location.href=\'?p=warehouse\'">'.phrase_warehouse.'</a></li>			
        	<li><a onclick="window.location.href=\'?p=auction\'">'.phrase_auction.'</a></li>
        	<li><a onclick="window.location.href=\'?p=market\'">'.phrase_item_market.'</a></li>
			<li><a onclick="window.location.href=\'?p=storage\'">'.phrase_personal_storage.'</a></li>	
        	<li><a onclick="window.location.href=\'?p=votes\'">'.phrase_vote.'</a></li>
        </ul>   
	  </div>
    </div>
  </div>
<div class="panel panel-default nav">
    <div class="panel-heading tdTitle">
      <h4 class="panel-title">
        <a data-toggle="collapse" class="accordion-toggle collapsed" data-parent="#accordion " href="#collapse2">
        '.phrase_character_settings.'</a>
      </h4>
    </div>
    <div id="collapse2" class="panel-collapse collapse '.$char.'">
      <div class="panel-body">
	  <ul class=" style4 side_menu afix" >
        	<li><a onclick="window.location.href=\'?p=characters\'">'.phrase_characters.'</a></li>
        	<li><a onclick="window.location.href=\'?p=resetcharacter\'">'.phrase_reset_character.'</a></li>
        	<li><a onclick="window.location.href=\'?p=addstats\'">'.phrase_add_stats.'</a></li>
        	<li><a onclick="window.location.href=\'?p=pkclear\'">'.phrase_pk_clear.'</a></li>
        	<li><a onclick="window.location.href=\'?p=resetstats\'">'.phrase_reset_stats.'</a></li>
        	<li><a onclick="window.location.href=\'?p=grandreset\'">'.phrase_grand_reset.'</a></li>
         </ul>   
	    </div>
       </div>
      </div>
	  
	  
  <div class="panel panel-default nav">
    <div class="panel-heading tdTitle">
      <h4 class="panel-title">
        <a data-toggle="collapse" class="accordion-toggle collapsed" data-parent="#accordion " href="#collapse4"><img width="30px;" src="imgs/new1.gif"/>
        '.phrase_vip_modules.' </a>
      </h4>
    </div>
    <div id="collapse4" class="panel-collapse collapse '.$vip.'">
      <div class="panel-body">
	  <ul class=" style4 side_menu afix" >
        	<li><a onclick="window.location.href=\'?p=buycredits\'">'.phrase_buy_credits.'</a></li>
			<li><a onclick="window.location.href=\'?p=buyvip\'">'.phrase_buy_vip.'</a></li>
        	<li><a onclick="window.location.href=\'?p=changeclass\'">'.phrase_change_class.'</a></li>
        	<li><a onclick="window.location.href=\'?p=changecharname\'">'.phrase_change_char_name.'</a></li>
        	<li><a onclick="window.location.href=\'?p=itemupgr\'">'.phrase_item_upgrade.'</a></li>
        	<li><a onclick="window.location.href=\'?p=randomitem\'">'.phrase_random_item.'</a></li>
        	<li><a onclick="window.location.href=\'?p=itemrafinery\'">'.phrase_item_rafinery.'</a></li>
			<li><a onclick="window.location.href=\'?p=dualinv\'">'.phrase_dual_inv.'</a></li>
			<li><a onclick="window.location.href=\'?p=dualstats\'">'.phrase_dual_stats.'</a></li>
         </ul>   
	    </div>
       </div>
      </div>
    <form name="'.form_enc().'" method="post">
       <input type="submit" class="button border" value="'.phrase_logout.'" name="logout"/>
    </form>    
</div>';	
	}
else { 
	echo '
<form name="'.form_enc().'" method="post">
	<ul class="form" style="width:80%;">
		<li>
			<label for="acc">'.phrase_account.': </label>
			<input id="acc" name="account" type="text" maxlength="10" />
		</li>
		<li>
			<label for="pass">'.phrase_password.': </label>
			<input name="password" type="password" maxlength="10" />
		</li>
			
		<li class="buttons">
			<input  name="login" type="submit" value="'.phrase_login.'" />
			<input type="button" onclick="window.location.href=\'?p=register\'" value="'.phrase_sign_up.'" />
		</li>
	</ul>
</form>';
	}
	if(isset($_POST['login'])){
		do_login();
	}
}
?>