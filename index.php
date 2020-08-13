<?php 

	//define("BASE_URL", "http://localhost/startup_aprende/");
	define("BASE_URL", "http://aprende.weonmart.com/");

	$dsn     = "mysql:host=localhost; dbname=aprende; charset=utf8";
  $user    = "root";
  $pass    = "";
  $charset = "utf8mb4";


  try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"; 
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

  $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id ASC");
	$stmt->execute();
	$users = $stmt->fetchAll(PDO::FETCH_OBJ);	


	if (isset($_POST["submit"])) {
		$name = trim(stripslashes(htmlspecialchars($_POST["name"])));
		$email = trim(stripslashes(htmlspecialchars($_POST["email"])));
		
		if (empty($name)) {
			$errorName = "Name is required";
		}else if(empty($email)){
			$errorEmail = "Email is required";
		}else{
			if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
			  $errorName = "Only letters and white space allowed";
			}

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	      $errorEmail = "Invalid email format";
	    }

	    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
	    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
	    $stmt->execute(); 
			$user = $stmt->fetch();
		 	if ($user) {
		 		$error ="User already registered";
		 	}else {
		    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES(:name, :email) ");
		    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
		    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
		    if($stmt->execute()){
		    	$message = "user registered successfully!";
		    	header("Location:" . BASE_URL);
		    }else{
		    	$error = "Could not register user";
		    	header("Location:" . BASE_URL);
		    }
    	}
	    
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Aprendre Startup</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<header id="header" class="navbar">
		<a href="/" class="active">Aprendre Startup</a>
	</header>
	<div class="container">

		<h2 class="title">Register as a User</h2>
		<div class="left-side">
			<h3 class="register-title">Add your Name and Email</h3>
			<form action="index.php" method="post">
				<div class="register-form">
					<div class="form-group">
						<label for="fullname"><b>Name</b> </label>
						<input type="text" name="name" value="" placeholder="Enter name" required>
						<span class="error"> <?php if(isset($errorName)) {echo $errorName;} ?></span>
					</div><br>
					<div class="form-group">
						<label for="email"><b>Email</b></label>
						<input type="email" name="email" value="" placeholder="Enter email" required>
						<span class="error"> <?php if(isset($errorEmail)) {echo $errorEmail;} ?></span>
					</div>
					<button type="submit" name="submit">Register</button>
				</div>
			</form>
			<?php if (isset($message)){ echo "<span class='success'>".$message."</span>"; }?>

			<?php if (isset($error)){ echo "<span class='errorM'>".$error."</span>"; }?>
		</div>
		<div class="right-side">
			<h3 class="table-title">User registered</h3>
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($users){
						foreach ($users as $user) { ?>
							<tr>
								<td><?=$user->name ?></td>
								<td><?=$user->email  ?></td>
							</tr>
						<?php } } ?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>