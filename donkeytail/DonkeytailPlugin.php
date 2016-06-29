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
		return '1.0.0';
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
		return 'http://simplygoodwork.com/plugins/donkeytail';
	}

	public function getDocumentationUrl()
	{
		return 'http://simplygoodwork.com/plugins/donkeytail';
	}

	public function getReleaseFeedUrl()
	{
		return 'http://simplygoodwork.com/plugins/donkeytail/changelog.json';
	}

}