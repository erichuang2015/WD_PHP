<?php


/**
 * 匯出CSV
 * MTsung by 20180912
 */
namespace MTsung{

	class csv{

		/**
		 * 匯出csv
		 * @param  array  $data     [ 0 => [資料array] , 1 => [資料1array]]
		 * @param  [type] $filename 檔案名稱
		 * @return [type]           [description]
		 */
		public function export($data=array(),$filename=DATE){

			$csv = "";
			foreach ($data as $key => $value) {
				$csv .= "\"";
				$value = array_map(function($v){
					return str_replace('"','""',$v);
				}, $value);
				$csv .= implode('","',$value);
				$csv .= "\"\r\n";
			}

			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename={$filename}.csv");
			echo $csv;
			exit;
		}

		/**
		 * 過濾數字key
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		public function filterNumberKey($data){
			foreach ($data as $key => $value) {
				if(!version_compare(PHP_VERSION,'5.6','ge')){
					foreach ($value as $key1 => $value1) {
						if(is_numeric($key1)){
							unset($data[$key][$key1]);
						}
					}
				}else{
					$data[$key] = array_filter($value, function($k) {
					    return !is_numeric($k);
					},ARRAY_FILTER_USE_KEY);
				}
			}
			return $data;
		}
	}
}