<?php

namespace MyShop
{

    class Database
    {
        public function query()
        {
            return array('1', '2', '3');
        }
    }

    class DatabaseConstructorConsumer
    {
        protected $db;

        public function __construct(Database $db)
        {
            $this->db = $db;
        }

        public function doSomething()
        {
            return implode(', ', $this->db->query());
        }
    }

    class DatabaseSetterConsumer
    {
        public function setDatabase(Database $db)
        {
            $this->db = $db;
        }

        public function doSomething()
        {
            return implode(', ', $this->db->query());
        }
    }
}

namespace
{

    use MyShop\Database;
    use MyShop\DatabaseConstructorConsumer;
    use MyShop\DatabaseSetterConsumer;

    // constructor injection
    $consumer = new DatabaseConstructorConsumer(new MyShop\Database);
    assert($consumer->doSomething() == '1, 2, 3');

    // setter injection
    $consumer = new DatabaseSetterConsumer;
    $consumer->setDatabase(new MyShop\Database);
    assert($consumer->doSomething() == '1, 2, 3');

}