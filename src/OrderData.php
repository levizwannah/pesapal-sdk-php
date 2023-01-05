<?php 
namespace LeviZwannah\PesapalSdk;

use LeviZwannah\PesapalSdk\Traits\PropertyTrait;

/**
 * Wrapper for order data.
 */
class OrderData {
    use PropertyTrait;

    /**
     * **Required**! This refers to your unique merchant reference generated by 
     * your system. 
     * **Maximum - 50 characters**
     * @var string
     */
    public $id = "";

    /**
     * **Required**! This represents the currency you want to charge your 
     * customers. [ISO Currency Codes](https://en.wikipedia.org/wiki/ISO_4217)
     * @var string
     */
    public $currency = "KES";

    /**
     * Amount to be processed.
     * @var float
     */
    public $amount = 0;

    /**
     * **Required**! Represents the description of your order. 
     * Maximum - 100 characters
     * @var string
     */
    public $description = "";

    /**
     * **Required**! A valid URL which Pesapal will redirect your clients to after 
     * processing the payment.
     * @var string
     */
    public $callback = "";

    /**
     * **Optional**! A valid URL which Pesapal will redirect your clients to incase 
     * they click on cancel request while on the Payment link.
     * @var string
     */
    public $cancelUrl = "";

    /**
     * **Required**! IPN ID for an IPN URL.
     * This represents an IPN URL which Pesapal will send notifications to 
     * after payments have been processed.
     * @var string
     */
    public $ipnId = "";

    /**
     * Customer Address Object
     * @var null|BillingAddress
     */
    public $billingAddress = null;

    /**
     * Converts the order to a data suitable to be send to Pesapal.
     * @return array
     */
    public function data(){
        if($this->billingAddress === null) throw new PesapalException("Billing Address Required");
        return [
            "id" => $this->id,
            "currency" => $this->currency,
            "amount" => $this->amount,
            "description" => $this->description,
            "callback_url" => $this->callback,
            "notification_id" => $this->ipnId,
            "billing_address" => $this->billingAddress->data()
        ];
    }
}
?>