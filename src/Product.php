<?php
namespace App;
class Product{

    private $entityTable = "products";
    public $id;
    public $name;
    public $price;
    public $code;
    private $conn;
	
    public function __construct($db){
       $this->conn = $db;
       $this->checkIftablexists($this->entityTable);
    }

    private function checkIftablexists($entityTable){

		$queryCreateProductTable = "CREATE TABLE `".$this->entityTable."` (
		    `id` int(11) unsigned NOT NULL auto_increment,
		    `code` varchar(10) NOT NULL default '',
		    `name` varchar(255) NOT NULL default '',		    
		    `price` varchar(255) NOT NULL default '',
		    PRIMARY KEY  (`id`)
		)";

		if($this->conn->query($queryCreateProductTable)===true){
			$this->insertDummyRecords();
		}

    }

    private function insertDummyRecords(){

		$products = json_decode('[{"id":100,"name":"iPhone SE (32 GB)","price":"349.00"},{"id":101,"name":"iPhone SE (128 GB)","price":"449.00"},{"id":102,"name":"iPhone 6s (32 GB)","price":"449.00"},{"id":103,"name":"iPhone 6s (128 GB)","price":"549.00"},{"id":104,"name":"iPhone 6s Plus (32 GB)","price":"549.00"},{"id":105,"name":"iPhone 6s Plus (128 GB)","price":"649.00"},{"id":106,"name":"iPhone 7 (32 GB)","price":"549.00"},{"id":107,"name":"iPhone 7 (128 GB)","price":"649.00"},{"id":108,"name":"iPhone 7 Plus (32 GB)","price":"669.00"},{"id":109,"name":"iPhone 7 Plus (128 GB)","price":"769.00"},{"id":110,"name":"iPhone 8 (64 GB)","price":"699.00"}]');
	
		foreach ($products as $key => $product) {

			$code = "P00".($key+1);

			@$this->conn->query("
				INSERT INTO ".$this->entityTable." SET name='".$product->name."', code='".$code."', price='".$product->price."' 
			");
		}

    }
	
	public function list(){

		$data =[];
		$query = $this->conn->prepare("SELECT * FROM ".$this->entityTable);
		$query->execute();
		$result = $query->get_result();
		return $result;

	}

	public function details($id){

		$query = $this->conn->prepare("SELECT * FROM ".$this->entityTable." WHERE `id` = ?");
		$query->bind_param("i", $id);
		$query->execute();
		$result = $query->get_result();
		if($result->num_rows>0){
			return $result->fetch_assoc();
		}

	}	

}