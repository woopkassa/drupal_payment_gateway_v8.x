<?php

namespace Drupal\Wooppay\PluginForm;

use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\wooppay\Soap\CashCreateInvoiceByServiceRequest;
use Drupal\wooppay\Soap\CoreLoginRequest;
use Drupal\wooppay\Soap\WooppaySoapClient;
use Exception;

class RedirectCheckoutForm extends PaymentOffsiteForm
{

	public function buildConfigurationForm(array $form, FormStateInterface $form_state)
	{
		$form = parent::buildConfigurationForm($form, $form_state);
		$configuration = $this->getConfiguration();

		/** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
		$payment = $this->entity;

		$data['api_url'] = $configuration['api_url'];
		$data['api_username'] = $configuration['api_username'];
		$data['api_password'] = $configuration['api_password'];
		$data['service_name'] = $configuration['service_name'];
		$data['order_prefix'] = $configuration['order_prefix'];
		try {
			$login_request = new CoreLoginRequest();
			$login_request->username = $data['api_username'];
			$login_request->password = $data['api_password'];
			$client = new WooppaySoapClient($data['api_url']);
			if ($client->login($login_request)) {
				global $base_url;
				$invoice_request = new CashCreateInvoiceByServiceRequest();
				$invoice_request->serviceName = $data['service_name'];
				$invoice_request->referenceId = $this->createOrderId();
				$invoice_request->addInfo = 'Оплата заказа №' . $payment->getOrderId();
				$invoice_request->backUrl = $form['#return_url'];
				$invoice_request->requestUrl = $form['#return_url'];
				$invoice_request->amount = $payment->getAmount()->getNumber();
				$invoice_data = $client->createInvoice($invoice_request);
			}
		} catch (Exception $exception) {
			throw new PaymentGatewayException('Payment failed!');
		}
		return $this->buildRedirectForm(
			$form,
			$form_state,
			$invoice_data->response->operationUrl,
			[],
			PaymentOffsiteForm::REDIRECT_GET
		);
	}

	public function onReturn(OrderInterface $order, Request $request)
	{
		if ($request->something_that_marks_a_failure) {
			throw new PaymentGatewayException('Payment failed!');
		}

		$payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
		$payment = $payment_storage->create([
			'state' => 'completed',
			'amount' => $order->getTotalPrice(),
			'payment_gateway' => $this->entityId,
			'order_id' => $order->id(),
			'remote_id' => $request->request->get('remote_id'),
			'remote_state' => $request->request->get('remote_state'),
		]);

		$payment->save();
	}

	private function createOrderId()
	{
		/** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
		$payment = $this->entity;

		$configuration = $this->getConfiguration();
		$order_id = $payment->getOrderId();

		// Ensure that Order number is at least 4 characters otherwise QuickPay will reject the request.
		if (strlen($order_id) < 4) {
			$order_id = substr('000' . $order_id, -4);
		}

		if ($configuration['order_prefix']) {
			$order_id = $configuration['order_prefix'] . $order_id;
		}

		return $order_id;
	}

	private function getConfiguration()
	{
		/** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
		$payment = $this->entity;

		/** @var \Drupal\wooppay\Plugin\Commerce\PaymentGateway\RedirectCheckout $payment_gateway_plugin */
		$payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();
		return $payment_gateway_plugin->getConfiguration();
	}
}