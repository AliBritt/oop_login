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
			
			$this->email = $_POST['email'];
			$this->pass  = $_POST['pass'];
			
			if(!empty($_POST['email']) && !empty($_POST['pass'])){
				//validation(!how should this validation match work together with jquery validation?!)
				if(filter_var($this->email, FILTER_VALIDATE_EMAIL)){
					//(pass can only contain only letters,full-stop, apostrophe, space, dash. 4-20 chars long)
					if(preg_match('/^[a-z \'.-]{4,20}$/i', $this->pass)){
						$this->email = filter_var($this->email,FILTER_SANITIZE_EMAIL);
						//Sanitize-magic quotes removes ', ", \,NULL maybe use FILTER_SANITIZE_STRING?
						$this->pass  = filter_var($this->pass,FILTER_SANITIZE_MAGIC_QUOTES);
				
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
						$this->messages .= "passwords should contain only letters,full-stop, apostrophe, space and dash.";
					}
				}
				else{
					$this->messages .= "Please enter a valid email address";
				}
			}
			else{
				$this->messages .= "Please enter your email address and password";
			}
		echo "<label class='form-signin'>$this->messages</label>";
		}
		
		public function Register(){
			
			$this->first_name	= $_POST['first_name'];
			$this->last_name	= $_POST['last_name'];
			$this->email 		= $_POST['email'];
			$this->pass			= $_POST['pass'];
			$this->pass_confirm = $_POST['pass_confirm'];
			
			if(!empty($this->first_name) && !empty($this->last_name) && !empty($this->email) && !empty($this->pass) && !empty($this->pass_confirm)){
				//validation:
				//check first name for chars(only letters,full-stop, apostrophe, space, dash. 2-20 chars long)
				if(preg_match('/^[a-z \'.-]{2,20}$/i', $this->first_name)){
					
					//check last name for chars
					if(preg_match('/^[a-z \'.-]{2,40}$/i', $this->last_name)){
						
						//email
						if(filter_var($this->email, FILTER_VALIDATE_EMAIL)){
							
							//password(change login to use this too?)
							if(preg_match('/^\w{4,20}$/', $this->pass)){
								
								//pass confirm
								if($this->pass == $this->pass_confirm){
									
									//all inputs validated
								}
								else{
									$this->messages .="Your passwords do not match";
								}
							}
							else{
								$this->messages .="Please enter a valid password";
							}
							
						}
						else{
							$this->messages .="Please enter a valid email address";
						}
					}
					else{
						$this->messages .="Please enter your last name!";
					}
				}
				else{
					$this->messages .= "Please enter your first name!";
				}
				
				if(!isset($this->messages)){
					//sanitise? PDO??
					$this->first_name	= filter_var($this->first_name, FILTER_SANITIZE_MAGIC_QUOTES);
					$this->last_name	= filter_var($this->last_name, FILTER_SANITIZE_MAGIC_QUOTES);
					$this->email 		= filter_var($this->email, FILTER_SANITIZE_EMAIL);
					$this->pass			= filter_var($this->pass, FILTER_SANITIZE_MAGIC_QUOTES);
					$this->pass_confirm = filter_var($this->pass_confirm, FILTER_SANITIZE_MAGIC_QUOTES);
			
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
						 	$this->messages .= "Thanks for registering. A conformation email has been sent to the address you provided. <br><br>\n\n
							Please follow the link to activate your account " ;
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
				}//end of !isset($this->messages) conditional
				
			}
			else{
				//user vars were false
				$this->messages .= "Please complete all fields";
			}
			
		echo "<label class='form-signin'>$this->messages</label>";
		
		} //end of register function

		
		
		public function Activate(){
			
			$this->email 	= $_GET['x'];
			$this->activate = $_GET['y'];
	
			if(filter_var($this->email, FILTER_VALIDATE_EMAIL) && strlen($this->activate) == 60){
				
				$this->email 	= filter_var($this->email, FILTER_SANITIZE_EMAIL);
				$this->activate = filter_var($this->activate, FILTER_SANITIZE_MAGIC_QUOTES);
				
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
		echo "<label class='form-signin'>$this->messages</label>";
		}
		
		public function Forgot_pass(){
			
			$this->user_id = FALSE ;
			$this->email = $_POST['email'];
			
			if (filter_var($this->email , FILTER_VALIDATE_EMAIL)){
				$this->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
				//check for record of email
				$st = $this->db->prepare('SELECT user_id FROM logoninfo WHERE email = ?');
				$st->bindParam(1, $this->email);
				
				$st->execute();
				
				if($st->rowCount() == 1){
					$this->result = $st->fetch();
					$this->user_id = $this->result['user_id'];
					//change session data?
					if ($this->user_id){
						//create a random 10 char password
						$this->pass = substr(password_hash(uniqid(rand(), true), PASSWORD_BCRYPT), 10, 15);
						//pass hash the random password
						$this->pass_hash = password_hash($this->pass, PASSWORD_BCRYPT);
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
							$this->messages .= 'Your password has been changed. You will recieve the new temporary password at the email address which you registered. 
							Once you have logged in you may change you password by clicking the "change password" link.';
							//no mail!
							$this->messages .= $body;
						}
						else{
							$this->messages .= 'Your password could not be changed due too a system error. Please try again';
						}
					
					}
					else{
						$this->messages .= 'Please try again';
					}
				}
				else{
					$this->messages .= 'The email address provided does not match those on file';
				}
			}
			else{
				$this->messages .= 'Please provide a vailid email address.';
			}
			
		echo "<label class='form-signin'>$this->messages</label>";
		}

		public function Change_pass(){
			
			$this->pass = $_POST['pass'];
			$this->pass_confirm = $_POST['pass_confirm'];
			
			//get session data
			session_start();
			$this->user_id = $_SESSION['user_id'];
			
			//VALIDATE password
			if(preg_match('/^[a-z \'.-]{4,20}$/i', $this->pass)){
				
				//check passwords match and execute query
				if ($this->pass == $this->pass_confirm){
					
					$this->pass = filter_var($this->pass, FILTER_SANITIZE_MAGIC_QUOTES);
					$this->pass_confirm = filter_var($this->pass_confirm, FILTER_SANITIZE_MAGIC_QUOTES);
					//encription
					$this->pass_hash = password_hash($this->pass , PASSWORD_BCRYPT);
					
					$st = $this->db->prepare("UPDATE logoninfo SET pass = ? WHERE user_id = ? LIMIT 1");
					$st->bindParam(1, $this->pass_hash);
					$st->bindParam(2, $this->user_id);
					
					$st->execute();
					
					if($st->rowCount()== 1){
						$this->messages .= "Your password has been changed";
					}
					else{
						$this->messages .="Your password could not be changed. Make sure the password is different from your current pasword. Please try again";
					}
				}
				else{
					$this->messages .= "The passwords you've entered do not match.";
				}
			}
			else{
				$this->messages .="passwords should contain only letters,full-stop, apostrophe, space and dash.";
			}

			echo "<label class='form-signin'>$this->messages</label>";
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
				$this->messages .= "You are now logged out";
			}			
			
		echo "<label class='form-signin'>$this->messages</label>";
		}
	
	}
?>
