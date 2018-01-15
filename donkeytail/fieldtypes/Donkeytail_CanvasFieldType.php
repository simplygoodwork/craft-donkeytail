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
        // Reformat the input name into something that looks more like an ID
        $id = craft()->templates->formatInputId($name);

        // Figure out what that ID is going to look like once it has been namespaced
        $namespacedId = craft()->templates->namespaceInputId($id);

        // Include our Javascript
        craft()->templates->includeJsResource('donkeytail/js/donkeytail.js');
        craft()->templates->includeJsResource('donkeytail/js/jquery-ui.min.js');
        craft()->templates->includeCssResource("donkeytail/css/donkeytail.css");

        $settings = $this->getSettings();

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
                    if ($sibling[$name] != "" && $values) {
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
            'id'          => $id,
            'name'        => $name,
            'namespaceId' => $namespacedId,
            'settings'    => $settings,
            'dot'         => $dot,
            'siblings'    => $siblings,
        );

        return craft()->templates->render("donkeytail/field", $variables);
    }

    public function prepValueFromPost($value)
    {
        if ($value['topPercentage'] == "" && $value['leftPercentage'] == "") {
            return false;
        } else {
            return JsonHelper::encode($value);
        }
    }

    public function prepValue($value)
    {
        $values = (array) JsonHelper::decode($value);
        if (!empty($values['topPercentage']) && !empty($values['leftPercentage'])) {
            if ($values['topPercentage'] != "" && $values['leftPercentage'] != "") {
                $values["topLeftStyles"] = "top:" . $values['topPercentage'] . "%;left:" . $values['leftPercentage'] . "%;";
            }
        } else {
          return false;
        }

        return $values;
    }

    protected function defineSettings()
    {
        return array(
            'canvasImage'   => array(AttributeType::Mixed),
            'canvasImageId' => array(AttributeType::Mixed),
            'dotColor'      => array(AttributeType::Mixed, 'default' => '#F06320'),
            'dotType'       => array(AttributeType::Mixed, 'default' => 'circle'),
            'dotWidth'      => array(AttributeType::Number, 'default' => 20),
            'showSiblings'  => array(AttributeType::Bool, 'default' => false),
        );
    }

    public function getSettingsHtml()
    {
        $settings = $this->getSettings();
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
        foreach ($elementType->getSources() as $key => $source) {
            if (!isset($source['heading'])) {
                $sources[] = array('label' => $source['label'], 'value' => $key);
            }
        }

        return $sources;
    }
}
