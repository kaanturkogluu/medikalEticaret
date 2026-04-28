<?php

namespace App\Services;

use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Options;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Request\RetrieveCheckoutFormRequest;

class IyzicoService
{
    protected $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->setApiKey(config('iyzico.api_key'));
        $this->options->setSecretKey(config('iyzico.secret_key'));
        $this->options->setBaseUrl(config('iyzico.base_url'));
    }

    public function createForm($order, $items)
    {
        $request = new CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        \Illuminate\Support\Facades\Log::info('Iyzico createForm: Setting ConversationId to ' . $order->id);
        $request->setConversationId((string)$order->id);
        $request->setPrice($order->total_price);
        $request->setPaidPrice($order->total_price);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId("B" . $order->id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl(route('iyzico.callback'));

        $buyer = new Buyer();
        $buyer->setId(auth()->id() ?? 0);
        $buyer->setName(explode(' ', $order->customer_name)[0]);
        $buyer->setSurname(explode(' ', $order->customer_name)[1] ?? '-');
        $buyer->setGsmNumber($order->customer_phone);
        $buyer->setEmail($order->customer_email);
        $buyer->setIdentityNumber("11111111111"); // Dummy or from user
        $buyer->setRegistrationAddress($order->address_info['address']);
        $buyer->setIp(request()->ip());
        $buyer->setCity($order->address_info['city']);
        $buyer->setCountry("Turkey");
        $request->setBuyer($buyer);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($order->customer_name);
        $shippingAddress->setCity($order->address_info['city']);
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress($order->address_info['address']);
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new Address();
        $billingAddress->setContactName($order->customer_name);
        $billingAddress->setCity($order->address_info['city']);
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($order->address_info['address']);
        $request->setBillingAddress($billingAddress);

        $basketItems = [];
        foreach ($items as $item) {
            $basketItem = new BasketItem();
            $basketItem->setId($item['product_id']);
            $basketItem->setName($item['name']);
            $basketItem->setCategory1("Medical");
            $basketItem->setItemType(BasketItemType::PHYSICAL);
            $basketItem->setPrice($item['price'] * $item['quantity']);
            $basketItems[] = $basketItem;
        }
        
        // If there is shipping, add it as a basket item
        if ($order->shipping_price > 0) {
            $shippingItem = new BasketItem();
            $shippingItem->setId("SHIPPING");
            $shippingItem->setName("Kargo Ücreti");
            $shippingItem->setCategory1("Shipping");
            $shippingItem->setItemType(BasketItemType::VIRTUAL);
            $shippingItem->setPrice($order->shipping_price);
            $basketItems[] = $shippingItem;
        }

        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = CheckoutFormInitialize::create($request, $this->options);

        return $checkoutFormInitialize;
    }

    public function getPaymentStatus($token)
    {
        $request = new RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setToken($token);

        return CheckoutForm::retrieve($request, $this->options);
    }
}
