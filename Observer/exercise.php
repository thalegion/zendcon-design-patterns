<?php
namespace MyCompanyShop {

    use SplObserver;
    use SplSubject;

    class Product implements SplSubject
    {
        private $data = [];
        protected $observers;

        public function __get($key)
        {
            return $this->data[$key];
        }

        public function __set($key, $value)
        {
            $this->data[$key] = $value;
            $this->notify();
        }

        public function attach(SplObserver $observer)
        {
            $this->observers[spl_object_hash($observer)] = $observer;
        }

        public function detach(SplObserver $observer)
        {
            $objHash = spl_object_hash($observer);
            if (array_key_exists($objHash, $this->observers)) {
                unset($this->observers[$objHash]);
            }
        }

        public function notify()
        {
            foreach ($this->observers as $observer) {
                $observer->update($this);
            }
        }


    }

    class Logger implements SplObserver
    {
        private $events = [];

        public function getEvents()
        {
            return $this->events;
        }

         function update(SplSubject $subject)
        {
            $this->events[] = var_export($subject, true);
        }


    }
}

namespace {
    use MyCompanyShop\Product,
        MyCompanyShop\Logger;

    $p = new Product;
    $l = new Logger;
    $p->name = 'Test Product 1';
    $p->attach($l);

    assert(count($l->getEvents()) == 0);
    $p->foo = 'bar';
    assert(count($l->getEvents()) == 1);

}