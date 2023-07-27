<?php namespace app\glide\ffmpeg;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use League\Glide\Api\ApiInterface;

class Api implements ApiInterface
{
	
	private FFMpeg $ffmpeg;
	private array $manipulators;
	
	public function __construct(FFMpeg $ffmpeg, array $manipulators)
    {
        $this->setFFMPEG($ffmpeg);
        $this->setManipulators($manipulators);
    }
	
	public function run($source, array $params)
	{
		/**
		 * @var Video
		 */
        $image = $this->ffmpeg->open($source);
		
        foreach ($this->manipulators as $manipulator) {
            $manipulator->setParams($params);
            $image = $manipulator->run($image);
        }
		
		$tmpfilename = tempnam(sys_get_temp_dir(), 'ffmpeg') . '.mp4';
		
		/**
		 * This uses WebM, for it's superior support accross browsers and lack of
		 * proprietary bs.
		 * https://bugzilla.mozilla.org/show_bug.cgi?id=1368063
		 * 
		 * The set-alt-ref parameter is required to appropriately remove transparency
		 * from GIF files.
		 */
		$format = new WebM();
		$format->setAdditionalParameters(array('-auto-alt-ref', '0'));
		
		$image->save($format, $tmpfilename);
		return file_get_contents($tmpfilename);
	}

	/**
	 * Get the value of ffmpeg
	 *
	 * @return FFMpeg
	 */
	public function getFFMPEG(): FFMpeg
	{
		return $this->ffmpeg;
	}

	/**
	 * Set the value of ffmpeg
	 *
	 * @param FFMpeg $ffmpeg
	 *
	 * @return self
	 */
	public function setFFMPEG(FFMpeg $ffmpeg): self
	{
		$this->ffmpeg = $ffmpeg;

		return $this;
	}

	/**
	 * Get the value of manipulators
	 *
	 * @return array
	 */
	public function getManipulators(): array
	{
		return $this->manipulators;
	}

	/**
	 * Set the value of manipulators
	 *
	 * @param array $manipulators
	 *
	 * @return self
	 */
	public function setManipulators(array $manipulators): self
	{
		$this->manipulators = $manipulators;

		return $this;
	}
}
