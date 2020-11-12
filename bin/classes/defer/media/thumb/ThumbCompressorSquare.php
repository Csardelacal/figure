<?php namespace defer\media\thumb;

use defer\media\Compressor;

/* 
 * The MIT License
 *
 * Copyright 2020 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class ThumbCompressorSquare extends ThumbCompressor
{
	
	public function getOptions() {
		$presets = parent::getOptions();
		$presets[Compressor::SETTING_RATIO_MAX] = 1;
		$presets[Compressor::SETTING_RATIO_MIN] = 1;
		$presets[Compressor::SETTING_HEIGHT_MAX] = 256;
		$presets[Compressor::SETTING_WIDTH_MAX]  = 256;
		return $presets;
	}

	public function getTarget() {
		return \MediaModel::TARGET_THUMB_SQUARE_SMALL;
	}

	public function after($_id) {
		\spitfire\core\async\Async::defer(0, new \defer\media\PosterTask([$_id, 'image/jpeg']));
		\spitfire\core\async\Async::defer(0, new \defer\media\PosterTask([$_id, 'image/webp']));
	}

}
