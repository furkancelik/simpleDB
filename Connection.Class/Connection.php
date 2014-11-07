<?php

include __DIR__."/ConnectionInterface.php";


/**
 * Class Connection
 * @author Furkan Çelik
 * @web http://www.furkancelik.com.tr
 * @email furkan.celik32@gmail.com
 * @date 06 Kasım 2014
 */



class Connection implements ConnectionInterface
{


    private static $instance;
    private $DB;
    private $SQL;
    private $table;
    private $where;
    private $orderBy;
    private $groupBy;
    private $join;
    private $limit;
    private $result;
    private $lastInsertID;
    private $rowCount;


    /**
     * @param $host
     * @param $dbname
     * @param $user
     * @param $password
     * @param $port
     */
    private function __construct($host,$dbname,$user,$password,$port)
    {
        try
        {
            $db = new PDO("mysql:host={$host};port={$port};dbname={$dbname}",$user,$password);
            $db->exec("SET NAMES utf8");
            $this->DB = $db;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }

    }


    /**
     * @param $host
     * @param $dbname
     * @param $user
     * @param $password
     * @param int $port
     * @return Connection
     */
    public static function Connect($host,$dbname,$user,$password,$port=3306)
    {
        if (!self::$instance){
            self::$instance = new Connection($host,$dbname,$user,$password,$port);
        }
        return self::$instance;
    }


    /**
     * @param $sql
     * @param array $param
     * @return PDOStatement
     */
    public function Query($sql,array $param = null)
    {
        $query  = $this->DB->prepare($sql);
        $query->execute($param);
        $this->result = $query;
        return $query;

    }


    /**
     * @param $table
     * @return $this
     */
    public function Select($table)
    {
        $this->SQL ="SELECT * FROM ".$table;
        $this->table = $table;
        return $this;

    }


    /**
     * @param $from
     * @return $this
     */
    public function From($from)
    {
        $this->SQL = str_replace('*',$from,$this->SQL);
        return $this;
    }


    /**
     * @param $column
     * @param string $value
     * @param string $mark
     * @return $this
     */
    public function Where($column,$value="?",$mark = '=')
    {
        if ($value=="")
        {
            $this->where = $column;
            return $this;
        }
        else
        {
            switch ($mark)
            {
                case "LIKE":
                    $this->where = $column." LIKE "."'".$value."'";
                    break;
                case "IN":
                    $valueaar = explode(",",$value);
                    foreach($valueaar as $v){
                        $vaar[] = "'".$v."'";
                    }
                    $value = implode(",",$vaar);
                    $this->where = $column." IN "."(".$value.")";
                    break;
                case "BETWEEN":
                    $valueaar = explode(",",$value);
                    $value = implode("AND",$valueaar);
                    $this->where = "( ".$column." BETWEEN ".$value." )";
                    break;
                default:

                    preg_match("/(and)|(or)|(\()|(\))/i",$column,$a);
                    if (count($a)>0)
                        {
                            $this->where = $column;
                        }
                    else
                        {
                            $this->where = $column.$mark."'".$value."'";
                        }
            }
            return $this;
        }
    }


    /**
     * @param $column
     * @param string $value
     * @param string $mark
     * @return $this
     */
    public function orWhere($column,$value="?",$mark = '=')
    {

        if ($value=="")
        {
            $this->where = $this->where." or ".$column;
            return $this;
        }
        else
        {
            $this->where = $this->where." or ".$column.$mark."'".$value."'";
            return $this;
        }
    }


    /**
     * @param $column
     * @param string $value
     * @param string $mark
     * @return $this
     */
    public function andWhere($column,$value="?", $mark = '=')
    {

        if ($value=="")
        {
            $this->where = $this->where." and ".$column;
            return $this;
        }
        else
        {
            $this->where = $this->where." and ".$column.$mark."'".$value."'";
            return $this;
        }
    }


    /**
     * @param $column
     * @param string $sort
     * @return $this
     */
    public function orderBy($column, $sort = 'DESC')
    {
        switch ($sort)
        {
            case "az":
                $sort = "ASC";
                break;
            case "za":
                $sort = "DESC";
                break;
        }

        $this->orderBy = $this->orderBy." ORDER BY ".$column." ".$sort;
        return $this;
    }


    /**
     * @param $column
     * @return $this
     */
    public function groupBy($column)
    {
        $this->groupBy = $this->groupBy." GROUP BY ".$column;
        return $this;
    }


    /**
     * @param $table
     * @param $column1
     * @param string $mark
     * @param $column2
     * @param string $joinType
     * @return $this
     */
    public function Join($table,$column1,$mark='=',$column2,$joinType="INNER")
    {
        $this->join = $this->join." {$joinType} JOIN ".$table." ON ".$this->table.".{$column1} {$mark} {$table}.{$column2}";
        return $this;
    }


    /**
     * @param $start
     * @param $end
     * @return $this
     */
    public function Limit($start,$last)
    {
        $this->limit = " LIMIT ".$start." , ".$last;
        return $this;
    }


    /**
     * @param array $values
     * @return bool|PDOStatement
     */
    public function get(array $values = null)
    {
        $this->where = str_replace("'?'","?",$this->where);
        if ($this->where){ $this->where = " WHERE ".$this->where; }

        switch (substr($this->SQL,0,6))
        {
            case "SELECT":
                $sql =  $this->SQL.$this->where.$this->orderBy.$this->groupBy.$this->join.$this->limit;
                $data = $values;
                break;
            case "INSERT":
                foreach($values as $k=>$v){
                    $column[] = "`".$k."`";
                    $value[] = "?";
                    $data[] = $v;
                }
                $column = implode(",",$column);
                $value = implode(",",$value);
                $sql =  $this->SQL." ({$column}) VALUES ({$value})";
                break;
            case "UPDATE":
                foreach($values as $k=>$v){
                    if (!is_numeric($k)){
                        $columnT = "`".str_replace("%","",$k)."`"."=?";
                        $columnaar[] = $columnT;

                    }
                    $value[] = "?";
                    $data[] = $v;
                }
                $column = implode(",",$columnaar);
                $sql =  $this->SQL." ".$column.$this->where;
                break;
            case "DELETE":
                $sql =  $this->SQL.$this->where;
                $data = $values;
                break;
        }

        $query = $this->DB->prepare($sql);
        print_r($data);
        $result = $query->execute($data);
        $this->lastInsertID = $this->DB->lastInsertId();
        $this->rowCount = $query->rowCount();
        $this->result = $query;
        if ($result>0){
            return $query;
        }else {
            return false;
        }

    }


    /**
     * @param array $values
     * @param int $returntype
     * @return string
     */
    public function toSql(array $values = null,$returntype=0)
    {
        $this->where = str_replace("'?'","?",$this->where);
        if ($this->where){ $this->where = " WHERE ".$this->where; }

        switch (substr($this->SQL,0,6))
        {
            case "SELECT":
                $sql =  $this->SQL.$this->where.$this->orderBy.$this->groupBy.$this->join.$this->limit;
                $data = $values;
                break;
            case "INSERT":
                foreach($values as $k=>$v){
                    $column[] = "`".$k."`";
                    $value[] = "?";
                    $data[] = $v;
                }
                $column = implode(",",$column);
                $value = implode(",",$value);
                $sql =  $this->SQL." ({$column}) VALUES ({$value})";
                break;
            case "UPDATE":

                foreach($values as $k=>$v){
                    if (!is_numeric($k)){
                        $columnT = "`".str_replace("%","",$k)."`"."=?";
                        $columnaar[] = $columnT;

                    }
                    $value[] = "?";
                    $data[] = $v;
                }
                $column = implode(",",$columnaar);
                $sql =  $this->SQL." ".$column.$this->where;
                break;
            case "DELETE":
                $sql =  $this->SQL.$this->where;
                $data = $values;
                break;
        }

        if (!$returntype)
        {
            return $sql;
        }
        else
        {
            echo $sql;
        }

    }


    /**
     * @echo string
     */
    public function error()
    {
        echo "<pre>";
        echo "Sql Code: ".$this->result->queryString."<br />";
        print_r($this->result->errorInfo());
        echo "</pre>";
    }


    /**
     * @return mixed
     */
    public function lastId()
    {
        return $this->lastInsertID;
    }


    /**
     * @return mixed
     */
    public function rowCount()
    {
        return $this->rowCount;
    }


    /**
     * @param $fetch_style
     * @return mixed
     */
    public function fetch($fetch_style)
    {
        switch ($fetch_style)
        {
            case "ASSOC":
                $x = PDO::FETCH_ASSOC;
                break;
            case "BOTH":
                $x = PDO::FETCH_BOTH;
                break;
            case "BOUND":
                $x = PDO::FETCH_BOUND;
                break;
            case "INTO":
                $x = PDO::FETCH_INTO;
                break;
            case "LAZY":
                $x = PDO::FETCH_LAZY;
                break;
            case "NUM":
                $x = PDO::FETCH_NUM;
                break;
            case "OBJ":
                $x = PDO::FETCH_OBJ;
                break;
            default:
                $x = $fetch_style;
        }
        $a = $this->result;
        return $a->fetch($x);
    }


    /**
     * @param $fetch_style
     * @return mixed
     */
    public function fetchAll($fetch_style)
    {
        switch ($fetch_style)
        {
            case "ASSOC":
                $x = PDO::FETCH_ASSOC;
                break;
            case "BOTH":
                $x = PDO::FETCH_BOTH;
                break;
            case "BOUND":
                $x = PDO::FETCH_BOUND;
                break;
            case "INTO":
                $x = PDO::FETCH_INTO;
                break;
            case "LAZY":
                $x = PDO::FETCH_LAZY;
                break;
            case "NUM":
                $x = PDO::FETCH_NUM;
                break;
            case "OBJ":
                $x = PDO::FETCH_OBJ;
                break;
            default:
                $x = $fetch_style;
        }

        $a = $this->result;
        return $a->fetchAll($x);
    }


    /**
     * @param $table
     * @return $this
     */
    public function Insert($table)
    {
        $this->SQL = "INSERT INTO ".$table;
        return $this;
    }


    /**
     * @param $table
     * @return $this
     */
    public function Delete($table)
    {
        $this->SQL = "DELETE FROM ".$table." ";
        return $this;
    }


    /**
     * @param $table
     * @return $this
     */
    public function Update($table)
    {
        $this->SQL = "UPDATE {$table} SET";
        return $this;
    }



}