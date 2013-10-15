<div id="app-error" class="attmsg attmsg32">
	<div class="msg">
		<h1><?php echo $this->module->t($this->pageTitle); ?></h1>
		<p class="text"><?php echo $this->module->t($msg); ?></p>
	</div>
	<div class="desc">
		<?php
		if ($error['code'] == 404 || $error['code'] == 400)
		{
			$this->renderPartial('404');
		}
		if (HelpFunctions::isAdminIp()) var_dump($error);
		?>
	</div>
</div>