<?php
/**
 * Shopping cart class, implements all cart specific logic
 * 
 * @package efusion
 * @subpackage models
 */
class cart
{
	/**
	 * Array of products in the customers cart, format: (int product_id, int quantity, array variants[variant_group_id => selected_variant_id])
	 * @var array
	 */
	var $items;
	
	/**
	 * Flag to indicate if the cart has had any changes made to it
	 * @var boolean
	 */
	var $has_cart_been_modified;
	
	/**
	 * Cart constructor, loads the cart items from the database if user is logged in, else from the session
	 */
	function cart()
	{
		$this->has_cart_been_modified = false;
		
        if(isset($_SESSION['account_id']) && isset($_SESSION['account_group']) && $_SESSION['account_group'] == 'members')
           	$this->find_cart($_SESSION['account_id']);
        else
            $this->find_cart();
            
        register_shutdown_function(array($this,'destroy'));
	}
	
	/**
	 * Adds a product to the cart
	 * @param integer $product_id The unique product ID of the product
	 * @param integer $quantity The number of these products to add (optional)
	 * @param array $variants Array of unique selected variant ID's for the product variation (optional)
	 * @return integer Shopping Cart Unit (SKU), the items array index within the cart
	 */
	function add_to_cart($product_id, $quantity = 1, $variants = false)
	{	
		//Check if the product already exists with the same variants
		$shopping_cart_unit = $this->search_cart($product_id, $variants);
		if($shopping_cart_unit != -1)
		{
			//Update quantity of the product
			$this->items[$shopping_cart_unit]['quantity'] += $quantity;
			$result = $shopping_cart_unit;
			$this->has_cart_been_modified = true;
		}
		else
        {
            //Check that this product actually exists
            $product =& model::create('product');
            if($product->find($product_id))
            {
			     //Create the new product
			     $this->items[] = array('product_id' => $product_id, 'quantity' => $quantity, 'variants' => $variants);									
                 $result = count($this->items);
                 $this->has_cart_been_modified = true;
            }
            else
                $result = false;
		}
		
		return $result;
	}

	/**
	 * Sets the quantity of a product in the cart
	 * @param integer $shopping_cart_unit The index of the item array to remove
	 * @param integer $quantity The quantity of this product to set in the cart
	 * @return true if item changed, false if not found
	 */
	function set_quantity($shopping_cart_unit, $quantity)
	{
		$result = true;
		//Check if item exists in cart
		if(isset($this->items[$shopping_cart_unit]))
		{
			//Validate quantity and set
			$quantity = is_numeric($quantity) ? (int)$quantity : -1;
			if($quantity > 0)
				$this->items[$shopping_cart_unit]['quantity'] = $quantity;
			else if($quantity == 0)
				$this->remove_from_cart($shopping_cart_unit);	//Remove item from cart if quantity is 0
			else
				$result = false;	//Cannot have -ve quantity
				
			$this->has_cart_been_modified = true;
		}
		else
			$result = false;
		
		
		return $result;
	}
	
	/**
	 * Removes a product item from the cart
	 * @param integer $shopping_cart_unit cart items array index of the product to remove
	 * @return boolean true if removed, false if not found
	 */
	function remove_from_cart($shopping_cart_unit)
	{
		//Check if item exists in the cart
		if(isset($this->items[$shopping_cart_unit]))
		{
			//Remove product from the cart
			$this->items[$shopping_cart_unit] = null;	
			unset($this->items[$shopping_cart_unit]);
			
			$result = true;
			$this->has_cart_been_modified = true;
		}
		else
			$result = false; //Cant find the product in the cart to remove
		
		return $result;
	}
	
	/**
	 * Removes all products regardless of variants and quantities with of a specified product id from the cart
	 * @param integer $product_id Unique product ID as stored in the product table
	 * @return integer Number of products removed from the cart
	 */
	function flush_from_cart($product_id)
	{
		//Remove all products of this id
		$shopping_cart_unit = $this->search_cart($product_id);
		$result = 0;

		while($shopping_cart_unit != -1)
		{
			$this->remove_from_cart($shopping_cart_unit);
			$shopping_cart_unit = $this->search_cart($product_id);
			$result++;
		}
		
		$this->has_cart_been_modified = true;
		
		return $result;
	}
	
	/**
	 * Flushes the full contents of the cart (resets it)
	 */
	function flush_cart()
	{
		$this->has_cart_been_modified = true;
		$this->items = array();
	}
	
	/**
	 * Searches the cart for a specified product with matching variants
	 * @param integer $product_id Unique ID of the product from product table
	 * @param array $variants product variants, array list of selected variants (optional)
	 * @return integer Shopping Cart Unit (SKU) / Cart items array index if found, else -1
	 */
	function search_cart($product_id, $variants = false)
	{
		//Check all products until we find a match
		foreach($this->items as $SKU => $product)
		{
			//Find a productId match
			if($product['product_id'] == $product_id)
			{
				if($variants)
				{
					//Check all variants match
					$variants_diff = array_diff($product['variants'], $variants);
					if(count($variants_diff) == 0)
						return $SKU;
				}
				else
					return $SKU;	
			}
		}
		
		//No item found matching criteria
		return -1;
	}
	
    
    /**
     * Loads a shopping cart for a specific member if id given else from session, synchronizes with current session
     * @param integer $account_id members account id
     * @return int number of items loaded into the cart
     */
    function find_cart($account_id = null)
    {  
        if($account_id)
        {
            //Load cart from database
            $account =& model::create('account',$account_id);
            $product =& model::create('product');
            
            $this->items = array();
            
            if($account->serialized_cart != '')
            {
                $saved_cart_products = unserialize($account->serialized_cart);
                
                //Verify the old cart products still exist
                foreach($saved_cart_products as $SKU => $cart_product)
        		{
        			if($product->find($cart_product['product_id']))
        				$this->items[] = $cart_product;
        			else
        				$this->flash['error'][] = 'We no longer stock ' . $product->name . ' so we have removed it from your cart';
        		}
            }
            
            //If a current session cart exists, merge it and delete it
            if(isset($_SESSION['cart']))
            {
                $session_cart = unserialize($_SESSION['cart']);
                
                if(!empty($session_cart))
                {
	                foreach($session_cart as $SKU => $product)
	                    $this->add_to_cart($product['product_id'],$product['quantity'],$product['variants']);   
	            
	                $this->has_cart_been_modified = true;
                }
                
          		$_SESSION['cart'] = null;
				unset($_SESSION['cart']);
            }
        }
        else
        {
            //Load cart from session
            if(isset($_SESSION['cart']))
                $this->items = unserialize($_SESSION['cart']);
            else
                $this->items = array();   
        }
        
        return count($this->items);
    }
	
    /**
     * Saves the shopping cart to the database or session
     * @param integer $account_id members account id, if none given, session will be used
     * @return int number of cart items saved
     */
	function save_cart($account_id = null)
    {
    	if(!$this->has_cart_been_modified)
    		return false;
    	
        if($account_id)
        {   
            $account =& model::create('account',$account_id);
            $account->serialized_cart = serialize($this->items);
            $account->save();      
        }
        else
            $_SESSION['cart'] = serialize($this->items);
        
        return count($this->items);
    }
    
    /**
     * Returns an array of cart items with its product information loaded
     * @return array (float price_total, int quantity, float price, float cost, float weight, int quantity_in_stock, array variants)
     */
    function get_products_in_cart()
    {
    	$cart = array();
        $cart['price_total'] = 0.00;
        
        $product =& model::create('product');
        $variant_group =& model::create('variant_group');
        $product_variant =& model::create('product_variant');
        
        foreach($this->items as $SKU => $cart_product)
        {
        	if(!$product->find($cart_product['product_id']))
        		continue;
        		
        	$product->find_foreign_key('image_id');
        	$product->find_foreign_key('category_id');
        	
        	$cart['products'][$SKU] 			= $product->fields_as_associative_array();
			$cart['products'][$SKU]['image'] 	= $product->image->fields_as_associative_array();
			$cart['products'][$SKU]['category']	= $product->category->fields_as_associative_array();
			
            $cart['products'][$SKU]['quantity'] = $this->items[$SKU]['quantity'];
            $cart['products'][$SKU]['subtotal'] = $this->items[$SKU]['quantity'] * $cart['products'][$SKU]['sale_price'];
            
            $cart['price_total'] += $cart['products'][$SKU]['subtotal'];
          
            //Load the product variants
            if($this->items[$SKU]['variants'])
            {
	           	foreach($this->items[$SKU]['variants'] as $variant_group_id => $variant_id)
	  			{
	  				$variant_group->find($variant_group_id);
	  				$product_variant->find($variant_id);
	  				
	     			$cart['products'][$SKU]['variants'][$variant_group->id] = $product_variant->name;
	           	}
            }
            
            $product->clear_field_values();
        }
        
        return $cart;
    }
    
    /**
     * Gets an array of product id's that have been added to the cart
     * @return array singular array of product id's
     */
    function get_product_ids_in_cart()
    {
    	$product_ids = array();
    	
        foreach($this->items as $SKU => $cart_product)
        	$product_ids[] = $cart_product['product_id'];	
        	
        return $product_ids;
    }
    
    /**
     * Returns a count of the total number of items in the cart
     * @param boolean $unique if we should only count unique items (ignores item quantities)
     * @return int number of items in the cart
     */
    function get_total_items($unique = false)
    {
    	$total_items = 0;
    	
		foreach($this->items as $SKU => $cart_product)
		{
			if(!$unique)
       			$total_items += $cart_product['quantity'];
       		else
       			$total_items++;
		}
       		
       	return $total_items;
    }
    
    /**
     * Calculates the total weight of all the products in the cart and returns the total
     * @return float Total weight of all products in the cart in Kilograms
     */
    function get_total_weight()
    {
		$total_weight = 0.000;
		$product =& model::create('product');
		
		foreach($this->items as $SKU => $cart_product)
		{
			$product->find($cart_product['product_id']);
			$total_weight += ($product->weight * $cart_product['quantity']);
		}
			
		return $total_weight;	
    }
    
    /**
     * Saves the cart data before the cart object is destroyed
     * If user is logged in as a customer, cart is saved to db, else session is used
     */
    function destroy()
    {
        if(isset($_SESSION['account_id']) && isset($_SESSION['account_group']) && $_SESSION['account_group'] == 'members')
           	$this->save_cart($_SESSION['account_id']);
        else
            $this->save_cart();
        
        $this->flush_cart();
    }
}

?>