<?php namespace app\glide\ffmpeg;

use app\glide\ffmpeg\manipulators\Size;
use app\glide\ServerFactory as GlideServerFactory;
use FFMpeg\FFMpeg;

class ServerFactory extends GlideServerFactory
{
	
	public function makeServer()
	{
		return new Server(
			$this->getSource(),
			$this->getCache(),
			$this->getApi()
        );
	}
	
	public function getApi()
	{
		return new Api(
			$this->getFFMPEG(),
			$this->getManipulators()
		);
	}
    /**
     * Get Intervention image manager.
     *
     * @return FFMpeg Intervention image manager.
     */
    public function getFFMPEG() : FFMpeg
    {
        return FFMpeg::create();
    }

    /**
     * Get image manipulators.
     *
     * @return array Image manipulators.
     */
    public function getManipulators()
    {
        return [
            new Size($this->getMaxImageSize()),
        ];
    }
}
