<?php

namespace ShopingCartFramework {
    class Shop {
        protected $shopFactory;
        public function __construct(ShopFactoryInterface $shopFactory) {
            $this->shopFactory = $shopFactory;
        }
        public function listProductsWithShippingCost(array $codes, $shippingMethodName, $miles) {
            $output = array();
            foreach ($codes as $code) {
                $product = $this->shopFactory->createProduct($code);
                $shippingMethod = $this->shopFactory->createShippingMethod($shippingMethodName);
                $output[] = $product->getShopProductCode() . ' - '
                    . $product->getShopDescription() . ' / via: '
                    . $shippingMethod->getName() . ', cost: $'
                    . $shippingMethod->getCostEstimate($miles, $product);
            }
            return implode(PHP_EOL, $output);
        }
    }
    interface ShopFactoryInterface {
        public function createProduct($productCode);
        public function createShippingMethod($name);
    }
    interface ProductInterface {
        public function getShopProductCode();
        public function getShopDescription();
    }
    interface ShippingMethodInterface {
        public function getName();
        public function getCostEstimate($miles, ProductInterface $product);
    }
}

namespace MyCompanyShop {

    use ShopingCartFramework\ShopFactoryInterface;
    use ShopingCartFramework\ProductInterface;
    use ShopingCartFramework\ShippingMethodInterface;

    // @TODO Implement the abstract factory MyShopProductFactory
    class MyShopProductFactory implements ShopFactoryInterface {
        protected $productsData;
        protected $shippingMethodsData;

        public function __construct(Array $productsData, Array $shippingMethodsData) {
            $this->productsData = $productsData;
            $this->shippingMethodsData = $shippingMethodsData;
        }

        public function createProduct($productCode) {
            if (array_key_exists($productCode, $this->productsData)) {
                return new \MyCompanyShop\MyShopProduct(
                    $productCode,
                    $this->productsData[$productCode][0],
                    $this->productsData[$productCode][1]
                );
            }
        }

        public function createShippingMethod($name) {
            if (array_key_exists($name, $this->shippingMethodsData)) {
                return new \MyCompanyShop\MyShippingMethod(
                    $name,
                    $this->shippingMethodsData[$name][0],
                    $this->shippingMethodsData[$name][1]
                );
            }
        }
    }

    // @TODO Implement the product MyShopProduct
    class MyShopProduct implements ProductInterface {
        protected $code;
        protected $description;
        protected $weight;

        public function __construct($code, $description, $weight) {
            $this->code = $code;
            $this->description = $description;
            $this->weight = $weight;
        }

        public function getShopProductCode() {
            return $this->code;
        }

        public function getShopDescription() {
            return $this->description;
        }

        public function getShopWeight() {
            return $this->weight;
        }
    }

    // @TODO Implement Shipping Method MyShippingMethod
    class MyShippingMethod implements ShippingMethodInterface {
        protected $name;
        protected $miles;
        protected $weight;

        public function __construct($name, $miles, $weight) {
            $this->name = $name;
            $this->miles = $miles;
            $this->weight = $weight;
        }

        public function getName() {
            return $this->name;
        }

        public function getCostEstimate($miles, ProductInterface $product) {
            return $miles * $this->miles + $product->getShopWeight() * $this->weight;
        }
    }


}

namespace {
    use ShopingCartFramework\Shop;
    use MyCompanyShop\MyShopProductFactory;

    $productData = [
        // id => [name, weight]
        'BumperSticker1' => ['Bumper Sticker Item #1', 1],
        'BumperSticker3' => ['Bumper Sticker Item #3', 1],
        'BumperSticker5' => ['Bumper Sticker Item #5', 1],
        'CoffeeTableBook6' => ['Coffee Table Book Item #6 (500 pages)', 5],
        'CoffeeTableBook7' => ['Coffee Table Book Item #7 (300 pages)', 3],
        'CoffeeTableBook9' => ['Coffee Table Book Item #9 (900 pages)', 9],
    ];

    $shippingMethodData = [
        // code => [miles, weight]
        'UPS' => [1.4, 1.1],
        'FEXED' => [2.2, 1.3],
    ];

    $shop = new Shop(new MyShopProductFactory($productData, $shippingMethodData));

    $targetOutput = <<<EOS
BumperSticker1 - Bumper Sticker Item #1 / via: UPS, cost: $15.1
CoffeeTableBook6 - Coffee Table Book Item #6 (500 pages) / via: UPS, cost: $19.5
EOS;

    // simulation of Shopping Cart Listings Page

    $actualOutput = $shop->listProductsWithShippingCost(['BumperSticker1', 'CoffeeTableBook6'], 'UPS', 10);

    assert($actualOutput == $targetOutput);
}