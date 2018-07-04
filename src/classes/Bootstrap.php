<?php

class Bootstrap extends Bootstrapbase {

    public function mediaPath($prefix='')
    {
		$path=__DIR__.'/../../x';
		if ($prefix) $path.='/'.$prefix;
		return $path;
    }


}