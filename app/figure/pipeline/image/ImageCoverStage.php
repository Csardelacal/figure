<?php namespace app\figure\pipeline\image;

use app\figure\pipeline\image\ImagePipelineContext;

class ImageCoverStage implements ImagePipelineStage
{
	
	private ?int $width;
	private ?int $height;
	
	public function __construct(int $width = null, int $height = null)
	{
		$this->width = $width;
		$this->height = $height;
		
		assume(!($width === null && $height === null), 'Invalid dimensions');
	}
	
	public function run(ImagePipelineContext $ctx): ImagePipelineContext
	{
		$x = $this->width?: $ctx->getWidth() / $ctx->getHeight() * $this->height;
		$y = $this->height?: $ctx->getHeight() / $ctx->getWidth() * $this->width;
		
		$wider = ($ctx->getWidth() / $x) > ($ctx->getHeight() / $y);
		
		if ($wider) {
			$ratio    = $ctx->getHeight() / $y;
			# TGX stands for target gravity x. It shows how much the gravity displaces on
			# the x axis.
			$tgx      = $ctx->getGravity()->getX();
			$offset_x = clamp(0, $tgx - $x * $ratio / 2, $ctx->getWidth() - $x * $ratio);
			$offset_y = 0;
		}
		else {
			$ratio    = $ctx->getWidth() / $x;
			# TGY stands for target gravity Y. It shows how much the gravity displaces on
			# the Y axis.
			$tgy      = $ctx->getGravity()->getY();
			$offset_y = clamp(0, $tgy - $y * $ratio / 2, $ctx->getHeight() - $y * $ratio);
			$offset_x = 0;
		}
		
		$img = imagecreatetruecolor($x, $y);
		imagecolortransparent($img, imagecolorallocatealpha($img, 255, 255, 255, 127));
		imagealphablending($img, false);
		imagesavealpha($img, true);
		
		imagecopyresampled(
			$img,
			$ctx->getResource(),
			0, //dstx
			0, //dsty
			$offset_x, //srcx
			$offset_y, //srcy
			$x, //dstw
			$y, //dsth
			$x * $ratio, //srcw
			$y * $ratio //srch
		);
		
		return new ImagePipelineContext($img, $x, $y, 0);
	}
}
