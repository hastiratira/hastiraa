<?php
echo $action = $_REQUEST['action'];

parse_str($_REQUEST['dataku'], $hasil);  
echo "firstname: ".$hasil['firstName']."<br/>";
echo "lastname: ".$hasil['lastName']."<br/>";
echo "username: ".$hasil['Username']."<br/>";

//$hasil = $_REQUEST;

$fotoku = $_FILES["mypicture"];	

$username = trim($hasil['username']);
if (!empty($fotoku["name"]) and !empty($username)){
	$namafile = $fotoku["name"];		//nama filenya
	preg_match("/([^\.]+$)/", $namafile, $ext);		//Regex: mencari string sesudah titik terakhir, saved in array ext
	$file_ext = strtolower($ext[1]);
	$namafilebaru = $hasil['username'].".".$ext[1];	//nama file barunya [ccnumber].png
    $file = $fotoku["tmp_name"];						//source filenya 
    //perform the upload operation
	$extensions= array("jpeg","jpg","png");				//extensi file yang diijinkan
	//Kirim pesan error jika extensi file yang diunggah tidak termasuk dalam extensions
	$errors = array();
	if(in_array($file_ext,$extensions) === false)
	 $errors[] = "Extensi yang diperbolehkan jpeg atau png.";
	
	//Kirim pesan error jika ukuran file > 500kB
	$file_size = $fotoku['size'];
	if($file_size > 2097152)
	 $errors[] = "Ukuran file harus lebih kecil dari 2MB.";
    
	//Upload file
	if(empty($errors)){
		if(move_uploaded_file($file, "uploads/" . $namafilebaru))
			echo "Uploaded dengan nama $namafilebaru";
	}
}else echo $errors[] = "Lengkapi username dan gambarnya. ";
echo "<br/>";

if(!empty($errors)){
	echo "Error : ";
	foreach ($errors as $val)
		echo $val;
}

/* SQL: select, update, delete */

if($action == 'create')
	$syntaxsql = "insert into tbl_billing values (null, '$hasil[firstName]', '$hasil[lastName]', '$hasil[Username]', '$hasil[Email]', '$hasil[Address]',now(), '$namafilebaru')";
elseif($action == 'update')
	$syntaxsql = "update tbl_billing set first_name = '$hasil[firstName]', last_name = '$hasil[lastName]', foto = '$namafilebaru' where Username = '$hasil[Username]',
	Email = '$hasil[Email]', Address = '$hasil[Address]'";
elseif($action == 'delete')
	$syntaxsql = "delete from tbl_billing where username = '$hasil[username]'";
elseif($action == 'read')
	$syntaxsql = "select * from tbl_billing";
	
//eksekusi syntaxsql 
$conn = new mysqli("localhost","root","","formhastira"); //dbhost, dbuser, dbpass, dbname
if ($conn->connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}else{
  echo "Database connected. ";
}
//create, update, delete query($syntaxsql) -> true false
if ($conn->query($syntaxsql) === TRUE) {
	echo "Query $action with syntax $syntaxsql suceeded !";
}
elseif ($conn->query($syntaxsql) === FALSE){
	echo "Error: $syntaxsql" .$conn->error;
}
//khusus read query($syntaxsql) -> semua associated array
else{
	$result = $conn->query($syntaxsql); //bukan true false tapi data array asossiasi
	if($result->num_rows > 0){
		echo "<table id='tresult' class='table table-striped table-bordered'>";
		echo "<thead><th>First_name</th><th>Last_name</th><th>Username</th><th>Email</th><th>Address</th><th>foto</th></thead>";
		echo "<tbody>";
		while($row = $result->fetch_assoc()) {
			echo "<tr><td>". $row['First_name']."</td><td>". $row['Last_name']."</td><td>". $row['Username']."</td><td>". $row['Email']."</td><td>". $row['Address']."</td><td>". $row['foto']."</td></tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
}
$conn->close();

?>