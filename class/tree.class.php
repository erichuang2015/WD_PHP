<?php


/**
 * 分類樹
 * MTsung by 20180625
 */
namespace MTsung{

	class tree extends center{

		function __construct($console,$table,$lang=''){
			$this->isTree = true;
			parent::__construct($console,$table,$lang);
		}


		/**
		 * 取得資料
		 * @param  string $whereSql [description]
		 * @param  array  $sqlArray [description]
		 * @return [type]           [description]
		 */
		function getData($whereSql='',$sqlArray=array(),$explodeArray=array(),$module=array()){
			$temp = parent::getData($whereSql,$sqlArray,$explodeArray,$module);
			if(strstr($whereSql,"status")){
				return $this->arrange($temp);
			}else{
				return $temp;
			}
		}

		/**
		 * 分類樹的修改
		 * @param [type] $data [description]
		 */
		function setData($data,$isSetAll=false,$checkArray=array(),$requiredArray=array()){
			$temp1 = parent::setData($data,$isSetAll,$checkArray=array(),$requiredArray=array());
			$temp = $this->message;
			//排序
			$this->sortTable();
			$this->sortTree();
			$this->message = $temp;
			return $temp1;
		}

		/**
		 * 分類樹的列表頁存檔用
		 * @param [type] $data [description]
		 */
		function setDataAll($data,$checkArray=array()){
			$newData = array();
			foreach ($data as $key=>$value) {
				//分解取出id
				$temp = explode("_",$key);
				$dataID = $temp[count($temp)-1];
				unset($temp[count($temp)-1]);
				//重組
				$dataKey = implode("_",$temp);
				
				array_push($newData, array("id" => $dataID, $dataKey => $value));
			}
			$isSet = false;//是否有修改
			foreach ($newData as $key => $value) {
				$isSet |= parent::setData($value,true,$checkArray);
			}
			if($isSet){
				$this->sortTable();
				$this->sortTree();
				$this->message = $this->console->getMessage('EDIT_OK');
			}else{
				$this->message = $this->console->getMessage("DATA_NO_CHANGE");
				return false;
			}
			return true;
		}


		/**
		 * 分類樹的刪除資料 (所有子結點)
		 * @param  [type] $parent id
		 * @return [type]         [description]
		 */
		function rmData($parent){
			$temp = array();
			if(is_array($parent)){
				foreach ($parent as $key => $value) {
					$temp = $this->findChildren($value,$temp);
					parent::rmData($value);
				}
			}
			parent::rmData($temp);
			$this->sortTable();
			$this->sortTree();
			$this->message = $this->console->getMessage('DELETE_OK');
			return true;
		}
		
		/**
		 * 取得所有子節點
		 * @param  [type] $parent   id
		 * @param  array  $children 遞迴用
		 * @return [type]           [description]
		 */
		function findChildren($parent,$children=array()){
			$temp = $this->getData("where parent='".$parent."' order by sort asc");
			if($temp){
				foreach ($temp as $key => $value) {
					$children = $this->findChildren($value["id"],$children);
					array_push($children,$value["id"]);
					$children = array_unique($children);
				}
			}
			return $children;
		}

		/**
		 * 取得所有子節點 (開啟狀態)
		 * @param  [type] $parent   id
		 * @param  array  $children 遞迴用
		 * @return [type]           [description]
		 */
		function findChildren1($parent,$children=array()){
			$temp = $this->getData("where parent='".$parent."' and status='1' order by sort asc");
			if($temp){
				foreach ($temp as $key => $value) {
					$children = $this->findChildren($value["id"],$children);
					array_push($children,$value["id"]);
					$children = array_unique($children);
				}
			}
			return $children;
		}

		/**
		 * 重新排序分類樹
		 * @param  integer $parent [description]
		 * @param  integer $i      [description]
		 * @param  integer $floor  [description]
		 * @return [type]          [description]
		 */
		function sortTree($parent=0,$i=0,$floor=0){
			$temp = $this->conn->GetArray("select * from ".$this->table." where parent='".$parent."' order by sort asc");
			if($temp){
			    foreach ($temp as $key => $value) {
					$this->conn->Execute("update ".$this->table." set step=".$i.",floor='".$floor."' where id='".$value["id"]."'");
					$i = $this->sortTree($value["id"],$i+1,$floor+1);
			    }
			}
		    return $i;
		}

		/**
		 * 母節點關閉子節點連動無法取得data
		 * @param [type] $data [description]
		 */
		function arrange($data){
			if($data !== false){
				$tempAll = array('0');
				$temp = parent::getData(" where status='1' ");
				foreach ($temp as $key => $value) {
					$tempAll[$value["id"]] = $value["id"];
				}
				foreach ($data as $key => $value) {
					if(!in_array($value["parent"], $tempAll)){
						unset($data[$key]);
						unset($tempAll[$value["id"]]);
					}
				}
				$data = array_values($data);
			}
			return $data;
		}

		/**
		 * 取得最大層數
		 * @return [type] [description]
		 */
		function getMaxFloor(){
			return ($this->conn->GetRow("select max(floor) from ".$this->table."")[0]);
		}

		/**
		 * 排序資料表
		 */
		function sortTable(){
			//第0層
			$this->conn->Execute("set @j:=0;");
			$this->conn->Execute("update ".$this->table." set sort=@j:=@j+1 where parent='0' order by sort");

			$maxFloor = $this->getMaxFloor();
			for ($i = 0; $i < $maxFloor ; $i++) { 
				$temp = $this->conn->getArray("select id from ".$this->table." where floor='".$i."' order by sort");
				foreach ($temp as $key => $value) {
					$this->conn->Execute("set @j:=0;");
					$this->conn->Execute("update ".$this->table." set sort=@j:=@j+1 where parent='".$value["id"]."' and floor='".($i+1)."' order by sort");
				}
			}
		}


		/**
		 * 取得樹狀資料 data.next.next...
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		function getTree($data='start'){
			if($data=='start'){
				$data = $this->getData("where status='1' and floor='0' order by step");
			}
			if($data){
				foreach ($data as $key => $value) {
					$data[$key]["next"] = $this->getTree($this->getData("where parent='".$value["id"]."' and status='1' order by step"));
				}
			}
			return $this->console->urlKey($data);
		}
	}
}
