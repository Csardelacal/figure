
<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<?php $media = db()->table('media')->get('upload', $upload)->where('cover', false)->setOrder('target', 'ASC')->all(); ?>
		
		<?php foreach ($media as $m): ?>
		<div>
		
			<?php $posters = db()->table('media')->get('upload', $upload)->where('target', $m->target)->where('cover', true)->all(); ?>
			<?php $all     = collect([$m])->add($posters); ?>
			<?php $count   = $all->count() + 1; ?>
			
			<div class="row l<?= $count ?>">
				<div class="span l1"><?= $m->target ?></div>
				
				<?php foreach ($all as $m): ?>
				<div class="span l1">
					<?php if ($m->upload->type === 'animation' && !$m->cover) : ?>
					<video src="<?= $m->url($secret) ?>" loop muted autoplay=""></video>
					<?php endif; ?>

					<?php if ($m->upload->type === 'image' || $m->cover) : ?>
					<img src="<?= $m->url($secret) ?>" >
					<?php endif; ?>
					[<?= str_replace('image/', '', $m->mime) ?>, <?= $m->width ?>x <?= $m->height ?>]<?= $m->cover? '(poster)' : '' ?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>