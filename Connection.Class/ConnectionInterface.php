<?php

/**
 * interface ConnectionInterface
 * @author Furkan Çelik
 * @web http://www.furkancelik.com.tr
 * @email furkan.celik32@gmail.com
 * @date 06 Kasım 2014
 */


interface ConnectionInterface
{

    /**
     * @param $host
     * @param $dbname
     * @param $user
     * @param $password
     * @param $port
     * @return mixed
     */
    public static function Connect($host,$dbname,$user,$password,$port);


    /**
     * @param $sql
     * @param array $param
     * @return mixed
     */
    public function Query($sql,array $param);


    /**
     * @param $table
     * @return mixed
     */
    public function Select($table);


    /**
     * @param $from
     * @return mixed
     */
    public function From($from);


    /**
     * @param $column
     * @param string $value
     * @param string $mark
     * @return mixed
     */
    public function Where($column,$value,$mark);


    /**
     * @param $column
     * @param string $value
     * @param string $mark
     * @return mixed
     */
    public function orWhere($column,$value,$mark);


    /**
     * @param $column
     * @param string $value
     * @param string $mark
     * @return mixed
     */
    public function andWhere($column,$value,$mark);


    /**
     * @param $column
     * @param string $sort
     * @return mixed
     */
    public function orderBy($column,$sort);


    /**
     * @param $column
     * @return mixed
     */
    public function groupBy($column);


    /**
     * @param $table
     * @param $column1
     * @param string $mark
     * @param $column2
     * @param string $joinType
     * @return mixed
     */
    public function Join($table,$column1,$mark,$column2,$joinType);


    /**
     * @param $start
     * @param $end
     * @return mixed
     */
    public function Limit($start,$last);


    /**
     * @param array $values
     * @return mixed
     */
    public function get(array $values);


    /**
     * @param array $values
     * @param int $returntype
     * @return mixed
     */
    public function toSql(array $values,$returntype);


    /**
     * @return mixed
     */
    public function error();


    /**
     * @return mixed
     */
    public function lastId();


    /**
     * @return mixed
     */
    public function rowCount();


    /**
     * @param $fetch_style
     * @return mixed
     */
    public function fetch($fetch_style);


    /**
     * @param $fetch_style
     * @return mixed
     */
    public function fetchAll($fetch_style);


    /**
     * @param $table
     * @return mixed
     */
    public function Insert($table);


    /**
     * @param $table
     * @return mixed
     */
    public function Delete($table);


    /**
     * @param $table
     * @return mixed
     */
    public function Update($table);



}

