<?php
use google\appengine\api\users\UserService;

class Bootstrap extends Bootstrapbase {

    public function mediaPath($prefix='')
    {
		$path=__DIR__.'/../../x';
		if ($prefix) $path.='/'.$prefix;
		return $path;
    }


    public function getCurrentUser() {
        $email = 'piotr@reseller.webkameleon.com';

        if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
            $user = UserService::getCurrentUser();
            $email=$user->email;
        }


        return md5(str_replace('.','',strtolower($email)));
    }

}