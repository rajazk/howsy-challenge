<?php
use App\Database;
use App\Store;
use App\Cart;
use PHPUnit\Framework\TestCase;
require_once 'config.php';

class CartTests extends TestCase
{
    protected $conn;
    protected $cart;
    protected $store;
    protected function setUp():void
    {
        $db = new Database(array('db'=>DB_NAME, 'user'=>DB_USER, 'password'=>DB_PASSWORD, 'host'=>DB_HOST ));
        $this->conn  = $db->connect();
        $this->store = new Store($this->conn);
        $this->store->setOffer(array('title'=>'10% discount on 12-month contract'  ,'discount'=>10, 'type'=>'percent'));
        $this->cart  = new Cart($this->conn);
    }


    public function testListProducts()
    {
        $products = $this->store->getProducts();
        echo "Total Store Products:";
        print_r($products);
        $this->assertTrue((count($products)>0));

    }

    public function testAddTocart()
    {
        echo "Adding random Items to cart on each test:". "/n/r";
        $products = $this->store->getProducts();
        $cartItems =  $this->cart->addToCart($products[rand(0,(count($products)-1))]->id, 1);
        echo "cart Items:";
        print_r($cartItems);
        $this->assertTrue((count($cartItems)>0));        
    }

    public function testgetItemsTotal()
    {
        echo "Total amount without offer:";
        echo $ItemsTotal =  $this->cart->getItemsTotal();
        $this->assertTrue(($ItemsTotal>-1));
    }
    /* @test */
    public function test_getItemsTotalWithOffer()
    {
        echo "testgetItemsTotalWithOffer: ";
        echo  $ItemsTotalDiscounted =  $this->cart->getItemsTotalWithOffer($this->store->offer);
        echo ":". "\n\r";        
        $this->assertTrue(($ItemsTotalDiscounted>-1));
    }

}