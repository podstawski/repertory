<?php

class Bootstrap extends Bootstrapbase {

    public function mediaPath($prefix='')
    {
		$path=__DIR__.'/../../x';
		if ($prefix) $path.='/'.$prefix;
		return $path;
    }


    public function getCurrentUser() {
        $email = 'piotr.webkameleon.com';

        return md5(str_replace('.','',strtolower($email)));
    }

}