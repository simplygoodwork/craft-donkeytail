<?php

namespace Craft;

class DonkeytailPlugin extends BasePlugin
{

	public function getName()
	{
		return Craft::t('Donkeytail');
	}

	public function getVersion()
	{
		return '1.0.1';
	}

	public function getDescription()
	{
		return 'A fieldtype for quickly and easily managing points on images.';
	}

	public function getDeveloper()
	{
		return 'Good Work';
	}

	public function getDeveloperUrl()
	{
		return 'http://simplygoodwork.com';
	}

	public function getPluginUrl()
	{
		return 'https://github.com/simplygoodwork/donkeytail-plugin';
	}

	public function getDocumentationUrl()
	{
		return 'https://github.com/simplygoodwork/donkeytail-plugin';
	}

	public function getReleaseFeedUrl()
	{
		return 'https://github.com/simplygoodwork/donkeytail-plugin';
	}

}