<?php


/**
 * 頁碼產生
 * MTsung by 20180605
 */
namespace MTsung{

	class pageNumber{
		var $console;
		var $conn;
		var $queryName; 		//GET變數名稱
		var $per; 				//每頁顯示數量
		var $pageViewMax; 		//頁碼最多顯示數量
		var $dataCount; 		//總筆數
		var $pageNow; 			//現在頁碼
		var $pagePrevious; 		//上一頁頁碼
		var $pageNext; 			//下一頁頁碼
		var $pageTotal; 		//總頁數
		var $dataStart; 		//開始筆數
		var $newQuery; 			//新query

		function __construct($console,$sql,$per=10,$pageViewMax=5,$queryName='page'){
			$this->setConsole($console);
			$this->conn = $this->console->conn;
			$this->per = $per>100 ? 100 : $per;
			$this->pageViewMax = $pageViewMax;
			$this->queryName = $queryName; 

			// 取得筆數
			if(!strpos($sql,'count(*)')){
				$sql = str_replace('*','count(*)',$sql);
			}

			$temp = $this->conn->GetArray($sql);
			if(count($temp)>1){
				$this->dataCount = count($temp);
			}else{
				$this->dataCount = $temp[0][0];
			}

			$this->pageTotal = ceil($this->dataCount/$this->per);
			
			// 現在頁碼
			if(!isset($_GET[$this->queryName]) || !is_numeric($_GET[$this->queryName])){
				$this->pageNow = 1;
			}else{
				$this->pageNow = intval($_GET[$this->queryName] + 0);
				if($this->pageNow > $this->pageTotal){
					$this->pageNow = $this->pageTotal;
				}else if($this->pageNow < 0){
					$this->pageNow = 1;
				}
			}

			$this->dataStart = ($this->pageNow-1)*$this->per;

			// 重組query 將page刪除
			if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']){
				$temp = explode('&',$_SERVER['QUERY_STRING']);
				$newQuery = "";
				foreach ($temp as $key => $value) {
					$parameter = explode('=',$value);
					if($parameter[0] && isset($parameter[1]) && ($parameter[0] != $this->queryName)){
						$newQuery .= "&".$parameter[0]."=".$parameter[1];
					}
				}
				$this->newQuery = substr_replace($newQuery, '?', 0, 1);
				$this->newQuery .= "&".$queryName."=";
			}else{
				$this->newQuery .= "?".$queryName."=";
			}
			$this->newQuery = explode("?",$_SERVER["REQUEST_URI"])[0].$this->newQuery;

			// 上/下一頁碼
			$this->pagePrevious = $this->pageNow > 1 ? $this->pageNow - 1 : 1;
			$this->pageNext = $this->pageNow < $this->pageTotal ? $this->pageNow + 1 : $this->pageTotal;
			
		}

		/**
		 * 取得每幾筆換頁
		 * @return [type] [description]
		 */
		function getPer(){
			return $this->per;
		}

		/**
		 * 頁碼最多顯示數量
		 * @return [type] [description]
		 */
		function getPageViewMax(){
			return $this->pageViewMax;
		}

		/**
		 * 取得總筆數
		 * @return [type] [description]
		 */
		function getDataCount(){
			return $this->dataCount;
		}

		/**
		 * 取得現在頁碼
		 * @return [type] [description]
		 */
		function getPageNow(){
			return $this->pageNow;
		}

		/**
		 * 取得上一頁頁碼
		 * @return [type] [description]
		 */
		function getPagePrevious(){
			return $this->pagePrevious;
		}

		/**
		 * 取得下一頁頁碼
		 * @return [type] [description]
		 */
		function getPageNext(){
			return $this->pageNext;
		}

		/**
		 * 取得總頁數
		 * @return [type] [description]
		 */
		function getPageTotal(){
			return $this->pageTotal;
		}

		/**
		 * 取得開始筆數
		 * @return [type] [description]
		 */
		function getDataStart(){
			return $this->dataStart;
		}

		/**
		 * 取得新query
		 * @return [type] [description]
		 */
		function getNewQuery(){
			return $this->newQuery;
		}

		/**
		 * 取得page參數陣列
		 * @return [type] [description]
		 */
		function getPageArrayData(){
			$data["per"] = $this->per;
			$data["pageViewMax"] = $this->pageViewMax;
			$data["dataCount"] = $this->dataCount;
			$data["pageNow"] = $this->pageNow;
			$data["pagePrevious"] = $this->pagePrevious;
			$data["pageNext"] = $this->pageNext;
			$data["pageTotal"] = $this->pageTotal;
			$data["dataStart"] = $this->dataStart;
			$data["newQuery"] = $this->newQuery;
			return $data;
		}

		/**
		 * 取得html 
		 * @param  string $class (bootstrap 4語法)
		 * @return [type]        [description]
		 */
		function getHTML($class=''){

			//沒資料不顯示
			if(!$this->pageTotal){
				return;	
			}

			//左右頁碼各數
			$R = ceil($this->pageViewMax/2);
			$L = floor($this->pageViewMax/2);

			//左右補正變數
			$Lcount = ($this->pageTotal - $this->pageNow) < $R ? $R - ($this->pageTotal - $this->pageNow) - 1 : 0;
			$Rcount = 0;

			// 上一頁
			if($class=='bootstrap'){
				echo '
					<nav >
						<ul class="pagination">
							<li class="page-item">
								<a class="page-link" href="'.$this->newQuery.$this->pagePrevious.'" aria-label="Previous">
									<span aria-hidden="true">&laquo;</span>
									<span class="sr-only">Previous</span>
								</a>
							</li>
					';
			}else{
				echo '<a href="'.$this->newQuery.$this->pagePrevious.'">上一頁</a> ';
			}

			//左邊
			for($i = $this->pageNow - $L - $Lcount ; $i < $this->pageNow ; $i++){
				if($i < 1){
					$Rcount++;
				}else{
					if($class=='bootstrap'){
						echo '<li class="page-item"><a class="page-link" href="'.$this->newQuery.$i.'">'.$i.'</a></li>';
					}else{
						echo '<a href="'.$this->newQuery.$i.'">'.$i.'</a> ';
					}
				}
			}

			// 現在頁碼
			if($class=='bootstrap'){
				echo '<li class="page-item active"><a class="page-link">'.$this->pageNow.'</a></li>';
			}else{
				echo '<a>'.$this->pageNow.'</a> ';
			}

			//右邊
			for($i = $this->pageNow + 1 ; $i < $this->pageNow + $R + $Rcount ; $i++){
				if($i <= $this->pageTotal){

					if($class=='bootstrap'){
						echo '<li class="page-item "><a class="page-link" href="'.$this->newQuery.$i.'">'.$i.'</a></li>';
					}else{
						echo '<a href="'.$this->newQuery.$i.'">'.$i.'</a> ';
					}
				}
			}

			//下一頁
			if($class=='bootstrap'){
				echo '
							<li class="page-item">
								<a class="page-link" href="'.$this->newQuery.$this->pageNext.'" aria-label="Next">
									<span aria-hidden="true">&raquo;</span>
									<span class="sr-only">Next</span>
								</a>
							</li>　
					';
			}else{
				echo '<a href="'.$this->newQuery.$this->pageNext.'">下一頁</a>';
			}

			echo '
							<li class="page-item">
								<select class="form-control" onchange="location=this.value;">
			    ';
			for ($i=1; $i <= $this->pageTotal; $i++){ 
				if($this->pageNow == $i){
					echo '<option value="'.$this->newQuery.$i.'" selected>'.$i.'</option>';
				}else{
					echo '<option value="'.$this->newQuery.$i.'" >'.$i.'</option>';
				}
			}
			echo '
			                    </select>
							</li>
						</ul>
					</nav>
				';
	    }

	    /**
	     * 設計師常用
	     * @return [type] [description]
	     */
	    function getHTML1(){
	    	if(!$this->dataCount || $this->pageTotal<2){
	    		return "";
	    	}
	    	$str = '全部共 '.$this->dataCount.'  筆 (每頁 '.$this->per.' 筆) <br>　　'.($this->pageNow>$this->pagePrevious?'<a href="'.$this->newQuery.$this->pagePrevious.'">上一頁</a>':'').'　　'.($this->pageNow<$this->pageNext?'<a href="'.$this->newQuery.$this->pageNext.'">下一頁</a>':'').'　　<br>前往第 <select onchange="location=this.value;">';

	    	for ($i=1; $i <= $this->pageTotal ; $i++) { 
	    		if($i == $this->pageNow){
	    			$str .= '<option value="'.$this->newQuery.$i.'" selected>'.$i.'</option>';
	    		}else{
	    			$str .= '<option value="'.$this->newQuery.$i.'">'.$i.'</option>';
	    		}
	    	}

	    	$str .= '</select>頁';
	    	return $str;
	    }

	    /**
	     * 設定console
	     * @param Mtsung/main $console 
	     */
	    public function setConsole($console){
	    	$this->console = $console;
	    }
	}
}