<?php namespace app\figure\pipeline\image;

use app\figure\pipeline\image\ImagePipelineContext as Context;
use spitfire\collection\Collection;

/**
 * A pipeline has a series of stages. These stages are used to assemble
 * a modified version of an image.
 */
class ImagePipeline
{
	
	/**
	 *
	 * @var Collection<ImagePipelineStage>
	 */
	private Collection $stages;
	
	public function __construct()
	{
		$this->stages = new Collection();
	}
	
	/**
	 * A pipeline stage can apply changes to an image.
	 */
	public function push(ImagePipelineStage $stage) : void
	{
		$this->stages->push($stage);
	}
	
	/**
	 * Apply the different stages that this pipeline contains to the
	 * provided image.
	 */
	public function apply(Context $image) : Context
	{
		return $this->stages->reduce(
			fn (Context $image, ImagePipelineStage $stage) : Context => $stage->run($image),
			$image
		);
	}
}
