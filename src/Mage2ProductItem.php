<?php  
/**
* 
*/

use Cocur\Slugify\Slugify;

class Mage2ProductItem
{

	public $sku;
	public $store_view_code;
	public $attribute_set_code = 'Default';
	public $product_type = 'simple';
	public $categories;
	public $product_websites = 'base';
	public $name;
	public $description;
	public $short_description;
	public $weight;
	public $product_online = 1;
	public $tax_class_name = 'Taxable Goods';
	public $visibility = 'Catalogus, Zoeken';
	public $price = 0;
	public $special_price;
	public $special_price_from_date;
	public $special_price_to_date;
	public $url_key;
	public $meta_title;
	public $meta_keywords;
	public $meta_description;
	public $created_at;
	public $updated_at;
	public $new_from_date;
	public $new_to_date;
	public $display_product_options_in = 'Block after Info Column';
	public $map_price;
	public $msrp_price;
	public $map_enabled;
	public $gift_message_available;
	public $custom_design;
	public $custom_design_from;
	public $custom_design_to;
	public $custom_layout_update;
	public $page_layout = '2 columns with right bar';
	public $product_options_container;
	public $msrp_display_actual_price_type;
	public $country_of_manufacture;
	public $additional_attributes;
	public $qty = 100;
	public $out_of_stock_qty = 0;
	public $use_config_min_qty = 1;
	public $is_qty_decimal = 0;
	public $allow_backorders = 0;
	public $use_config_backorders = 1;
	public $min_cart_qty = 1;
	public $use_config_min_sale_qty = 0;
	public $max_cart_qty = 0;
	public $use_config_max_sale_qty = 1;
	public $is_in_stock = 1;
	public $notify_on_stock_below;
	public $use_config_notify_stock_qty = 1;
	public $manage_stock = 0;
	public $use_config_manage_stock = 1;
	public $use_config_qty_increments = 1;
	public $qty_increments = 0;
	public $use_config_enable_qty_inc = 1;
	public $enable_qty_increments = 0;
	public $is_decimal_divided = 0;
	public $website_id = 1;
	public $deferred_stock_update = 0;
	public $use_config_deferred_stock_update = 1;
	public $related_skus;
	public $crosssell_skus;
	public $upsell_skus;
	public $hide_from_product_page;
	public $custom_options;
	public $bundle_price_type;
	public $bundle_sku_type;
	public $bundle_price_view;
	public $bundle_weight_type;
	public $bundle_values;
	public $associated_skus;
	public $base_image;
	public $small_image;
	public $thumbnail_image;		
	public $additional_images;


	public static $names = array();
	public static $cats = array();

	public function addAttribute($key,$value)
	{
		if(!$value){
			return;
		}
		$value = trim($value);
		$value = str_replace(',', '.', $value);
		$value = str_replace('=', '--', $value);
		$value = str_replace("\n", '  ', $value);
		//todo: use different separator instead
		$av = "$key=$value";
		$this->additional_attributes = trim($this->additional_attributes)?$this->additional_attributes.','.$av:$av;
	}

	public function addCategory($value)
	{
		$value = $this->normalizeCategory($value);
		$value = str_replace('/', '--', $value);
		$value = str_replace('\\', '/', $value);
		$value = 'hoofd categorie'.($value?'/':'').$value;

		$this->categories = $this->categories?$this->categories.','.$value:$value;
	}

	public function normalizeCategory($value)
	{
		// fix duplicate name first
		$tnames_new = array();
		$path = '';
		$tnames = explode('\\', $value);
		foreach ($tnames as $name) {
			$name = $this->normalizeCatName($name,$path?$path.'\\'.$name:$name);
			$path = $path?$path.'\\'.$name:$name;
		}
		return $path;
	}

	public function normalizeCatName($name,$path)
	{
		// $r = array_search($path,static::$cats);
		$r = array_search(strtolower($path), array_map('strtolower', static::$cats));
		if(false !== $r){
			return $r;
		}
		if(isset(static::$cats[$name])){
			$name = $this->normalizeCatName($name.' I',$path);
		}
		static::$cats[$name] = $path;
		return $name;
	}

	public function setCategories($arr)
	{
		foreach ($arr as $cat) {
			$this->addCategory($cat);
		}
	}

	public function addImage($value)
	{

		if(strlen($this->additional_images) > 148){
			return;
		}

		$filename_new = $value;

		$replace = array(
			'+'=>'_',
			','=>'_',
			'%20'=>'_',
			'('=>'_',
			')'=>'_',
			' '=>'_',
		);

		$filename_new = str_replace(array_keys($replace), array_values($replace), $filename_new);
		$value = $filename_new;

		// $value = str_replace(' ', '%20', $value);
		// $value = str_replace('https://www.gastronoble.com', 'http://horecawebstore.nl/img.php?i=https://www.gastronoble.com', $value);
		if($this->base_image){
			$this->additional_images.=($this->additional_images?', ':'').$value;
		}
		else{
			$this->base_image = $value;
			$this->small_image = $value;
			$this->thumbnail_image = $value;
		}
	}

	public function setImages($arr)
	{
		foreach ($arr as $img) {
			$this->addImage($img);
		}
	}

	public function addDescriptionShort($value)
	{
		$this->short_description = $this->short_description?"<br>".$value:$value;
	}

	public function setName($value)
	{
		// $value = str_replace(array('&',"\""), array('and',""), $value);
		// $exception = in_array($value, array('Rechthoekige RVS serveerschaal 42x31cm','Rechthoekige RVS serveerschaal 50x36cm'));
		// $hash = md5(str_replace(array('-','.',',','  '), array('','','',' '), strtolower($value)));
		$slugify = new Slugify();
		$this->url_key=$slugify->slugify($value.' '.$this->sku,'-');
		// if(isset(static::$names[$hash]) || $exception){
		// 	$this->name = $value;
		// 	// $this->setName($this->sku .' '.$value);
		// }
		// else{
		// 	static::$names[$hash] = 1;
		// }
		$this->name = $value;
	}

	public function setWeight($value)
	{
		$value = str_replace(',', '.', $value);
		if(is_numeric($value)){
			$this->weight = (float)$value;
		}
	}

}
?>