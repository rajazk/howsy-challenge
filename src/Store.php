<?php
namespace App;
class Store{

    public $name;
    private $page;
    private $conn;
    public $offer = [];
	
    public function __construct($db){
        $this->conn = $db;
		$this->page = (isset($_GET['a'])) ? $_GET['a'] : 'home';        
    }

    public function page()
    {
    	return $this->page;
    }
	
	public function getProducts(){

		$product =  new Product($this->conn);
		$productsList = $product->list();
		if(!empty($productsList)){

			if($productsList->num_rows>0){
				while($row = $productsList->fetch_assoc()) {
				    $products[] = (object)$row;
				}
				return $products;
			}
		}
		else
			return false;

	}
	
	public function getCartpage()
	{

	}

	public function setOffer($offerArray){
		$this->offer = $offerArray;
	}

	public function removeOffer(){
		$this->offer = [];
	}
}