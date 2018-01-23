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
		return '1.0.5';
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
		return 'https://github.com/simplygoodwork/donkeytail-plugin/blob/master/README.md';
	}

	public function getReleaseFeedUrl()
	{
		return 'https://raw.githubusercontent.com/simplygoodwork/donkeytail-plugin/master/releases.json';
	}

}