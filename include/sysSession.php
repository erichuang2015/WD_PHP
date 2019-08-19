<?php

    set_time_limit(0);
/**
 * 使用DB存取session
 */
class sysSession implements SessionHandlerInterface{
    private $conn;
    private $table;
    
    function __construct($conn,$table){
        $this->conn = $conn;
        $this->table = $table;
    }

    /**
     * 連線
     * @param  [type] $savePath    [description]
     * @param  [type] $sessionName [description]
     * @return [type]              [description]
     */
    public function open($savePath, $sessionName){
        if($this->conn){
            return true;
        }
        return false;
    }

    /**
     * 關閉連線
     * @return [type] [description]
     */
    public function close(){
        return true;
    }

    /**
     * 讀取
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function read($id){
        $data = $this->conn->getRow($this->conn->Prepare("SELECT sessionData FROM ".$this->table." WHERE sessionId=?"),[$id]);
        return $data['sessionData'];
    }

    /**
     * 寫入
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function write($id, $data){
        $DateTime = date('Y-m-d H:i:s');
        if($this->conn->Execute($this->conn->Prepare("REPLACE INTO ".$this->table." SET sessionId=?,sessionUpdateDate=?,sessionData=?"),[$id,$DateTime,$data])){
            return true;
        }
        return false;
    }

    /**
     * 銷毀
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function destroy($id){
        if($this->conn->Execute($this->conn->Prepare("DELETE FROM ".$this->table." WHERE sessionId=?"),[$id])){
            return true;
        }
        return false;
    }

    /**
     * 系統自動回收
     * @param  [type] $maxlifetime [description]
     * @return [type]              [description]
     */
    public function gc($maxlifetime){
        $DateTime = date('Y-m-d H:i:s');
        if($this->conn->Execute("DELETE FROM ".$this->table." WHERE ((UNIX_TIMESTAMP(sessionUpdateDate) + ".$maxlifetime.") < ".strtotime($DateTime).")")){
            return true;
        }
        return false;
    }
}

$_sessionTable = PREFIX."session";
if(!$conn->GetArray("desc ".$_sessionTable)){
    $conn->Execute("
        CREATE TABLE `".$_sessionTable."` (
          `sessionId` varchar(255) NOT NULL,
          `sessionData` text,
          `sessionUpdateDate` timestamp NOT NULL,
          PRIMARY KEY (`sessionId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    $conn->Execute("
        CREATE INDEX INDEX_NAME ON `".$_sessionTable." (sessionId);
    ");
    return false;
}

$_handler = new sysSession($conn,$_sessionTable);
session_set_save_handler($_handler, true);
session_start();