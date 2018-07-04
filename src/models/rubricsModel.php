<?php
class rubricsModel extends Model {
	protected $_table='rubrics';

	public function search(Array $arrayQ,$maxResults=100) {

	    $where=[];
	    $where_v=[];
	    $result=['total'=>0,'results'=>null];

	    foreach ($arrayQ AS $q) {
            $where[] = "match(pl,en) against (? IN natural language mode)";
            $where_v[]=mb_strtolower($q,'utf-8');
        }

        $where=implode(' AND ',$where);

        $sql="SELECT count(*) FROM ".$this->_table." WHERE $where";

        $result['total']=$this->conn->fetchOne($sql,$where_v);
        if ($result['total']==0 || $result['total']>$maxResults)
            return $result;



	    $sql="SELECT * FROM ".$this->_table." WHERE $where";

        $result['results']=$this->conn->fetchAll($sql,$where_v);

        return $result;

    }

}
