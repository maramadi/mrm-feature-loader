<?php

Class MrmFeatureSettingsFieldRenderer {

	public static function text($data)
	{?>
		<input 
			type="<?php echo $fieldType ?>" 
			value="<?php echo $feature->getSetting('item_class') ?>"
			name="<?php echo $settingsKey ?>"
		>
	<?php
	}
}