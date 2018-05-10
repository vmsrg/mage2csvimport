<?php 
use Ajgl\Csv\Rfc;
use Ajgl\Csv\Rfc\CsvRfcUtils;


	class CsvWriter{
		protected $_fields=array();
		public $_file_path;
		public $_separator;
		public $_handle;

		public $_count = 0;
		public $_filenum = 0;
		public $_count_max = 1000;
		public $write_header = 0;

		public function __construct ($file_path,$flush=false){
			$this->_file_path=$file_path;
			$this->_separator=';';
			$mode=$flush?"w":"a";

			$exist=file_exists ($file_path);

			$this->_handle = fopen($file_path, $mode) or die("can't open file");
			CsvRfcUtils::setDefaultWriteEol(CsvRfcUtils::EOL_WRITE_DEFAULT);
			
			if(! $exist){

			}
		}

		public function setFields($fields=array()){
			$this->_fields=$fields;
		}

		public function writeHeader(){
			$record=array();
			foreach ($this->_fields as $key=>$value) {
				$record[]=$value;
			}
			Rfc\fputcsv($this->_handle, $record, $this->_separator);
			$this->write_header = 1;
		}

		public function write($record){
			$new_record = array();
			foreach ($this->_fields as $key) {
				$new_record[] = isset($record[$key])?trim($record[$key]):"";
			}
			Rfc\fputcsv($this->_handle, $new_record, $this->_separator);

			$this->_count++;
			if($this->_count > $this->_count_max){
				$this->_count = 0;
				$this->_filenum ++;
				fclose($this->_handle);

				$file_path = str_replace('.csv', '--'.$this->_filenum.'.csv', $this->_file_path);
				$this->_handle = fopen($file_path, "a");
				if($this->write_header){
					$this->writeHeader();
				}
			}
			
		}

		public function __destruct (){
			fclose($this->_handle);
		}

		function fputcsv_eol($handle, $array, $delimiter = ',', $enclosure = '"', $eol = "\n") {
		    $return = fputcsv($handle, $array, $delimiter, $enclosure);
		    if($return !== FALSE && "\n" != $eol && 0 === fseek($handle, -1, SEEK_CUR)) {
		        fwrite($handle, $eol);
		    }
		    return $return;
		}

	}
?>