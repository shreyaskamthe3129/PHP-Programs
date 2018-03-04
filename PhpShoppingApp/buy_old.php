<html>
<head><!-- <title>Buy Products</title> --></head>
<body>
	<?php
	error_reporting(E_ALL);
	ini_set('display_errors','On');
	//$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&visitorUserAgent&visitorIPAddress&keyword=Intel');
	//$xmlstr = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=1719&keyword=Intel&numItems=20');
	$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
	//$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&productId=108616083');
	$xml = new SimpleXMLElement($xmlstr);
	header('Content-Type: text/xml');
	print $xmlstr;
	/* foreach ($xml->children() as $mainParts) {
		if($mainParts->getName() == "category") {
			$categories = $mainParts->children();
			foreach ($categories as $value) {
				if ($value->getName() == "categories") {
					foreach ($value->children() as $categoryParts) {
						foreach ($categoryParts as $key) {
							if($key->getName() == "name") {
								echo $categoryParts->name;
								echo "<br/>";
								$subCategories = $categoryParts->children();
								foreach ($subCategories as $subCatValue) {
									if($subCatValue->getName() == "categories") {
										foreach ($subCatValue->children() as $subCategoryParts) {
											if($subCategoryParts->getName() == 'category') {
												echo "true that".$subCategoryParts["id"];
											}
											foreach ($subCategoryParts as $subKey) {
												if ($subKey->getName() == "name") {
													echo ":-".$subCategoryParts->name;
													echo "<br/>";
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}  */
	?>
</body>
</html>
