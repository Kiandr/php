<div class="rewmodule <?=$this->getId() . (!empty($this->config['wrap_class']) ? ' ' . $this->config['wrap_class'] : ''); ?>" id="<?=$this->getUID(); ?>">
	<?php if ($this->config['show_heading']) : ?>
	   <div class="rewmodule_title"><?=$this->config['heading']; ?></div>
	<?php endif;?>
	<div class="rewmodule_content">