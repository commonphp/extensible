<?php

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionType;

#[ExtensionType(singleton: false, allowPreloading: false, requireInterface: PaymentGatewayInterface::class)]
class PaymentGateway extends AbstractExtension {}

interface PaymentGatewayInterface {
    public function charge(float $amount): bool;
}

#[PaymentGateway('stripe')]
class StripeGateway implements PaymentGatewayInterface {
    public function charge(float $amount): bool {
        echo "Processing payment of $amount via Stripe.";
        return true;
    }
}

#[PaymentGateway('paypal')]
class PayPalGateway implements PaymentGatewayInterface {
    public function charge(float $amount): bool {
        echo "Processing payment of $amount via PayPal.";
        return true;
    }
}

// Register and use payment gateways
$store->typeRegistry->register(PaymentGateway::class);
$store->registry->register(PaymentGateway::class, StripeGateway::class);
$store->registry->register(PaymentGateway::class, PayPalGateway::class);

$gateway = $store->get(StripeGateway::class);
$gateway->charge(50.00);
