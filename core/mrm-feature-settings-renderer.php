<?php

Class MrmFeatureSettingsRenderer {

	static function render($feature, $object, $box)
	{
		$active = $feature->getSetting('active') === 'on' ? 'checked' : '' ?>
		<h4>
			<?php echo $feature->name ?>

			<input type="checkbox"
			id="mrm-fl-<?php echo $feature->slug ?>-active"
			name="mrm_fl_features[<?php echo $feature->slug?>][active]"
			<?php echo $active ?>
			>
		</h4>
		<h5><?php echo $feature->description ?></h5>
		<?php
		foreach ($feature->getSettings() as $fieldType => $settingsData):?>

			<div class="mrm-feature <?php echo $feature->slug . '--class' ?>">
				<?php $inputId = $feature->slug . '-' . $settingsData['slug'] ?>

				<?php if($fieldType === 'text'): ?>

					<label for="<?php echo $inputId ?>"><?php echo $settingsData['label'] ?></label>
					<input
						type="text"
						value="<?php echo $feature->getSetting($settingsData['slug']) ?>"
						name="<?php echo $feature->getSettingsKey($settingsData['slug']) ?>"
						id="<?php echo $inputId ?>"
					>

				<?php endif; ?>
			</div>
		<?php endforeach;
	}
}
