<?php
class caseController extends Controller {
    

    public function get()
    {
        $case = new caseModel();
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
                $c['short'] = mb_strtoupper(mb_substr($name[0],0,1,'utf-8'),'utf-8');
                if (count($name)>1)
                    $c['short'].= mb_strtoupper(mb_substr($name[1],0,1,'utf-8'),'utf-8');
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

    public function delete() {
        if ($this->id) {
            $case = new caseModel($this->id);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $case->remove();
            return array('status'=>true);
        }
    }


    public function delete_rubric() {
        if ($this->id) {
            $id=explode(',',$this->id);
            $case = new caseModel($id[0]);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $caser = new caserModel();
            $r=$caser->select(['case'=>$id[0],'rubric'=>$id[1]]);
            if ($r && count($r)>0) {
                $caser->remove($r[0]['id']);
                return array('status'=>true);
            }
            return array('status'=>false);

        }
    }


    public function post_active() {
        if ($this->id) {
            $case = new caseModel($this->id);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $case->lastActivity = time();
            $case->save();
        }
        $this->_set_active($this->id);
    }

    public function post_name() {
        if ($this->id && $this->_getParam('name')) {
            $case = new caseModel($this->id);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $case->lastActivity = time();
            $case->name = $this->_getParam('name');
            $case->save();
        }
        $this->_set_active($this->id);
    }


    public function post() {

        $active = $this->_get_active();

        if (!$active) {
            $case = new caseModel();
            $case->user32=Bootstrap::$main->getCurrentUser();
            $case->lastActivity = time();
        } else {
            $case = new caseModel($active);
            if (!$this->checkRight($case))
                return array('status'=>false,'message'=>'No right');
            $case->lastActivity = time();
        }

        $case->save();
        $this->_set_active($case->id);

        if (!isset($this->data['rubric'])) {
            return array('status'=>false,'message'=>'No rubric');
        }

        $rubric=new rubricModel();
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


    public function get_repertorize() {
        $case = new caseModel($this->id);
        if (!$this->checkRight($case))
            return array('status'=>false,'message'=>'No right');

        $case->lastActivity = time();
        $case->save();
        $this->_set_active($this->id);
        $caser= new caserModel();
        $caser->join('rubric','rubrics');
        $rubrics = $caser->select(['case'=>$case->id],'`id`');

        $rr=new rrModel();
        $rr->join('remedy','remedies');
        $all = [];
        foreach ($rubrics AS &$rubric) {

            $rubric['remedies']=[];
            if (!$rubric['rc']) continue;

            $token='rubric-'.$rubric['rubric'];
            $remedies = Tools::memcache($token);
            if (!$remedies) $remedies = Tools::memcache($token,$rr->select(['rubric'=>$rubric['rubric']]));
            if (!$remedies) continue;
            foreach ($remedies AS $remedy) {
                $remedy_id = $remedy['remedy'];
                if (!isset($all[$remedy_id])) {
                    $all[$remedy_id] = $remedy;
                    $all[$remedy_id]['points']=0;
                    $all[$remedy_id]['rubrics']=[];
                }
                $all[$remedy_id]['points']+=$remedy['weight'] * $rubric['weight'];
                $all[$remedy_id]['rubrics'][$rubric['rubric']] = $remedy['weight'];
            }
        }

        usort($all,function($a,$b){
            if ($a['points']==$b['points']) return 0;
            return $a['points']>$b['points'] ? -1 : 1;
        });

        $all_remedies = [];
        foreach ($all as $r) {
            $all_remedies[] = ['name'=>$r['name'],'score'=>$r['points']];
        }

        foreach ($rubrics AS &$rubric) {
            $rubric['remedies'] = [];
            $id=$rubric['rubric'];

            foreach ($all as $r) {
                $rubric['remedies'][] = isset($r['rubrics'][$id]) ? $r['rubrics'][$id] : 0;
            }

        }

        $ret=['remedies'=>$all_remedies, 'rubrics'=>$rubrics, 'case'=>$case->data()];
        return array('status'=>true,'data'=>$ret);
    }

    
}
