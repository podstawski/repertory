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
        $dr = new drModel();
        $dr->join('to','rubrics');

        if (is_array($result['results'])) foreach ($result['results'] AS &$r) {

            foreach ($q AS $word) {
                $r['pl'] = str_replace($word,"<b>$word</b>",$r['pl']);
                $r['en'] = str_replace($word,"<b>$word</b>",$r['en']);
            }

            if ($r['dr']) {
                $r['dr'] = $dr->select(['from'=>$r['id']]);
                foreach ($r['dr'] AS &$r2) {
                    foreach ($q AS $word) {
                        $r2['pl'] = str_replace($word, "<b>$word</b>", $r2['pl']);
                        $r2['en'] = str_replace($word, "<b>$word</b>", $r2['en']);
                    }
                }

            } else {
                $r['dr']=false;
            }
        }

        return array('status'=>true,'data'=>Tools::memcache($token,$result));
  
    }
    

    
}
