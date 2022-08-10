<?php
namespace App;
class Cart
{
    private $entityTable = "cart_sessions";
	public $cartId;
	private $cartItems = [];
	private $conn;
	private $message;	
	public function __construct($db)
	{
		// if (!session_id())
		// 	session_start();

		$this->conn = $db;
		// $this->cartId = md5($_SERVER['HTTP_HOST']);
		$this->cartId = md5('HTTP_HOST');
		$this->getCartItems($this->cartId);

	}

	public function getItems()
	{
		return $this->cartItems;
	}

	public function isEmpty()
	{
		return empty(array_filter($this->cartItems));
	}

	public function getTotalItem()
	{
		$total = count($this->cartItems);
		return $total;
	}

	/**
	 * Get the total of item quantity in cart.
	 *
	 * @return int
	 */
	public function getTotalQuantity()
	{
		$quantity = 0;

		foreach ($this->cartItems as $cartItems) {
			foreach ($cartItems as $item) {
				$quantity += $item['quantity'];
			}
		}

		return $quantity;
	}

	/**
	 * Get the sum of cart if offer is active.
	 *
	 * @param string $offer
	 *
	 * @return int
	 */
	public function getItemsTotalWithOffer($offer)
	{
		$total = 0;

		if($offer && $offer['discount']>0 && $offer['type']==='percent'){
			foreach ($this->cartItems as $cartItem) {
				if (isset($cartItem->price)) {
					$total += $cartItem->price * $cartItem->quantity;
				}
			}
		return $total - ($total * ($offer['discount']/100));
		}

	}
	/**
	 * Get the sum of cart.
	 *
	 *
	 * @return int
	 */
	public function getItemsTotal()
	{
		$total = 0;
		foreach ($this->cartItems as $cartItem) {
			if (isset($cartItem->price)) {
				$total += $cartItem->price * $cartItem->quantity;
			}
		}
		return $total;

	}
	public function getMessage()
	{
		return $this->message;
	}

	public function setMessage($msg)
	{
		$this->message = $msg;
	}

	public function removeMessage()
	{
		$this->message = '';
	}

	/**
	 * Add item to cart.
	 * $price has its orignal price
	 * $discount has int value of percentage
	 * @param int 	$id
	 * @param int   $quantity
	 * @param int   $price
	 * 
	 * @return bool
	 */
	public function addToCart($id, $quantity=1)
	{	
		$product =  new Product($this->conn);

		$productDetails = $product->details($id);
		if(!empty($productDetails)){
			if(isset($this->cartItems[$id])){
				$this->setMessage('Unsuccessfull!, Product cannot be added multiple time into cart.');
				return array('status'=>true, 'state'=>'already');
			}else{
				$product = array('quantity'=>$quantity, 'price'=>$productDetails['price']);
				$this->cartItems[$id] = (object)$product;
				$this->setCartItems($this->cartItems);
				$this->setMessage('Product added into cart successfully!');
				return array('status'=>true, 'state'=>'added');				
			}
		}
		$this->setMessage('Could not added to cart, Please try again!');
		return false;
	}
	/**
	 * Remove item from cart.
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public function removeFromCart($id)
	{
		if (!isset($this->cartItems[$id])) {
			return false;
		}
		$temp = [];
		foreach ($this->cartItems as $productId => $item) {
			if($id!==$productId){
				$temp[$productId]= $item;
			}
		}
		$this->setMessage('Item removed from Cart!');
		if(count($temp)>0){
			$this->cartItems = $temp;
			return $this->setCartItems($this->cartItems);
		}else{
			$this->emptyCart();
		}

	}
	


	/**
	 * get items from cart table in db.
	 */
	public function getCartItems($cartId)
	{
		$data = [];
		$qry = $this->conn->prepare("SELECT * FROM ".$this->entityTable." WHERE `key` = ?");
		$qry->bind_param("s", $cartId);
		$qry->execute();
		$result = $qry->get_result();
		if($result->num_rows>0){
			$data   = $result->fetch_assoc();
			$this->cartItems = (array)json_decode($data['value']);
			return $this->cartItems;
		}
		else{
			return $data;
		}
	}

	/**
	 * setCartItems changes into cart session.
	 */

	private function setCartItems($cartData)
	{
		$cartItems = $this->getCartItems($this->cartId);
		if($cartItems){			 
			$this->conn->query("UPDATE ".$this->entityTable."  SET 
				`value`	='".json_encode(array_filter($cartData))."', 
				`expiry`= '".date("Y-m-d H:i:s", strtotime('+24 hours'))."' WHERE
				`key` 	='".$this->cartId."'");

		}else{
			$query = "INSERT INTO ".$this->entityTable." SET `key`='".$this->cartId."', `value`='".json_encode(array_filter($this->cartItems))."', `expiry`='".date("Y-m-d H:i:s", strtotime('+24 hours'))."' ";
			$this->conn->query($query);
		}
		$cartItems = $this->getCartItems($this->cartId);
		$this->cartItems = $cartItems;
		return $cartItems;
	}


	/**
	 * Remove all cartItems from cart.
	 */
	public function emptyCart()
	{
		$this->conn->query("DELETE FROM ".$this->entityTable." WHERE `key`='".$this->cartId."'");
		$this->cartItems = [];
		$this->setMessage('Cart set to empty!');	
		// $this->setCartItems([]);
	}

}