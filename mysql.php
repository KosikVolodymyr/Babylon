<?php

    class MySQL {
        private $db;
        private $host,$database,$user,$pass,$charset = 'utf8';
        private $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        private $sqlList = array();
        
        public function __construct($host,$database,$user,$pass) {
            $dsn = "mysql:host=$host;dbname=$database;charset=$this->charset";
            $this->db = new PDO($dsn, $user, $pass, $this->opt);
        }
        
        //param: all, one, obj
        public function select($sql,$data = array(),$param = 'all') {
            $res = $this->execute($sql, $data);
            return $this->parameters($res,$param);
        }
        
        public function insert($sql,$data = array(),$id = false) {
            $this->execute($sql, $data);
            return $id ? $this->db->lastInsertId($id) : true;
        }
        
        public function query($sql,$data = array()) {
            $this->execute($sql, $data);
        }

        //type: select, insert, query;
        //id - return value for insert;
        public function addSQL ($name,$sql,$type = 'select',$id = false) {
                $this->sqlList[$name] = array('obj'=>$this->db->prepare($sql),'type'=>$type,'id'=>$id);
        }
        
        //param: all, one, obj - return for select
        public function execSQL ($name,$data = array(),$param = 'all') {
            $this->sqlList[$name]['obj']->execute($data);
            switch($this->sqlList[$name]['type']){
                case 'select' : 
                    return $this->parameters($this->sqlList[$name]['obj'],$param);
                    break;
                case 'insert' : 
                    return $this->sqlList[$name]['id'] ? $this->db->lastInsertId($this->sqlList[$name]['id']) : true;
                    break;
                case 'query' : 
                    return true;
                    break;
                default : 
                    return true;
                    break;
            }
        }

        private function execute($sql,$data) {
                $res = $this->db->prepare($sql);
                $res->execute($data);
                return $res;
        }
        
        private function parameters($obj,$param){
            if($obj->rowCount()>0){
                switch ($param){
                    case 'all' : 
                        return $obj->fetchAll();
                        break;
                    case 'one' : 
                        return $obj->fetch(); 
                        break;
                    case 'obj' : 
                        return $obj;
                        break;
                    default : 
                        return $obj->fetchAll();
                        break;
                }
            }else{
                return false;
            }
        }
    }
?>