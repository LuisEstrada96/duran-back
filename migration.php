<?php 
 
try {
  $db = new PDO('mysql:host=localhost;dbname=duran', 'root', 'root');
}
catch(PDOException $e) {
    echo $e->getMessage();
}
 $dicc = array('ene'=>'01', "feb"=>"02", "mar"=>"03", "abr"=>"04", "may"=>"05", "jun"=>"06",
				"jul"=> "07", "ago"=>"08", "sep"=>"09", "oct"=>"10", "nov"=>"11", "dic"=>"12",
				'01'=>'01', "02"=>"02", "03"=>"03", "04"=>"04", "05"=>"05", "06"=>"06",
				"07"=> "07", "08"=>"08", "09"=>"09", "10"=>"10", "11"=>"11", "12"=>"12",
				"14"=>"14","16"=>"16","18"=>"18","20"=>"20","22"=>"22","24"=>"24","26"=>"26","28"=>"28",
				"30"=>"30","32"=>"32","34"=>"34","36"=>"36","38"=>"38");
 $linea = 0;
//Abrimos nuestro archivo
$archivo = fopen("products.csv", "r");
//Lo recorremos
while (($datos = fgetcsv($archivo, ",")) == true) 
{
  	$num = count($datos);
	$linea++;
	$aux = explode('-', $datos[0]);
	$datos[0] =  $dicc[$aux[0]].'-'.$dicc[$aux[1]];
	$sql = 'INSERT INTO products (code, category, name, description, images) VALUES ("'.$datos[0].'","'.$datos[1].'","'.$datos[2].'","'.$datos[3].'","'.$datos[4].'")';
	$stmt = $db->prepare($sql);
	$stmt->execute();
	echo $linea.$sql;
	echo "<br><br><br>";
   	
    
}
//Cerramos el archivo
fclose($archivo);


 ?>