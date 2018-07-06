<?php
class rubricModel extends Model {
	protected $_table='rubrics';

	public function search(Array $arrayQ,$maxResults=100) {

	    $result=['total'=>0,'results'=>null];

	    $against='';
	    foreach ($arrayQ AS $q) {
            $q=mb_strtolower($q,'utf-8');
	        $q=preg_replace('~[^a-ząśćżźółńę]*~','',$q);
	        if (mb_strlen($q, 'utf-8')<3) continue;
            $against.="+$q* ";
        }
        $against=trim($against);

	    if (!strlen($against))
	        return $result;

        $where="match(pl,en) against ('$against' IN BOOLEAN MODE)";

        $sql="SELECT count(*) FROM ".$this->_table." WHERE $where";

        $result['total']=$this->conn->fetchOne($sql);
        if ($result['total']==0 || $result['total']>$maxResults)
            return $result;

	    $sql="SELECT * FROM ".$this->_table." WHERE $where";
        $result['results']=$this->conn->fetchAll($sql);

        return $result;

    }

}
