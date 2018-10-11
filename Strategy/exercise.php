<?php
namespace MyCompanyShop {

    class Product {
        public $name;
        public $listPrice;
        public $sellingPrice;
        public $manufacturer;
    }

    class ProductCollection {

        private $products = array();

        public function __construct(array $products) {
            $this->products = $products;
        }

        /**
         * @param ProductFilteringStrategy $filterStrategy
         * @return ProductCollection
         */
        public function filter(ProductFilteringStrategy $filterStrategy) {
            $filteredProducts = array();

            foreach ($this->getProductsArray() as $product) {
                if ($filterStrategy->filter($product)) {
                    $filteredProducts[] = $product;
                }
            }
            return new ProductCollection($filteredProducts);
        }

        public function getProductsArray() {
            return $this->products;
        }
    }

    interface ProductFilteringStrategy {
        /**
         * @param Product $product
         * @return true|false
         */
        public function filter(Product $product);
    }

    class MaxPriceFilter implements ProductFilteringStrategy
    {
        private $price;

        public function __construct($price)
        {
            $this->price = $price;
        }

        public function filter(Product $product)
        {
            return $product->listPrice <= $this->price;
        }
    }

    class ManufacturerFilter implements ProductFilteringStrategy
    {
        private $manufacturer;

        public function __construct($manufacturer)
        {
            $this->manufacturer = $manufacturer;
        }

        public function filter(Product $product)
        {
            return $product->manufacturer == $this->manufacturer;
        }
    }

}

namespace {

    use MyCompanyShop\Product;
    use MyCompanyShop\ProductCollection;
    use MyCompanyShop\ManufacturerFilter;
    use MyCompanyShop\MaxPriceFilter;

    $p1 = new Product;
    $p1->listPrice = 100;
    $p1->sellingPrice = 50;
    $p1->manufacturer = 'WidgetCorp';

    $p2 = new Product;
    $p2->listPrice = 100;
    $p2->manufacturer = 'Widgetron, LLC';

    $collection = new ProductCollection([$p1, $p2]);

    $resultCollection = $collection->filter(new ManufacturerFilter('Widgetron, LLC'));

    assert(count($resultCollection->getProductsArray()) == 1);
    assert($resultCollection->getProductsArray()[0]->manufacturer == 'Widgetron, LLC');


    $resultCollection = $collection->filter(new MaxPriceFilter(50));

    assert(count($resultCollection->getProductsArray()) == 1);
    assert($resultCollection->getProductsArray()[0]->manufacturer == 'WidgetCorp');

}
