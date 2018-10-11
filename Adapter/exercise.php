<?php
namespace MyCompanyShop {

    class PayPal
    {
        private $email;
        private $password;

        public function __construct($email, $password)
        {
            $this->email = $email;
            $this->password = $password;
        }

        public function transfer($email, $amount)
        {
            return "Paypal Success!";
        }

    }

    class CreditCard
    {
        private $number;
        private $expiration;

        public function __construct($number, $expiration)
        {
            $this->number = $number;
            $this->expiration = $expiration;
        }

        public function authorizeTransaction($amount)
        {
            return "Authorization code: 234da";
        }
    }

    interface PaymentAdapterInterface
    {
        /**
         * @param $amount
         * @return boolean
         */
        public function collectMoney($amount);
    }

    class CrediCardAdapter implements PaymentAdapterInterface
    {
        protected $creditCard;

        public function __construct(CreditCard $creditCard)
        {
            $this->creditCard = $creditCard;
        }

        public function collectMoney($amount)
        {
            $this->creditCard->authorizeTransaction($amount);
            return true;
        }
    }

    class PayPalAdapter implements PaymentAdapterInterface
    {
        protected $paypal;

        public function __construct(PayPal $paypal)
        {
            $this->paypal = $paypal;
        }

        public function collectMoney($amount)
        {
            $this->paypal->transfer('', $amount);
            return true;
        }

    }
}

namespace {
    use MyCompanyShop\PayPal,
        MyCompanyShop\PayPalAdapter,
        MyCompanyShop\CrediCardAdapter,
        MyCompanyShop\CreditCard;

    $paypal = new PayPal('customer@aol.com', 'password');
    $cc = new CreditCard(1234567890123456, "09/12");

    $paypalAdapter = new PayPalAdapter($paypal);
    $ccAdapter = new CrediCardAdapter($cc);

    assert($paypalAdapter->collectMoney(50));
    assert($ccAdapter->collectMoney(50));

}