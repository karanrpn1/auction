<div class="cus-search-form">
	<div class="cus-search-field">
  		<?php  $searchUrl = explode('?', $_SERVER['REQUEST_URI'], 2); ?>            	
    	<form role="search" method="get" class="woocommerce-product-search" action="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $searchUrl[0]; ?>">
    		<div class="cus-search-field-group">
            	<i class="fa fa-search" aria-hidden="true"></i>
				<div id="typed-strings">
				    <p>Audi A4 2.0 TDI</p>
				    <p>Bmw 335i</p>
				    <p>Volvo S40</p>
				    <p>Toyota Aygo 1.0</p>
				</div>
                <input id="typed" type="text" class="search-field" placeholder="<?php echo esc_attr_x( '', 'placeholder', 'woocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Søg:', 'label', 'woocommerce' ); ?>" />
            	<script>
				  var typed = new Typed('#typed', {
				    stringsElement: '#typed-strings',
				    typeSpeed: 100,
				    backSpeed: 50,
				    backDelay: 5000,
    				startDelay: 2000,
				    attr: 'placeholder',
    				bindInputFocusEvents: true,
    				loop: true
				  });
				</script>
            </div>
            <div class="cus-search-field-submit">
            	<button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'woocommerce' ); ?>">Søg</button>
            </div>
            <input type="hidden" name="post_type" value="product" />
        </form>                
    </div>
	<div class="cus-form-hide-show" onclick="myFunction()"><span>Filtrer dine valg</span><span><i class="fa fa-chevron-down" aria-hidden="true"></i>
</span></div>
	<div class="cus-search-form-inner <?php if(!isset($_REQUEST['filterProduct'])){ echo "hide-show"; } ?>" id="cus-search-form-inner">
    	<form class="cus-search-filter-form" method="get">
            <div class="cus-search-main-filter">
            	<div class="row row-eq-height filterOptionsRow">
             		<div class="col-xs-12 col-sm-9 col-md-10 filterOptions">
		            	<h3 class="visible-xs">Kategori</h3>
		            	<ul> 
		                	<li><input type="radio" name="mainCategory" value="all" id="mainCat_1" checked="checked"><label for="mainCat_1">Alle</label></li>
		                    
							<?php
								$productCatLoop = get_terms( array('taxonomy'=>'product_cat', 'hide_empty' => false) );		
								$i = 2; 
								if(isset($_GET['mainCategory'])) {	
									$chkValue = $_GET['mainCategory'];
								}			
		                        foreach($productCatLoop as $productData)	{
									if($productData->name=="Product Slideshow") {
										continue;
									}	
									$chk = "";
									if(!empty($chkValue) && $chkValue==$productData->slug) { 								
										$chk = "checked";								
									}										
		                            echo '<li><input type="radio" name="mainCategory" value="'.$productData->slug.'" id="mainCat_'.$i.'" '.$chk.'> <label for="mainCat_'.$i.'">'.$productData->name.'</label></li>';				
									$i++;		 	
		                        } 
							?>
					   	</ul>
              		</div>
              		<div class="col-sm-3 col-md-2 sellMyCar">
              			<a href="https://webbil.dk/saelg-din-bil/">
 							<div class="flash-button text-center">Sælg din bil</div>
            			</a>
            		</div>
              	</div>
            </div>            
            <div class="cus-attribute-search"> 
            	<?php	
												 
					$productAttributes = wc_get_attribute_taxonomies();					
					$productAttributes = json_decode(json_encode($productAttributes), True);						
					
					$order = array(4,10,7,8,9); //attribute id
					$newProductAttributesOrder = $productAttributes;  
					
					usort($newProductAttributesOrder, function ($a, $b) use ($order) {
						$pos_a = array_search($a['attribute_id'], $order);
						$pos_b = array_search($b['attribute_id'], $order);
						return $pos_a - $pos_b;
					});					
					
					foreach($newProductAttributesOrder as $productAttribute ) {						
						if(get_option("filterAttributeStatus_".$productAttribute['attribute_name'])=="on") {
							echo '<div class="cus-single-taxanomy">';
							echo '<h3>'.$productAttribute["attribute_label"].'</h3>';
							$productTaxLoop = get_terms(  array('taxonomy'=>'pa_'.$productAttribute['attribute_name'], 'hide_empty' => false) );
							if(get_option("filterAttributeType_".$productAttribute['attribute_name'])==2) {			
								$chkValue = "";
								if(isset($_GET['pa_'.$productAttribute["attribute_name"].'_from'])) {	
									$chkValue = $_GET['pa_'.$productAttribute["attribute_name"].'_from'];
								}
								echo "<label>Fra</label>";			
								echo '<select name="pa_'.$productAttribute["attribute_name"].'_from">';			
								foreach($productTaxLoop as $productTaxData)	{									
									$chk = "";
									if(!empty($chkValue) && $chkValue==$productTaxData->slug) { 								$chk = "selected";
									}
									echo '<option value="'.$productTaxData->slug.'" '.$chk.'>'.$productTaxData->name.'</option>';					
								}
								echo '</select>';
								$chkValue = "";
								if(isset($_GET['pa_'.$productAttribute["attribute_name"].'_to'])) {							$chkValue = $_GET['pa_'.$productAttribute["attribute_name"].'_to'];
								}
								echo "<label>Til</label>";	
								echo '<select name="pa_'.$productAttribute["attribute_name"].'_to">';							
								$startId = 0;
								$endId = count($productTaxLoop);		
								foreach($productTaxLoop as $productTaxData)	{
									$startId++;
									$chk = "";
									if(!empty($chkValue) && $chkValue==$productTaxData->slug) { 								$chk = "selected";				 				
									}	
									if(!isset($_GET['pa_'.$productAttribute["attribute_name"].'_to'])) {
										if($startId == $endId ) { 
											$chk = "selected";
										}
									} 
									echo '<option value="'.$productTaxData->slug.'" '.$chk.'>'.$productTaxData->name.'</option>';
								}
								echo '</select>';
							}
							else {	
								$getArray = array();							
								if(isset($_GET['pa_'.$productAttribute["attribute_name"]])) {									$getArray = $_GET['pa_'.$productAttribute["attribute_name"]]; 
								}
								echo '<ul>';
								$i = 1;
								foreach($productTaxLoop as $productTaxData)	{
									$chk = "";	
									if(in_array($productTaxData->slug,$getArray)) {
										$chk = "checked";
									}														
									echo '<li><input type="checkbox" name="pa_'.$productAttribute["attribute_name"].'[]" value="'.$productTaxData->slug.'" id="'.$productAttribute["attribute_name"].'_'.$i.'" '.$chk.'><label for="'.$productAttribute["attribute_name"].'_'.$i.'">'.$productTaxData->name.'</label></li>';
									$i++;
								}
								echo '</ul>';
							}	
							echo '</div>';
						}										
					}	
				?>
            </div>            
            <div class="cus-search-submit-field">
            	<?php 
				$resetUrl = explode('?', $_SERVER['REQUEST_URI'], 2); ?>
            	<a href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $resetUrl[0]; ?>">Nulstil</a>
            	<button type="submit" name="filterProduct" value="filter">Filtrer</button>
            </div>
        </form>        
    </div>
</div>
<script type="text/javascript">
function myFunction() {
    var x = document.getElementById("cus-search-form-inner");
	x.classList.toggle('hide-show');    
}
</script>