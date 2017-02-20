<?php
if( ! defined('ABSPATH') ) die();

function mrm_add_admin_menu(  ) { 

	add_submenu_page( 
		'themes.php',
		'Feature loader',
		'Feature loader',
		'manage_options',
		'mrm_fl_settings_init',
		'mrm_options_page'
		);

}


function mrm_fl_settings_init(  ) { 

	register_setting( 'mrm_fl_plugin_page', 'mrm_fl_settings' );

	add_settings_section(
		'mrm_pluginPage_section', 
		__( 'Basic settings for feature loader', ' mrm-feature-loader' ), 
		'mrm_fl_settings_section_callback', 
		'mrm_fl_plugin_page'
	);

	// Show where
	add_settings_field( 
		'mrm_fl_post_types', 
		__( 'Show on which post types', ' mrm-feature-loader' ),
		'mrm_fl_post_types_render', 
		'mrm_fl_plugin_page', 
		'mrm_pluginPage_section' 
	);

	// Activate/deactivate features
	add_settings_field( 
		'mrm_fl_features',
		__( 'Activate or deactivate featueres', ' mrm-feature-loader' ),
		'mrm_fl_features_render', 
		'mrm_fl_plugin_page', 
		'mrm_pluginPage_section' 
	);
}


function mrm_fl_post_types_render(  ) { 

	$options = get_option( 'mrm_fl_settings' );
	$post_types = MrmHelper::getAvailablePostTypes();
	?>
	
	<select multiple="true" name="mrm_fl_settings[mrm_fl_post_types][]" style="width:200px">
		<?php foreach($post_types as $post_type): ?>
			<?php $selected =  in_array($post_type->name, $options['mrm_fl_post_types'])?'selected':''?>
			<option value="<?php echo $post_type->name ?>" <?php echo $selected?>>
				<?php echo $post_type->label ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php
}

function mrm_fl_features_render(  ) { 

	$activatedFeatures = MrmFeatureLoader::getActivatedFeatures();
	$availableFeatures = MrmFeatureLoader::getAvailableFeatures();

	foreach($availableFeatures as $feature):?>
		<input 
			type="checkbox" 
			name="mrm_fl_settings[mrm_fl_features][]" 
			value="<?php echo $feature['name'] ?>"
			id="<?php echo $feature['name'] ?>"
			<?php echo in_array($feature['name'], $activatedFeatures)?' checked':'' ?>
		>
		<label for="<?php echo $feature['name'] ?>">
			<?php echo $feature['name'] ?>
		</label>
		<?php/*
		if(isset($options['mrm_fl_features']))
		{
			if(isset($options['mrm_fl_features'][$feature['name']]))
			{
				echo 'checked';
			}
		}*/
		?>
		
		<?php
	endforeach;
}

function mrm_fl_settings_section_callback(  ) { 
	/** Maybe put some js in here if you want */
}


function mrm_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Feature loader by Maramadi</h2>

		<?php
		settings_fields( 'mrm_fl_plugin_page' );
		do_settings_sections( 'mrm_fl_plugin_page' );
		submit_button();
		?>

	</form>
	<?php
}