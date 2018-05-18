<?php
class XpathHelper {
	public static function get_html_element($xpath, $html){
		libxml_use_internal_errors(true);
		$dom = new DomDocument;
		$dom->loadHTML($html);
		$dom_xpath = new DomXPath($dom);
		$result = $dom_xpath->query($xpath);
		return self::dom_to_array($result);
	}
	public static function dom_to_array($result){
		$elements = array();
		foreach($result as $element) {
		    $current = array();
		    foreach ($element->attributes as $attribute) {
		        $current[$attribute->name] = $attribute->value;
		    }
		    $elements[] = $current;
		}
		return $elements;
	}
}