<?php namespace app\figure\pipeline\video;

use app\figure\pipeline\video\VideoPipelineContext as Context;
use spitfire\collection\Collection;

/**
 * A pipeline has a series of stages. These stages are used to assemble
 * a modified version of a video.
 */
class VideoPipeline
{
	
	/**
	 *
	 * @var Collection<VideoPipelineStage>
	 */
	private Collection $stages;
	
	public function __construct()
	{
		$this->stages = new Collection();
	}
	
	/**
	 * A pipeline stage can apply changes to an image.
	 */
	public function push(VideoPipelineStage $stage) : void
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
			fn (Context $image, VideoPipelineStage $stage) : Context => $stage->run($image),
			$image
		);
	}
}
