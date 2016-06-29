<?php

namespace Craft;

class Donkeytail_CanvasFieldType extends BaseFieldType
{
	public function getName()
	{
		return Craft::t('Donkeytail');
	}

	public function defineContentAttribute()
	{
		return AttributeType::String;
	}

	public function getInputHtml($name, $dot)
	{
		craft()->templates->includeJsResource('donkeytail/js/donkeytail.js');
		craft()->templates->includeJsResource('donkeytail/js/jquery-ui.min.js');
		craft()->templates->includeCssResource("donkeytail/css/donkeytail.css");

		$id = craft()->templates->formatInputId($name);
		$namespacedId = craft()->templates->namespaceInputId($id);

		$settings = $this->getSettings();

		// Default settings
		if (!$settings->dotType) $settings->dotType = 'circle';
		if (!$settings->dotWidth) $settings->dotWidth = '20';
		if (!$settings->dotColor) $settings->dotColor = '#F06320';
		if (!$settings->canvasImageId) $settings->canvasImageId = "";

		$image = craft()->assets->getFileById($settings->canvasImageId);
		if ($image) {
			$settings->canvasImage = $image->url;
		}

		$siblings = array();
		// If showSiblings is enabled and we're not a matrix field
		if ($settings->showSiblings and isset($this->element->section)) {
			// Get all elements in this section
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->sectionId = $this->element->section->id;
			$siblingElements = craft()->elements->findElements($criteria);
			// Loop through them
			foreach ($siblingElements as $siblingElement) {
				// If they're not this current element
				if ($siblingElement['id'] != $this->element->id) {
					// Get edit url
					$cpEditUrl = $siblingElement->getCpEditUrl();
					// Get their content
					$sibling = $siblingElement->getContent();
					$values = $this->prepValue($sibling[$name]);
					// If they have an active dot
					if ($sibling[$name] != "") {
						// Prep them for returning
						$siblings[] = array(
							"id" => $sibling['id'],
							"title" => $sibling['title'],
							"cpEditUrl" => $cpEditUrl,
							"topLeftStyles" => "top:" . $values['topPercentage'] . "%;left:" . $values['leftPercentage'] . "%;",
						);
					}
				}
			}
		}

		$variables = array(
			'id' => $id,
			'name' => $name,
			'namespaceId' => $namespacedId,
			'settings' => $settings,
			'dot' => $dot,
			'siblings' => $siblings,
		);

		return craft()->templates->render("donkeytail/field", $variables);
	}


	public function prepValueFromPost($value)
	{
		if ($value['topPercentage'] == "" && $value['leftPercentage'] == "") {
			return false;
		} else {
			return json_encode($value);
		}
	}


	public function prepValue($value)
	{
		$values = (array)json_decode($value);
		if (!empty($values['topPercentage']) && !empty($values['leftPercentage'])) {
			if ($values['topPercentage'] != "" && $values['leftPercentage'] != "") {
				$values["topLeftStyles"] = "top:" . $values['topPercentage'] . "%;left:" . $values['leftPercentage'] . "%;";
			}
		}

		return $values;
	}


	protected function defineSettings()
	{
		return array(
			'showSiblings' => array(AttributeType::Bool),
			'dotType' => array(AttributeType::Mixed),
			'dotWidth' => array(AttributeType::Number),
			'dotColor' => array(AttributeType::Mixed),
			'canvasImageId' => array(AttributeType::Mixed),
			'canvasImage' => array(AttributeType::Mixed),
		);
	}


	public function getSettingsHtml()
	{
		$settings = $this->getSettings();
		if (!$settings->dotColor) $settings->dotColor = '#F06320';

		$variables = array();

		if ($settings['canvasImageId']) {
			if (is_array($settings['canvasImageId'])) {
				$settings['canvasImageId'] = $settings['canvasImageId'][0];
			}
			$asset = craft()->elements->getElementById($settings['canvasImageId']);
			$variables['elementsOwnerImage'] = array($asset);
		} else {
			$variables['elementsOwnerImage'] = array();
		}
		$variables['elementType'] = craft()->elements->getElementType(ElementType::Asset);

		return craft()->templates->render('donkeytail/field-settings', array(
			'settings' => $settings,
			'variables' => $variables,
		));
	}


	protected function getElementSources($elementType)
	{
		$sources = array();
		foreach ($elementType->getSources() as $key => $source)
		{
			if (!isset($source['heading']))
			{
				$sources[] = array('label' => $source['label'], 'value' => $key);
			}
		}

		return $sources;
	}
}