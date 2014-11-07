<?php

/**
 * Created By Furkan
 * @author Furkan Çelik
 * @web http://www.furkancelik.com.tr
 * @email furkan.celik32@gmail.com
 * @date 06 Kasım 2014
 *
 **/


//Include Class File
include __DIR__."/Connection.Class/Connection.php";



//Creating a database connection
 /*
  * $DB = Connection::Connect(HOST,DBNAME,Username,Password,DBPort=3306);
  *
  * */
$DB = Connection::Connect("localhost","deneme","root","");




//Using the Query method
/*
 * $DB->Query(SQL,array VALUE=Null);
 *
 * */
$Query = $DB->Query("SELECT * FROM uye WHERE username=?",array("furkan"));

//Using the error Method
/*
 * $DB->error()
 *
 * Screen prints Sql Code and errors
 * */

$DB->error();


//Using the rowCount Method
/*
 * $Query->rowCount()
 *
 * Screen prints total row count
 * */

$Query->rowCount();


//Using the fetch and fetchAll method
/*
 *
 * $DB->fetch(fetch style) or $DB->fetchAll(fetch style)
 *
 * fetch style = PDO::Fetch Metod or
 *
 * ASSOC
 * BOTH
 * BOUND
 * INTO
 * LAZY
 * NUM
 * OBJ
 *
 *
 * */
$user = $DB->fetch("OBJ");
echo $user->id;
echo $user->username;


//Using the Insert method
/*
 * $DB->Insert(table name)->get(array = null);
 * $DB->Insert("table")->get(array(column name => value,column name => value , ... ));
 *
 * */
$Query = $DB->Insert("uye")->get(array("username"=>"furkan"));
/*
 * LastID
 *
 * */
echo $DB->lastId();





//Using the Update method
/*
 * $DB->Update(table name)->Where(column name , value , mark = '=')->get(array = null);
 * $DB->Update("table")->Where("id","?")->get(array(column name => value,column name => value , ... , where column value));
 *
 * if the database column name of the get method if the number is in the key of the array is %3 in the form of use
 *
 *  usersdb:
 *  |id|username|password|1|
 *  |1|furkan   | *****  |0|
 *  |2|ahmet    | *****  |0|
 *  |3|simge    | *****  |0|
 *  |4|elif     | ****   |0|
 *  |5|hasan    | ****   |0|
 *
 * $Query = $DB->Update("usersdb")->Where("id","?,?,?","IN")->get(array("%1"=>5,1,3,4));
 *
 * updated table
 *
 * usersdb:
 *  |id|username|password|1|
 *  |1|furkan   | *****  |5|
 *  |2|ahmet    | *****  |0|
 *  |3|simge    | *****  |5|
 *  |4|elif     | ****   |5|
 *  |5|hasan    | ****   |0|
 *
 *
 *
 * */
$Query = $DB->Update("uye")->Where("id","?","=")->get(array("username"=>"furkan",3));
if ($Query){ echo "success";} else {echo "fail";}




//Using the Delete method
/*
 * $DB->Delete(table name)->Where(column name , value , mark = '=')->get(array = null);
 * $DB->Delete("table")->Where("id","?")->get(array(value));
 *
 * */
$Query = $DB->Delete("uye")->Where("id","?","=")->get(array(1));
if ($Query){ echo "success";} else {echo "fail";}



//Using the Select method
/*
 * $DB->Select(table name)->get(array = null);
 * $DB->Select("table")
 *                      ->From("username,password")
 *                      ->Where("id","?")
 *                      ->orWhere("age","?",">")
 *                      ->andWhere("city","?")
 *                      ->orderBy("id","az") # az , za , ASC , DESC ( az = ASC , za = DESC  )
 *                      ->groupBy("age")
 *                      ->Join("tablename","column1","=","column2","INNER")
 *                      ->Limit(0,5)
 *                      ->get();
 *
 * */

$Query = $DB->Select("uye")
                    ->Where("name","?","LIKE")
                    ->orWhere("age","?",">")
                    ->andWhere("city","?")
                    ->orderBy("id","az") # az , za , ASC , DESC ( az = ASC , za = DESC  )
                    ->groupBy("age")
                    ->Join("countrytable","country","=","country","INNER")
                    ->Limit(0,5)->get(array("%a%",20,"Turkey"));

/*
 * rowCount
 *
 * */
echo $DB->rowCount();



// Where the method of use detailed
/*
 *
 * ...->Where($column,$value,$mark)->...
 * $column = the table column
 * $value = default value ? ( ? it is recommended that you use the value )
 * $mark = default value =
 * $mark the values
 * LIKE
 * IN
 * BETWEEN
 *
 * The where method on your own, you can write
 *
 * ..->Where("age>'20' and (city='Isparta' and city='Ankara')")->...
 *
 * orWhere and andWhere methods are not allowed to write
 *
 * */


// orderBy the method of use detailed
/*
 * ..->orderBy($column,$sort)
 * $column = the table column
 * $sort = az , za , ASC , DESC
 *
 * */


// groupBy the method of use detailed
/*
 * ..->groupBy($column)
 * $column = the table column
 *
 * */


// Join the method of use detailed
/*
 * ..->Join($table,$column1,$mark,$column2,$joinType)
 * $table= to be merged table
 * $column1 = in the first table on any column
 * $mark = mark =
 * $column2 = in the second table on any column
 * $joinType = the default value INNER possible values;
 * LEFT
 * RIGHT
 * FULL OUTER
 *
 * */



// Limit the method of use detailed
/*
 * ..->Limit($start,$last)
 * $start = the first value
 * $last = the last value
 *
 * */




// get the method of use detailed
/*
 * ..->get(array $values = null)
 *
 * in this method, a query is run and values set.
 *
 *
 * */



// toSql the method of use detailed
/*
 * sql query code creates.
 * ..->toSql(array $values = null,$returntype=0)
 * $values = query values
 * $returntype = two takes value  (0 = return sql query, 1 echo sql query )
 *
    $DB->Select("users")
                ->From("username,password,city as c")
                ->Where("id","?,?,?,?,?","IN")
                ->toSql(array(1,2,3,4,5),1);
 *
 * screen output
 *
 * SELECT username,password,city as c FROM users WHERE id IN (?,?,?,?,?)
 *
 * */
echo $DB->Select("users")
    ->From("username,password,city as c")
    ->Where("id","?,?,?,?,?","IN")
    ->toSql(array(1,2,3,4,5));






















