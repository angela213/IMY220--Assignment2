<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Angela Goodhead">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass)
			{
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res))
				{
					$user_id=$row['user_id'];
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>

									<input type='hidden' name='loginEmail' value='".$row['email']."'/>
									<input type='hidden' name='loginPass' value='".$pass."'/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit2' />

								</div>
						  	</form>";

						  	 	//file checking
						  	echo "<h2>Image Gallery</h2>";
						  	if(isset($_POST["submit2"]))//checks if the user pressed submit
							{
							  	$file=$_FILES['picToUpload'];
							  	//print_r($file);
							  	$fileName=$file['name'];//file is an array of the file details
								$fileTName=$file['tmp_name'];
								$fileSize=$file['size'];
								$fileError=$file['error'];
								$fileType=$file['type'];

								$fileExt=explode('.', $fileName);//separating $fileName by the fullstop gets and array
								$fileActualExt=strtolower(end($fileExt));//end() gets last piece of data in an array
								$fileNameName=array_values($fileExt)[0];//geting first piece of array
								$allowed=array('jpg', 'jpeg');//which extensions are allowed

								if(in_array($fileActualExt, $allowed))
								{
										if($fileError===0)
										{
											if($fileSize<=1024)//change to 1MB1000
											{
												$fileNameRecent=$fileNameName.".".$fileActualExt;//putting name back together again
												$fileDestination='gallery/'.$fileName;//saving to destination
												//move from temp location to actual location
												move_uploaded_file($fileTName, $fileDestination);//actually uploading
											    $insertI = "INSERT INTO tbgallery (user_id, fileName) VALUES (".$row['user_id'].", '$fileName')";
												if(mysqli_query($mysqli, $insertI)){
												    echo "image added to tbgallery";
												} else{
												    echo "ERROR in $insertI. " . mysqli_error($mysqli);
												}
											}
											else{
												echo "your file is too big";
											}
										}
										else{
											echo "There was an error uploading the file";
										}
								}
									else{
											echo "you can't upload this type of file";
										}
												
							}//end of if the user pressed submit
							//displaying gallery
									$queryG = "SELECT * FROM tbgallery WHERE user_id= '$user_id'";
									$result = $mysqli->query($queryG);
									if($row = mysqli_fetch_array($result))
									{
									
										$query = "SELECT * FROM tbgallery WHERE user_id = $user_id;"; 
									    $result = mysqli_query($mysqli, $query); 
									    if ($result) 
									    { 
									       $rowNumber = mysqli_num_rows($result); 
										}								
										//echo "<br/> rowCount ".$rowNumber;//why not 3?
										//echo "<br/> all ".print_r(mysqli_fetch_array($resCount));
										//echo "<br/> user Id ".$user_id;
										$img_id=0;									
										
										echo "<div class='row imageGallery'>";
										for($i=1; $i<=$rowNumber; $i++)//repeat for number of images recieved 
										{
											//echo "yay";
											$queryG = "SELECT * FROM tbgallery WHERE user_id= '$user_id' AND image_id > '$img_id'";
											$result = $mysqli->query($queryG);
											if($row = mysqli_fetch_array($result))
											{
												$img_id=$row['image_id'];
												//$index=($i*3)-1;
												$fileName=$row['filename'];
												//echo "filename ".$fileName;

												//echo "<img src='gallery/".$fileName."'/>";
												echo "<div class='col-3' style='background-image: url(gallery/".$fileName.")'></div>";//$fileName already has the extension

												//style='background-image: url(gallery/".$fileName.".jpg)'
												 //<div style="background-image: url('uploads/erica.jpg');height:300px;width:200px;">
											}
										}
										/*if(isset($_POST["submit2"]))
										{
											
										echo "<div class='col-3' style='background-image: url(gallery/".$fileNameRecent.")'></div>";
								
										}*/
									echo " </div>";
									}
									else{
										echo "user Id not in database";
									}
				}//end of successful login
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>