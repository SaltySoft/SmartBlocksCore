<?php
/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */
/**
 * Model class
 * The model class must be inherited by all models (= doctrine entities)
 * stored in the app/Models folder.
 * This class implements an interface to current doctrine transactions
 * with the database:
 * - Finding, selecting from criterias, taking all entities etc. (Static Functions)
 * - Saving/Updating to the database
 * - Deleting from the database
 */
class Model
{
    protected $_model;
    protected $webservices_attr = array();
    public static $_elastic = false;


    function __construct()
    {
        $this->_model = get_class($this);
        $this->_table = strtolower($this->_model) . "s";
    }

    protected function before_save()
    {

    }

    protected function after_save()
    {

    }

    /**
     *
     * @static
     * @return All the entities from the caller model
     */
    static function all($params = array())
    {
        $class = get_called_class();
        $ob = new $class();
        if (!$ob::$_elastic)
        {
            $dql = "SELECT b FROM " . get_called_class() . " b";
            $list = $GLOBALS["em"]->createQuery($dql)->getResult();
            return $list;
        }
        else
        {
            $client = new \Elasticsearch\Client();
            $searchParams = array();
            $searchParams['index'] = INDEX;
            $searchParams['type'] = str_replace("\\", "_", $class);

            foreach ($params as $key => $param)
            {
                if (is_object($param))
                {
                    if (is_subclass_of($param, "Model"))
                    {
                        $searchParams['body']['query']['match'][$key.'.id'] = $params[$key]->getId();
                    }
                }
                else
                {
                    $searchParams['body']['query']['match'][$key] = $param;
                }
            }


            $retDoc = $client->search($searchParams);
            $objects = array();

            foreach ($retDoc["hits"]["hits"] as $ret)
            {
                $object = self::objectFromElastic($ret, $class);
                $objects[] = $object;

            }

            return $objects;
        }
    }

    static function objectFromElastic($ret, $class)
    {
        $object = new  $class();


        $reflectionClass = new ReflectionClass($object);


        foreach ($ret["_source"] as $key => $value)
        {
            if ($reflectionClass->hasProperty($key))
            {
                $prop = $reflectionClass->getProperty($key);
                if (!$prop->isPublic())
                {
                    $prop->setAccessible(true);
                }
                if (is_array($value))
                {
                    if (isset($value["id"]) && isset($value["class"]))
                    {
                        $subobject = $value["class"]::find($value["id"]);
                        $prop->setValue($object, $subobject);
                    }
                    else
                    {
                        $prop->setValue($object, $value);
                    }
                }
                else if ($date = \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $value) !== false)
                {
                    $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $value);
                    $prop->setValue($object, $date);
                }
                else
                {

                    $prop->setValue($object, $value);
                }
                if (!$prop->isPublic())
                {
                    $prop->setAccessible(false);
                }
            }
            else
            {
                if ($reflectionClass->hasProperty("data"))
                {

                    $prop = $reflectionClass->getProperty("data");
                    if (!$prop->isPublic())
                    {
                        $prop->setAccessible(true);
                    }
                    $data = json_decode($prop->getValue($object), true);
                    $data[$key] = $value;
                    $prop->setValue($object, json_encode($data));
                    if (!$prop->isPublic())
                    {
                        $prop->setAccessible(false);
                    }
                }
            }


        }

        $prop = $reflectionClass->getProperty("id");
        if (!$prop->isPublic())
        {
            $prop->setAccessible(true);
        }
        $prop->setValue($object, $ret["_id"]);
        if (!$prop->isPublic())
        {
            $prop->setAccessible(false);
        }


        return $object;
    }

    /**
     * @static
     * @param $id The id of the wanted entity
     * @return The wanted entity
     */
    static function find($id)
    {
        $class = get_called_class();
        $ob = new $class();
        if (!$ob::$_elastic)
        {

            return $GLOBALS["em"]->find(get_called_class(), $id);
        }
        else
        {

            $getParams = array();
            $getParams['index'] = INDEX;
            $getParams['type'] = str_replace("\\", "_", $class);
            $getParams['id'] = $id;
            $client = new \Elasticsearch\Client();
            $ret = $client->get($getParams);
            $object = self::objectFromElastic($ret, $class);

            return $object;
        }
    }

    private static function add_where_statement($where, $i, $field)
    {
        if ($where != "")
        {
            $where .= " AND ";
        }
        $where .= " b." . $field . " = ?" . $i;
        return $where;
    }

    /**
     * Returns all the entities that verify the given array of parameters
     * @static
     * @param array $where Parameters filter
     * @return array
     */
    static function where($where = array())
    {
        $class = get_called_class();
        $ob = new $class();
        if (!$ob::$_elastic)
        {
            $array = new \Doctrine\Common\Collections\ArrayCollection();
            //$array = $GLOBALS["em"]->getRepository(get_called_class())->findBy($where);
            $qb = $GLOBALS["em"]->createQueryBuilder();
            $qb->add("select", "b")->add("from", get_called_class() . " b");
            $statement = "";
            $i = 1;
            $vals = array();
            foreach ($where as $field => $value)
            {
                if ($statement != "")
                {
                    $statement .= " AND ";
                }
                $statement .= " b." . $field . " = ?" . $i;
                $vals[$i] = $value;
                $i++;
            }
            $qb->add("where", $statement);
            foreach ($vals as $k => $v)
            {
                $qb->setParameters(array($k => $v));
            }
            $array = $qb->getQuery()->getResult();
            return $array;
        }
        else
        {
            $params = $where;
            $client = new \Elasticsearch\Client();
            $searchParams = array();
            $searchParams['index'] = INDEX;
            $searchParams['type'] = str_replace("\\", "_", $class);

            foreach ($params as $key => $param)
            {
                if (is_object($param))
                {
                    if (is_subclass_of($param, "Model"))
                    {
                        $searchParams['body']['query']['bool']["must"][] = array("match" => array($key => $params[$key]->getId()));
                    }
                }
                else
                {
                    $searchParams['body']['query']['bool']["must"][] = array("match" => array($key => $param));
                }
            }


            $retDoc = $client->search($searchParams);
            $objects = array();

            foreach ($retDoc["hits"]["hits"] as $ret)
            {
                $object = self::objectFromElastic($ret, $class);
                $objects[] = $object;
            }

            return $objects;
        }
    }

    /**
     * Returns an array of entities corresponding to the given options
     * options:
     * -where: array("fieldname" => "value") --> exactly that value
     * -cond: array(array("fieldname" => "value"), "operator") --> specific operator
     * -like: array("fieldname" => "value") --> contains that value
     * -begins: array("fieldname" => "value") --> begins with that value
     * -ends: array("fieldname" => "value") --> ends with that value
     * -paginated: boolean --> determines if results should be paginated
     * -pagesize: integer --> if paginated, amount of results per page
     * -page: integer --> if paginated, paginated results to get
     * @static
     * @param array $options options to give
     * @return array entities
     */
    static function search($options = array())
    {
        global $em;
        $qb = $em->createQueryBuilder();
        $where = "";
        $vals = array();
        $i = 1;
        $dql = "SELECT b FROM " . get_called_class() . " b";
        $qb->add("select", "b")->add("from", get_called_class() . " b");
        if (isset($options["where"]))
        {
            $dql = "SELECT b FROM " . get_called_class() . " b WHERE ";
            foreach ($options["where"] as $key => $value)
            {
                //$qb->add("where", "b." . $key . " = ?1");
                //$qb->setParameters(array(1 => $value));
                $where = self::add_where_statement($where, $i, $key);
                $vals[$i] = $value;
                $i++;
            }
        }
        if (isset($options["cond"]))
        {
            $dql = "SELECT b FROM " . get_called_class() . " b WHERE ";
            $array = $options["cond"];
            $operator = $array[1];
            foreach ($array[0] as $key => $value)
            {
                //$qb->add("where", "b." . $key . " ".$operator." ?1");
                //$qb->setParameters(array(1 => $value));
                if ($where != "")
                    $where .= " AND ";
                $where .= " b." . $key . " " . $operator . " ?" . $i;
                $vals[$i] = $value;
                $i++;
            }
        }
        if (isset($options["like"]))
        {
            foreach ($options["like"] as $key => $value)
            {
                //$qb->add("where", "b." . $key . " LIKE ?1");
                //$qb->setParameters(array(1 => "%" . $value . "%"));
                if ($where != "")
                    $where .= " AND ";
                $where .= " UPPER(b." . $key . ") LIKE ?" . $i;
                $vals[$i] = "%" . strtoupper($value) . "%";
                $i++;
            }
        }
        if (isset($options["begins"]))
        {
            foreach ($options["begins"] as $key => $value)
            {
                // $qb->add("where", "UPPER(b." . $key . ") LIKE ?1");
                // $qb->setParameters(array(1 => strtoupper($value) . "%"));
                if ($where != "")
                    $where .= " AND ";
                $where .= " UPPER(b." . $key . ") LIKE ?" . $i;
                $vals[$i] = strtoupper($value) . "%";
                $i++;
            }
        }
        if (isset($options["ends"]))
        {
            foreach ($options["ends"] as $key => $value)
            {
                //$qb->add("where", "UPPER(b." . $key . ") LIKE ?1");
                //o$qb->setParameters(array(1 => "%" . strtoupper($value)));
                if ($where != "")
                    $where .= " AND ";
                $where .= " UPPER(b." . $key . ") LIKE ?" . $i;
                $vals[$i] = "%" . strtoupper($value);
                $i++;
            }
        }
        if (isset($options["order"]))
        {
            $qb->add("orderBy", "b." . $options["order"]);
        }
        if (isset($options["paginated"]))
        {
            if (isset($options["pagesize"]))
            {
                $pagesize = $options["pagesize"];
            }
            else
            {
                $pagesize = 15;
            }
            if ($options["paginated"] == true)
            {
                if (isset($options["page"]))
                {
                    $page = $options["page"];
                }
                else
                {
                    $page = 1;
                }
                $qb->setFirstResult(($page - 1) * $pagesize);
                $qb->setMaxResults($pagesize);
            }
        }
        $qb->add("where", $where);
        foreach ($vals as $k => $v)
        {
            $qb->setParameters(array($k => $v));
        }
        $list = $qb->getQuery()->getResult();
        return $list;
    }

    static function query($dql)
    {
        global $em;
        $query = $em->createQuery($dql);
        $list = $query->getResult();
        return $list;
    }

    /**
     * Creates the entity in the database if it doesn't exist yet
     * Saves the entity's changes to the database if it already exists
     */
    function save()
    {
        $this->before_save();
        $class = get_called_class();
        $ob = new $class;
        if (!$ob::$_elastic)
        {
            $GLOBALS["em"]->persist($this);
            $GLOBALS["em"]->flush();
        }
        else
        {
            $client = new \Elasticsearch\Client();
            $stored_array = $this->getPersistArray();

            $params = array(
                "index" => INDEX,
                "type" => str_replace("\\", "_", get_class($this)),
                "body" => $stored_array
            );
            if ($stored_array["id"] != null)
            {
                $params["id"] = $stored_array["id"];
            }
            unset($stored_array["id"]);

            $ret = $client->index($params);

            if ($ret["ok"] == 1)
            {
                $reflectionClass = new ReflectionClass($this);
                $prop = $reflectionClass->getProperty("id");
                if (!$prop->isPublic())
                    $prop->setAccessible(true);
                $prop->setValue($this, $ret["_id"]);
                if (!$prop->isPublic())
                    $prop->setAccessible(false);
            }
        }
        $this->after_save();
    }

    protected function getPersistArray()
    {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties();
        $stored_array = array();

        foreach ($properties as $prop)
        {

            if (!$prop->isPublic())
                $prop->setAccessible(true);
            $value = $prop->getValue($this);
            if (is_object($value))
            {

                if (get_class($value) == 'DateTime')
                {

                    $stored_array[$prop->getName()] = $value->format('Y-m-d\TH:i:s.uO');
                }
                else
                {
                    $stored_array[$prop->getName()] = array(
                        "id" => $value->id,
                        "class" => get_class($value)
                    );
                }
            }
            else
            {
                $stored_array[$prop->getName()] = $value;

            }
            if (!$prop->isPublic())
                $prop->setAccessible(false);
        }
        return $stored_array;
    }

    /**
     * Deletes the entity from the database
     */
    function delete()
    {
        $this->before_save();
        $class = get_called_class();
        $ob = new $class;
        if (!$ob::$_elastic)
        {
            $GLOBALS["em"]->remove($this);
            $GLOBALS["em"]->flush();
        }
        else
        {
            $client = new \Elasticsearch\Client();
            $deleteParams = array();
            $deleteParams['index'] = INDEX;
            $deleteParams['type'] = str_replace("\\", "_", $class);
            $deleteParams['id'] = $this->id;
            $client->delete($deleteParams);
        }
    }

    static function count()
    {
        $qb = $GLOBALS["em"]->createQueryBuilder();
        $qb->select("COUNT(b)")
            ->from(get_called_class(), "b");
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    function getWSattr()
    {
        return array_merge($this->webservices_attr, array("id"));
    }

    function __destruct()
    {
    }

    //new feature 0.5
    static function xmlRenderObject($object, $arb = "")
    {
        $xml = $arb . "<" . get_class($object) . ">\n";
        $classInfo = new ReflectionClass(get_class($object));
        //$xml .= $arb . "  <id>$object->id</id>\n";
        foreach ($classInfo->getMethods(ReflectionProperty::IS_PUBLIC) as $key)
        {
            $k = strtolower(str_replace("get", "", $key->getName()));
            if (preg_match("#get#", $key->getName()))
            {
                $methodInfo = new ReflectionMethod(get_class($object), $key->getName());
                $value = $methodInfo->invoke($object);
                if (in_array($k, $object->getWSattr()))
                {
                    if (is_object($value) && get_class($value) == "DateTime")
                    {
                        $xml .= $arb . "  <$k>\n";
                        $xml .= $arb . "  <day>" . $value->format("d") . "</day>\n";
                        $xml .= $arb . "  <month>" . $value->format("m") . "</month>\n";
                        $xml .= $arb . "  <year>" . $value->format("Y") . "</year>\n";
                        $xml .= $arb . "  <hour>" . $value->format("H") . "</hour>\n";
                        $xml .= $arb . "  <minute>" . $value->format("i") . "</minute>\n";
                        $xml .= $arb . "  <second>" . $value->format("s") . "</second>\n";
                        $xml .= $arb . "</$k>\n";
                    }
                    else
                    {
                        $xml .= $arb . "  <$k>$value</$k>\n";
                    }
                }
            }
        }
        $xml .= $arb . "</" . get_class($object) . ">\n";
        return $xml;
    }

    //new feature 0.5
    static function xmlRenderCollection($collection, $arb = "  ")
    {
        $xml = $arb . "<" . get_class($collection[0]) . "s>\n";
        foreach ($collection as $object)
        {
            $xml .= $arb . Model::xmlRenderObject($object, $arb . "  ");
        }
        $xml .= $arb . "</" . get_class($collection[0]) . "s>\n";
        return $xml;
    }

    public function setLastUpdated($time)
    {
        //Override
    }

    /**
     * @return array
     * Gets an array of the object, to ease webservices creation.
     */
    public function toArray()
    {
        $array = get_object_vars($this);
        $final_array = array();
        foreach ($array as $key => $value)
        {
            if (in_array($key, $this->webservices_attr))
            {
                if (!is_object($value))
                {
                    $final_array[$key] = $value;
                }
                else
                {
                    $final_array[$key] = $value->getId();
                }
            }
        }
        return $final_array;
    }

    /**
     * Returns the entity manager
     */
    public static function getEntityManager()
    {
        return ($GLOBALS["em"]);
    }

    public static function persist(Model $object)
    {
        $em = $GLOBALS["em"];
        $object->setLastUpdated(time());
        $object->before_save();
        $em->persist($object);
    }

    public static function flush()
    {
        $em = $GLOBALS["em"];
        $em->flush();
    }
}
