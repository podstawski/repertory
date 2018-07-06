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
        $result=Tools::memcache($token);
        if ($result && !$this->_getParam('debug'))
            return array('status'=>true,'data'=>$result);

        $model=new rubricModel();
        $result=$model->search($q);

        if (is_array($result['results'])) foreach ($result['results'] AS &$r) {
            foreach ($q AS $word) {
                $r['pl'] = str_replace($word,"<b>$word</b>",$r['pl']);
                $r['en'] = str_replace($word,"<b>$word</b>",$r['en']);
            }

        }

        return array('status'=>true,'data'=>Tools::memcache($token,$result));
  
    }
    

    
}
