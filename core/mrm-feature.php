<?php 
if( ! defined('ABSPATH') ) die();

Abstract Class MrmFeature {

	public $name;
	public $slug;
	public $description;

	public function __construct($name, $description, $filename )
	{
		$this->name = $name;
		$this->slug = basename($filename, '.php');
		$this->description = $description;

		MrmFeatureLoader::registerFeature($this);
	}

	public function isActive()
	{
		return MrmFeatureLoader::canShow();
	}

	public function getSettingsKey($settingsSlug)
	{
		return 'mrm_fl_features['. $this->slug .']['. $settingsSlug .']';
	}

	public function getSettings()
	{
		return $this->settings();
	}

	public function getSetting($slug, $postId = null)
	{
		if( ! isset( $this->settings ))
		{
			global $post;
			if( ! $post && $postId === null) return;
			$postId = $postId ?: $post->ID;
			$features = get_post_meta( $postId, 'mrm_fl_features', true );
			$this->settings = $features[$this->slug];
		}
		return isset( $this->settings[$slug] )? $this->settings[$slug] : '';
	}

	abstract function run();

	abstract function settings();
}