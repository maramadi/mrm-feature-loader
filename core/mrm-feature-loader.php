<?php
if ( ! defined( 'ABSPATH' ) ) exit;

Class MrmFeatureLoader {

	protected static $_instance;

	protected $features_path;
	protected $store = [];

	public function __construct(){}
	public static function activation(){
		// Set options
		update_option('mrm_fl_settings',array(
			'mrm_fl_features' => []
			)
		);
	}

	public static function deactivation(){
		delete_option('mrm_fl_settings');
	}

	/************************************
	*				INIT
	* --------------------------------*/
	public static function init()
	{
		if(is_null(self::$_instance))
	    {
			// Instantiate the object
			$instance = new self();

			// Set all vars
			$instance->features_path = get_stylesheet_directory() . '/features';

			// Add all hooks
			self::addActions();

			// Bint the instance to the variable
			self::$_instance = $instance;

			// Give back the the new instance
			return self::$_instance;
		}
		else{
			throw new \Exception('MrmFeatureLoader was already initialized. And can not be initialized again');
		}
	}

	private static function addActions()
	{

		add_action( 'init',array('MrmFeatureLoader', 'loadActivatedFeatures' ) );

		if( is_admin() )
		{
			/**
			 * Register admin hooks
			 *
			 * @see views/admin.php
			 */
			add_action( 'admin_menu', 'mrm_add_admin_menu' );
			add_action( 'admin_init', 'mrm_fl_settings_init' );

			/**
			 * Register the meta boxes hooks
			 */
			add_action( 'add_meta_boxes', array('MrmFeatureLoader', 'createMetaBox')  );
			add_action( 'save_post', array('MrmFeatureLoader', 'savePostMeta'), 10, 2 );
		}
		else
		{
			/**
			* Register the frontend hooks
			*/
			add_action( 'wp', array('MrmFeatureLoader', 'prepareFeatureload')  );
		}
	}

	public static function prepareFeatureload()
	{
		global $post;
		$instance = self::getInstance();
		/* The meta key. */
		$meta_key = 'mrm_fl_features';
		/* Get the meta value of the custom field key. */
		$features = get_post_meta( $post->ID, $meta_key, true );

		foreach ($features as $featureSlug => $featureData)
		{
			self::getInstance()->registeredFeatures[$featureSlug]->run();
		}
	}

	/**
	 * This function gets called from the feature
	 * to register the feature in the loader
	 * @param  MrmFeature $feature
	 * @return void
	 */
	static function registerFeature($feature)
	{
		$instance = self::getInstance();
		$instance->registeredFeatures[$feature->slug] = $feature;
	}


	/**
	 * Create the box and fill it with feature settings
	 * @return void
	 */
	public static function createMetaBox()
	{
		global $post;
		if( in_array( $post->post_type, self::getActivatedPostTypes() ) ){
			add_meta_box(
				'mrm_fl_meta_box', 
				'Feature Loader',
				array(self::getInstance(),'renderMetaBox'),
				$post->post_type,
				'advanced'
			);
		}
	}

	/**
	 * Render the metabox
	 */
	static function renderMetaBox($object, $box)
	{

		foreach (self::getInstance()->registeredFeatures as $slug => $feature) :
		//foreach (self::getInstance()->registeredSettings as $slug => $featureData):?>

			<div class="mrm-features-metabox">
				<?php wp_nonce_field( basename( __FILE__ ), 'mrm_fl_metabox_nonce' ); ?>
				<?php if(self::canShow($slug, $object)): ?>
					<?php echo MrmFeatureSettingsRenderer::render($feature, $object, $box) ?>
				<?php endif; ?>

			</div>

		<?php endforeach;
	}

	public static function savePostMeta($post_id, $post )
	{
		/* Verify the nonce before proceeding. */
		 if ( !isset( $_POST['mrm_fl_metabox_nonce'] ) || !wp_verify_nonce( $_POST['mrm_fl_metabox_nonce'], basename( __FILE__ ) ) )
		   return $post_id;

		 /* Get the post type object. */
		 $post_type = get_post_type_object( $post->post_type );

		 /* Check if the current user has permission to edit the post. */
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* The meta key. */
		$meta_key = 'mrm_fl_features';

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		$new_meta_value = $_POST[$meta_key];

		/* If a new meta value was added and there was no previous value, add it. */
		if ( ! $meta_value && $new_meta_value )
		{
		   add_post_meta( $post_id, $meta_key, $new_meta_value, true );
		}
	 	/* If the new meta value does not match the old value, update it. */
	 	elseif( $new_meta_value && $new_meta_value != $meta_value )
	 	{
	 	 	update_post_meta( $post_id, $meta_key, $new_meta_value );
	 	}
	 	/* If there is no new meta value but an old value exists, delete it. */
	 	elseif ( '' == $new_meta_value && $meta_value )
	 	{
	 	  delete_post_meta( $post_id, $meta_key, $meta_value );
	 	}
	}

	public function loadActivatedFeatures()
	{

		$activatedFeatures = self::getInstance()->getActivatedFeatures();
		$avilableFeatures = self::getInstance()->getAvailableFeatures();
		
		foreach ($activatedFeatures as $feature) {

			$featurePath = $avilableFeatures[$feature]['path'];	

			if( file_exists($featurePath) )
			{
				require $featurePath;
			}

		}

	}

	public static function getAvailableFeatures()
	{
		$instance = self::getInstance();
		if( ! isset($instance->avilable_features) )
		{

			$features = [];

			if( file_exists($instance->features_path ) )
			{
				$folders = scandir($instance->features_path . '/');
				foreach ($folders as $feature)
				{
					if($feature !== '.' && $feature !== '..')
					{
						$instance->feature_fullpath = $instance->features_path . '/' . $feature . '/' . $feature . '.php';
						if( file_exists($instance->feature_fullpath) )
						{
							$features[$feature] = array(
								'name' => $feature,
								'path' => $instance->feature_fullpath
								);
						}
					}
				}
			}
			$instance->avilable_features = $features;
		}
		return $instance->avilable_features;
	}

	static function getActivatedFeatures()
	{
		//@Todo Don't list deleted folders anymore
		return self::getOption('mrm_fl_features');
	}

	static function getActivatedPostTypes()
	{
		return self::getOption('mrm_fl_post_types');
	}

	static function getOption($key)
	{
		$options = get_option('mrm_fl_settings');
		if( isset($options[$key]) )
		{
			return $options[$key];
		}
		return '';
	}

	static function isActive($feature)
	{
		$features = getActivatedFeatures();
		return in_array($feature, $features);
	}

	static function canShow($feature, $post)
	{

		$activatedPostTypes =  MrmFeatureLoader::getActivatedPostTypes();

		if( in_array($post->post_type, $activatedPostTypes ) )
		{
			return true;
		}

	}

	public static function getInstance()
	{
	    if(is_null(self::$_instance))
	    {
	    	self::init();
	        //throw new \Exception('FeatureLoader has to be initialized with init() first');
	    }
	    return self::$_instance;
	}


	/**
	 * Get data from the store
	 * @param  string storeKey store key
	 * @param  string value value to store
	 * @return self
	 */
	public function setData($storeKey, $data)
	{
		$_instance = self::getInstance();
		$_instance->store[$storeKey] = $data;
		return $_instance;
	}

	/**
	 * Get data from the store
	 * @param  string $storeKey store key
	 * @return mixed       	store value
	 */
	public function getData($storeKey = null)
	{
		
		$instance = self::getInstance();

		if(is_null($storeKey)){
			return $instance->store;
		}

		if(isset($instance->store[$storeKey]))
		{
			return $instance->store[$storeKey];
		}

		return '';

	}
}