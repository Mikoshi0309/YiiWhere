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
			return $this->arr2str($arr);
        }
    }

    private function arr2str($arr,$type='and'){
        if (isset($arr[0])) {
			if($arr[0] == 'and' || $arr[0] == 'or'){
				$type = $arr[0];
				 array_shift($arr);
				return $this->buildAndCondition($type, $arr);
			}else{
				return $this->CreateSql($arr);
			}
        } else { 
			return $this->buildAndCondition($type, $arr);
        }

    }

    private function CreateSql($arr){
		if(is_string($arr)){
			return $arr;
		}else{
			$count = count($arr);
			switch ($count){
				case 1:
					$key = array_keys($arr);
					$val = array_values($arr);
					return  $key[0].' = \''.$val[0].'\'';
					break;
				case 3:
					if($arr[0] == 'in' || $arr[0] == 'not in'){
						
						return $arr[1].' '.$arr[0].' ( \''.implode('\',\'',$arr[2]).'\' ) ';
					}elseif($arr[0] == 'like'){
						return $arr[1].' '.$arr[0].' \'%'.$arr[2].'%\'';
					}else{
						return $arr[1].' '.$arr[0].' \''.$arr[2].'\'';
					}
				
					break;
				case 4:
					return $arr[1].' '.$arr[0].' '.$arr[2].' and '.$arr[3].'';
					break;
			}
		}
	}
	public function buildAndCondition($operator, $operands)
    {
        $parts = [];
        foreach ($operands as $key=>$operand) {
			if(!is_numeric($key)){
				$operand = $this->CreateSql([$key=>$operand]);
			}
            if (is_array($operand)) {
                $operand = $this->arr2str($operand,$operator);
            }
           
            if ($operand !== '') {
                $parts[] = $operand;
            }
        }
        if (!empty($parts)) {
            return '(' . implode(") $operator (", $parts) . ')';
        } else {
            return '';
        }
    }
        
}

//$arr = ["and",'platform_id'=>1,"platform_id=1",["between", "status", 1, 3],['or',[">=", "platform_id", 1],["between", "status", 1, 3],['and',["like", "platform_id", 1],["between", "status", 1, 3]],['or',[">=", "platform_id", 1],["between", "status", 1, 3]]],["and", ["not in", "platform_id", [1,2,3,4]],["between", "status", 1, 3]]];
//$arr = ['platform_id'=>1,'platform_ids'=>1];
/*$arr = '["and", {
			"user_id": 313
		},
		["or", ["and", ["in", "status", [1, 3, 4, 5]],
				["<=", "created_at", 1517826431]
			],
			["and", {
					"status": 2
				},
				[">=", "roll_out_time", 1517826431]
			]
		]
	]';*/
	$arr = '["and",["<","type_id",997],{"platform_id":1,"user_id":467}]';
	$arr = json_decode($arr,true);
$where = new WhereServer();
echo $where->getWhereStr($arr);


?>
