<?php


/**
 * 匯出CSV
 * MTsung by 20180912
 */
namespace MTsung{
    include_once(APP_PATH.'include/PhpSpreadsheet/autoload.php');
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;


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
					return str_replace(array("\r", "\n", "\r\n", "\n\r"), '', str_replace('"','""',$v));
				}, $value);
				$csv .= implode('","',$value);
				$csv .= "\"\r\n";
			}

            header('Pragma: no-cache');
            header('Content-Encoding: UTF-8');
            header('Expires: 0');
			header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment; filename={$filename}.csv");
			echo "\xEF\xBB\xBF";
			echo $csv;
			exit;
		}


		/**
		 * 匯出xls
		 * @param  array  $data     [ 0 => [資料array] , 1 => [資料1array]]
		 * @param  [type] $filename 檔案名稱
		 * @return [type]           [description]
		 */
		public function export_xls($data=array(),$filename=DATE){
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			//資料寫入
			$maxRow = "A";//最大row
			$col = 1;
			foreach ($data as $one) {
				$row = "A";
				foreach ($one as $value) {
					$sheet->setCellValue($row.$col, $value);
					$row++;//$row="Z" ，$row++ 會是 "AA"
				}
				$maxRow = $row;
				$col++;
			}

			//設定寬度
			for ($i = "A"; $i != $maxRow; $i++) { 
				$sheet->getColumnDimension($i)->setAutoSize(true);
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
			header('Cache-Control: max-age=0');

			$writer = IOFactory::createWriter($spreadsheet, 'Xls');
			$writer->save('php://output');
			exit;
		}

	}
}