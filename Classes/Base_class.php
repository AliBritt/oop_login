<?php //class model.php
	abstract class model{
		
		//PDO STUFF 
		
		// create $vars to be used in METHOD1
		
		//METHOD1 -QUERY
			//IDEA 1 -FAILED
			// PDO equalivent of query that can be take different sql commands($vars?)to create new user(insert WHERE)
			//, login(select WHERE),change pass(update WHERE) etc(are there more?) AND should take user info (email
			//username etc)as $vars(Depending on how PDO works).
			//note - $vars may have to be arrays
			//note - will have to be PDO equilivent prepared statement
			//FUCK DOES THIS WORK? dont think so
		// do prepared statement in PDO style - look this up NOW.
		// leaves actual queries to METHODS below
		
		//METHOD 2 - REGISTER
		//calls METHOD1
		// sets query to (PDO equilevent of..) INSERT into dbname(column names)VALUES(user info fields)
		
		//METHOD3 - login
		//similar to above
		
		//METHOD4 - change pass
		//similar to above
		
		//METHOD5 - lost pass
		//similar to above
	}
?>