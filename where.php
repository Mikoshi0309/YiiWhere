<?php
/**
 * Created by PhpStorm.
 * User: Miko
 * Date: 3/20/18
 * Time: 12:29 AM
 */
class WhereServer{
    public $str='';
    public function getWhereStr($arr){
        if(empty($arr)){
            return false;
        }
        if(is_string($arr)){
            return $arr;
        }
        if(is_array($arr)){
           $this->arr2str($arr);
        }
        return substr($this->str,0,-4);
    }

    private function arr2str($arr,$type='and'){
        if(!empty($type)){
            $this->str .= '( ';
        }
        foreach ($arr as $k=>$v) {
			if(!is_numeric($k)){
				$this->CreateSql([$k=>$v],$type);
				continue;
			}
		
            if ($v == 'and' || $v == 'or') {
                $filter = array_shift($arr);
                $this->arr2str($arr,$filter);
				break;
            }elseif( $v[0] == 'and' || $v[0] == 'or'){
				$filter = array_shift($v);
                $this->arr2str($v,$filter);
				continue;
			} else {
                $this->CreateSql($v,$type);
            }
        }
        if(!empty($type)){
			$str_arr = explode(' ',$this->str);
			$last_str = $str_arr[count($str_arr)-2];
			$this->str = substr($this->str,0,-1*strlen($last_str)-1);
            $this->str .= ' ) and ';
        }

    }

    private function CreateSql($arr,$type=''){
		if(is_string($arr)){
			$this->str .= $arr.' '.$type.' ';
		}else{
			$count = count($arr);
			switch ($count){
				case 1:
					$key = array_keys($arr);
					$val = array_values($arr);
					$this->str .= $key[0].' = "'.$val[0].'" '.$type.' ';
					break;
				case 3:
					if($arr[0] == 'in' || $arr[0] == 'not in'){
						$this->str .= $arr[1].' '.$arr[0].' ( "'.implode('","',$arr[2]).'" ) '.$type.' ';
					}elseif($arr[0] == 'like'){
						$this->str .= $arr[1].' '.$arr[0].' "%'.$arr[2].'%" '.$type.' ';
					}else{
						$this->str .= $arr[1].' '.$arr[0].' "'.$arr[2].'" '.$type.' ';
					}
				
					break;
				case 4:
					$this->str .= $arr[1].' '.$arr[0].' '.$arr[2].' and '.$arr[3].' '.$type.' ';
					break;
			}
		}
	}
	
        
}

$arr = ["and",'platform_id'=>'asdasd',"platform_id=1",["between", "status", 1, 3],['or',[">=", "platform_id", 1],["between", "status", 1, 3],['and',["like", "platform_id", 1],["between", "status", 1, 3]]],["and", ["not in", "platform_id", [1,2,3,4]],["between", "status", 1, 3]]];
//$arr = ['platform_id'=>1,'platform_ids'=>2];
$where = new WhereServer();
echo $where->getWhereStr($arr);

?>
