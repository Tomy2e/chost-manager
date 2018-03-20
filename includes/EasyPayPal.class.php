<?php

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

class EasyPayPal
{
    /*
    Source : 
    https://developer.paypal.com/docs/api/quickstart/environment/
    */
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
              PAYPAL_CLIENT_ID,
              PAYPAL_CLIENT_SECRET
            )
          );

          $this->apiContext->setConfig(
            array(
              'mode' => 'sandbox'
            ));
    }

    public function nouveauPaiement($returnUrl, $cancelUrl, $total, $description)
    {
        // Create new payer and method
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // Set redirect urls
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)
        ->setCancelUrl($cancelUrl);

        // Set payment amount
        $amount = new Amount();
        $amount->setCurrency("EUR")
        ->setTotal(floatval($total));

        // Set transaction object
        $transaction = new Transaction();
        $transaction->setAmount($amount)
        ->setDescription($description);

        // Create the full payment object
        $payment = new Payment();
        $payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));

        // Create payment with valid API context
        try {
            $payment->create($this->apiContext);
        
            // Get PayPal redirect URL and redirect user
            $approvalUrl = $payment->getApprovalLink();
        
            // REDIRECT USER TO $approvalUrl
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            die($ex);
        }

        return $approvalUrl;
    }

    public function finaliserPaiement($payment_id, $payer_id)
    {

        // Get payment object by passing paymentId
        $paymentId = $payment_id;
        $payment = Payment::get($paymentId, $this->apiContext);
        $payerId = $payer_id;

        // Execute payment with payer id
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
        // Execute payment
        $result = $payment->execute($execution, $this->apiContext);
        //var_dump($result);

        //print_r($result->getTransactions()[0]);

        return array("success"=>true, "montant"=>floatval($result->getTransactions()[0]->getAmount()->getTotal()));

        //print_r($result->getTransactions()[0]->getAmount());
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
        //echo $ex->getCode();
        return array("success"=>false, "code"=>$ex->getCode());
        //die($ex);
        } catch (Exception $ex) {
        die($ex);
        }
    }

}