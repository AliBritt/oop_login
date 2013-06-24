<?php 		//user.php
	//script deals with user login, registration, account activation, change password, forgot passwod, logout

	include_once'Connection.php';
	require('includes/password.php');

	class User{
		private $db				= "";
		
		private $user_id		= "";
		private $email			= "";
		private $pass			= "";
		private $pass_hash		= "";
		private $pass_confirm 	= "";
		private $first_name		= "";
		private $last_name		= "";
		private $active			= "";
		private $registration_date;
		
		private $result			= "";//remove this
		private $logged_in		= FALSE;//true or false. may not need this
		
		public $messages; //make array?
		
		public function __construct(){
			$this->db = new Connection();
			$this->db = $this->db->dbConnect();
		}
		
		public function Login(){
				
			$this->email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
			//Sanitize-magic quotes removes ', ", \,NULL maybe use FILTER_SANITIZE_MAGIC_QUOTES?
			$this->pass  = filter_var($_POST['pass'],FILTER_SANITIZE_MAGIC_QUOTES);
			
			if(!empty($this->email) && !empty($this->pass)){
				$st = $this->db->prepare("select pass,user_id,first_name from logoninfo where email=? ");
				$st->bindParam(1, $this->email);
				$st->execute();
				$this->result = $st->fetch();
				
				if($st->rowCount() == 1){
					
					if(password_verify($this->pass, $this->result['pass'])){
						//set session data
						session_start();
						$_SESSION['first_name'] = $this->result['first_name'];
						$_SESSION['user_id']=$this->result['user_id'];
						
						header('Location:' . BASE_URL . 'oop_home.php');
					}
					else{
						$this->messages .= "Incorrect email or password";
					}
				}
				else{
					$this->messages .= "Incorrect email or password";
				}
			}
			else{
				$this->messages .= "Please enter your email address and password";
			}
		echo $this->messages;
		}
		
		public function Register(){
			//sanitize
			$this->first_name	= filter_var($_POST['first_name'],FILTER_SANITIZE_MAGIC_QUOTES);
			$this->last_name	= filter_var($_POST['last_name'],FILTER_SANITIZE_MAGIC_QUOTES);
			$this->email 		= filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
			$this->pass			= filter_var($_POST['pass'],FILTER_SANITIZE_MAGIC_QUOTES);
			$this->pass_confirm = filter_var($_POST['pass_confirm'],FILTER_SANITIZE_MAGIC_QUOTES);
			
			//!! check passwods match
			
			//check if email is taken//may remove this validation after jquery
			if(!empty($this->first_name) && !empty($this->last_name) && !empty($this->email) && !empty($this->pass) && !empty($this->pass_confirm)){
				
				$st = $this->db->prepare("SELECT user_id FROM logoninfo WHERE email = ?");
				$st->bindParam(1, $this->email);
				$st->execute();
				//if statement false then email is not taken so add the user
				if ($st->rowCount() == 0){
					//hash pass
					$this->pass_hash = password_hash($this->pass, PASSWORD_BCRYPT);
					//create activation code
					$this->active = password_hash(uniqid(rand(), true), PASSWORD_BCRYPT);
					
					$st = $this->db->prepare("INSERT INTO logoninfo (email, pass, first_name, last_name, active, registration_date)VALUES (? , ? , ? , ? , ? , NOW())");
					$st->bindParam(1,$this->email);
					$st->bindParam(2,$this->pass_hash);
					$st->bindParam(3,$this->first_name);
					$st->bindParam(4,$this->last_name);
					$st->bindParam(5,$this->active);
					 
					$st->execute();
					
					if($st->rowCount() == 1){
					 	//!!send email
					 	$body .=  'http://localhost/oop_login/' . 'oop_activate.php?x=' . urlencode($this->email) . "&y=$this->active" ;
					 	$this->messages .= "<p>Thanks for registering. A conformation email has been sent to the address you provided. <br><br>\n\n
						Please follow the link to activate your account </p>" ;
						//don't have email set up so add email to messages
						$this->messages .= "<br>\n\n EMAIL: " . $body;
					 }
					 else{
					 	//couldn't insert to db
					 	$this->messages .= "please try again";
					 }
				}
				else{
					//selected an email with query - returned 1
					$this->messages .= "that email is already taken";
				}
			}
			else{
				//user vars were false
				$this->messages .= "Please complete all fields";
			}
			
		echo $this->messages;
		
		} //end of register function

		
		
		public function Activate(){
			
			$this->email 	= filter_var($_GET['x'],FILTER_SANITIZE_EMAIL);
			$this->activate = filter_var($_GET['y'],FILTER_SANITIZE_MAGIC_QUOTES);
	
			if(strlen($this->activate) == 60){
				
				$st = $this->db->prepare('UPDATE logoninfo SET active = NULL WHERE email = ? AND active = ? LIMIT 1');
				$st->bindParam(1,$this->email);
				$st->bindParam(2,$this->activate);
				
				$st->execute();
				
				if($st->rowCount() == 1){
					$this->messages .= "Your account has been activated. You may now login.";
				}
				else{
					$this->messages .= "Your account could not be activated. Please re-check the link or contact the administrator";
				}
			}
			else{
				//redirect
				//$url = BASE_URL . 'oop_register.php';
				header('Location:' . BASE_URL . 'oop_register.php');
			}
		echo $this->messages;
		}
		
		public function Forgot_pass(){
			
			$this->user_id = FALSE ;
			$this->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			
			if (filter_var($this->email , FILTER_VALIDATE_EMAIL)){//this will have to change after i've sorted validation with jquery
				//check for record of email
				$st = $this->db->prepare('SELECT user_id FROM logoninfo WHERE email = ?');
				$st->bindParam(1, $this->email);
				
				$st->execute();
				
				if($st->rowCount() == 1){
					$this->result = $st->fetch();
					$this->user_id = $this->result['user_id'];
					//change session data?
				}
				else{
					$this->messages .= '<p> The email address provided does not match those on file</p>';
				}
			}
			else{
				$this->messages .= '<p> Please provide a vailid email address.</p>';
			}
		
		
			if ($this->user_id){
				//create a random 10 char password
				$this->pass = substr(password_hash(uniqid(rand(), true), PASSWORD_BCRYPT), 10, 15);
				//pass hash the random password
				$this->pass_hash = password_hash($this->pass, PASSWORD_BCRYPT);//create pri var $pass_hash?
				// update db with new pass
				$st = $this->db->prepare("UPDATE logoninfo SET pass = ? WHERE user_id = ? LIMIT 1");
				$st->bindParam(1, $this->pass_hash);
				$st->bindParam(2, $this->user_id);
				
				$st->execute();
				
				if($st->rowCount()== 1){
					//send email
					$body = "Your password has been temporarily changed to $this->pass. 
					Please log in using this password and this email. You can then change your password to something more familiar.";
					
					//mail($this->email, 'Your temporary password', $body, 'From:Admin@Whatever.com'); 
					$this->messages .= '<p>Your password has been changed. You will recieve the new temporary password at the email address which you registered. 
					Once you have logged in you may change you password by clicking the "change password" link.</p>';
					//no mail!
					$this->messages .= $body;
				}
				else{
					$this->messages .= '<p>Your password could not be changed due too a system error. Please try again</p>';
				}
			
			}
			else{
				$this->messages .= 'Please try again';
			}
		echo $this->messages;
		}

		public function Change_pass(){
			//get session data
			session_start();
			$this->user_id = $_SESSION['user_id'];
			//sanitize POST data
			$this->pass = filter_var($_POST['pass'], FILTER_SANITIZE_MAGIC_QUOTES);
			$this->pass_confirm = filter_var($_POST['pass_confirm'], FILTER_SANITIZE_MAGIC_QUOTES);
			
			//check passwords match and execute query
			if ($this->pass == $this->pass_confirm){
				//encription
				$this->pass_hash = password_hash($this->pass , PASSWORD_BCRYPT);
				
				$st = $this->db->prepare("UPDATE logoninfo SET pass = ? WHERE user_id = ? LIMIT 1");
				$st->bindParam(1, $this->pass_hash);
				$st->bindParam(2, $this->user_id);
				
				$st->execute();
				
				if($st->rowCount()== 1){
					$this->messages .= "<p>Your password has been changed</p>";
				}
				else{
					$this->messages .="<p>Your password could not be changed. Make sure the password is different from your current pasword. Please try again</p>";
				}
			}
			else{
				$this->messages .= "<p>The passwords you've entered do not match.</p>";
			}

			echo $this->messages;
		}
	

		public function Logout(){
			//access the session
			session_start();
			//if no session is present redirect the user
			if(!isset($_SESSION['user_id'])){
			
				header('Location:' . BASE_URL . 'oop_login.php');
			}
			else{
				//cancel session
				$_SESSION = array(); //setting SESSION to empty array resets SESSION
				session_destroy(); // removes data from server.does not unset global variables
				setcookie('PHPSESSID', '', time()-3600,'/','', 0, 0);//destroys cookie.
				//PHPSESSID is the session ID parameter passed in a cookie. not sure about the rest of parameters except time???
				$this->messages .= "<h1>Logged out</h1> <p>You are now logged out </p> ";
			}			
			
		echo $this->messages;
		}
	
	}
?>
