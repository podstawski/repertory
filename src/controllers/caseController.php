<?php
class caseController extends Controller {
    

    public function get()
    {
        $case = new casesModel();
        $cases = $case->select(['user32'=>Bootstrap::$main->getCurrentUser()],'lastActivity DESC', $this->_getParam('limit',0));
        $caser = new caserModel();

        if (count($cases)>0) {
            if (time()-$cases[0]['lastActivity'] < 1800 ) {
                $this->_set_active($cases[0]['id']);
            }
        }
        foreach ($cases AS &$c) {
            $c['rubrics'] = $caser->count(['case'=>$c['id']]);
            $c['active'] = $c['id']==$this->_get_active();
            $c['short']='';
            if ($c['name']) {
                $name=explode(' ',$c['name']);
                $c['short'] = mb_strtolower(mb_substr($name[0],0,1,'utf-8'),'utf-8');
                if (count($name)>1)
                    $c['short'].= mb_strtolower(mb_substr($name[1],0,1,'utf-8'),'utf-8');
            }

        }

        return array('status'=>true,'data'=>$cases);
  
    }

    protected function checkRight($case) {
        if ($case->user32==Bootstrap::$main->getCurrentUser()) return true;

        return false;
    }

    protected function _set_active($active=null) {
        Bootstrap::$main->session('active_case',$active);
    }

    protected function _get_active() {
        return Bootstrap::$main->session('active_case');
    }


    public function post_active() {
        if ($this->id) {
            $case = new casesModel($this->id);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $case->lastActivity = time();
            $case->save();
        }
        $this->_set_active($this->id);
    }

    public function post() {

        $active = $this->_get_active();

        if (!$active) {
            $case = new casesModel();
            $case->user32=Bootstrap::$main->getCurrentUser();
            $case->lastActivity = time();
        } else {
            $case = new casesModel($active);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $case->lastActivity = time();
        }

        $case->save();
        $this->_set_active($case->id);

        if (!isset($this->data['rubric'])) {
            return array('status'=>false,'message'=>'No rubric');
        }

        $rubric=new rubricsModel();
        $rub=$rubric->find_one_by_id32($this->data['rubric']);

        if (!$rub){
            return array('status'=>false,'message'=>'No valid rubric');
        }

        $caser = new caserModel();
        $cr=$caser->select(['rubric'=>$rub['id'],'case'=>$case->id]);

        if (!$cr) {
            $caser->rubric = $rub['id'];
            $caser->case = $case->id;
            $caser->weight = 1;
            $caser->save();
        }

        $ret=$case->data();

        $ret['rubrics'] = $caser->count(['case'=>$case->id]);

        return array('status'=>true,'data'=>$ret);

    }



    
}
