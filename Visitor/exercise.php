<?php
/**
 * Visitor design pattern (example)
 *
 * @see http://css.dzone.com/books/practical-php-patterns/practical-php-patterns-visitor
 */
abstract class InputValue
{
    private $_value;

    public function __construct($value)
    {
        $this->set($value);
    }

    public function set($value)
    {
        $this->_value = $value;
    }

    public function get()
    {
        return $this->_value;
    }

    public abstract function acceptVisitor(Visitor $visitor);
}

/**
 * A ConcreteElement. Accepts a Visitor and forwards to it on its specialized method.
 */
class SingleInputValue extends InputValue
{
    public function acceptVisitor(Visitor $visitor)
    {
        $visitor->visitSingleInputValue($this);
    }
}

/**
 * Another ConcreteElement. 
 */
class MultipleInputValue extends InputValue
{
    public function acceptVisitor(Visitor $visitor)
    {
        $visitor->visitMultipleInputValue($this);
    }
}

/**
 * The Visitor participant. Again, interface or abstract classes are equivalent.
 */
interface Visitor
{
    /**
     * Since in PHP there is no simple mechanism for method overloading,
     * the visit() methods must not only have different parameters
     * but different names too.
     */
    public function visitSingleInputValue(SingleInputValue $inputValue);
    public function visitMultipleInputValue(MultipleInputValue $inputValue);
}

/**
 * A ConcreteVisitor.
 * Filters all the values provided casting them to integers.
 */
class IntFilter implements Visitor
{
    public function visitSingleInputValue(SingleInputValue $inputValue)
    {
        $inputValue->set(intval($inputValue->get()));
    }

    public function visitMultipleInputValue(MultipleInputValue $inputValue)
    {
        $data = $inputValue->get();
        foreach ($data as &$field) {
            $field = intval($field);
        }
        $inputValue->set($data);
    }
}

/**
 * Another ConcreteVisitor.
 * Sorts multiple values.
 */
class AscendingSort implements Visitor
{
    public function visitSingleInputValue(SingleInputValue $inputValue)
    {
        throw new Exception("Only for multiple values");
    }

    public function visitMultipleInputValue(MultipleInputValue $inputValue)
    {
        $data = $inputValue->get();
        asort($data);
        $inputValue->set($data);
    }
}

$userId = new SingleInputValue("42");
$categories = new MultipleInputValue(array('hated' => 16, 'ordinary' => 23, 'preferred' => 15));
$userId->acceptVisitor(new IntFilter);
printf("%s<br>", is_int($userId->get()) ? "OK" : "ERROR");
$categories->acceptVisitor(new AscendingSort);
$result = array_values($categories->get());
if ($result === array(15, 16, 23)) {
    echo "OK";
} else {
    echo "ERROR";
}

