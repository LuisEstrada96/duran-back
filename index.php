<?php

require('includes/class.phpmailer.php');
require('includes/class.smtp.php');
require 'vendor/autoload.php';

$app = new \Slim\Slim();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Acess-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS"){
	die();
}

$db = new PDO('mysql:host=localhost;dbname=id11321632_duran', 'id11321632_root', 'Futbol1996.');


$app->get('/categories', function () use($app,$db) {
	$sql = "SELECT * FROM categories";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
});

$app->post('/products', function () use($app,$db) {
	$data = $app->request->post('filter');
	$filter = json_decode($data, true);
	$sql = "SELECT code, metadata, products.name, description, images, categories.name as category, categories.id as categoryId FROM products LEFT JOIN categories ON products.category = categories.id ";
	if(isset($filter['category']))
		$sql .= "WHERE category = '".$filter['category']."'";
	if(isset($filter['query'])){
		if(!isset($filter['category'])){
			$sql .= "WHERE ";
		}else{
			$sql .= "AND ";
		}
		$sql .= "products.name LIKE '%".$filter['query']."%'";
	}
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$sql = "SELECT * FROM categories";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach ($data as $key=>$product) {
		$data[$key]['files'] = [];
		if($product['images'] == 0){
			$data[$key]['files'][] = 'error.png';
		}else{
			for($i=0; $i<$product['images']; $i++){
				$data[$key]['files'][] = $product['code']."-".strval($i+1).".png";
			}
		}
		$categories[intval($product['categoryId'])-1]['products'][] = $data[$key];
	}
	$response = array('data'=>$data,'totalRows'=>sizeof($data), "filter"=>$filter, "sql"=> $sql, "new"=>$categories);
    echo json_encode($response);
});

$app->get('/product/:id', function ($id) use($app,$db) {
	$sql = "SELECT code, metadata, products.name, description, images, categories.name as category FROM products LEFT JOIN categories ON products.category = categories.id WHERE products.code LIKE '".$id."'";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$data['files'] = [];
	for($i=0; $i<$data['images']; $i++){
		$data['files'][] = $data['code']."-".strval($i+1).".png";
	}
	$response = array('data'=>$data);
    echo json_encode($response);
});



$app->run();


?>