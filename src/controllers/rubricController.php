<?php
class rubricController extends Controller {
    

    public function get()
    {

        $q=explode(' ',$this->_getParam('q'));
        if (count($q)==1 && $q[0]=='')
            return array('status'=>false);

        foreach ($q AS $i=>$qq) {
            $q[$i] = mb_strtolower(trim($qq),'utf-8');
            if (!strlen($q[$i])) unset($q[$i]);
        }

        sort($q);

        $token=implode(",",$q);
        if ($result=Tools::memcache($token))
            return array('status'=>true,'data'=>$result);

        $model=new rubricsModel();
        $result=$model->search($q);

        return array('status'=>true,'data'=>Tools::memcache($token,$result));
  
    }
    

    
}
