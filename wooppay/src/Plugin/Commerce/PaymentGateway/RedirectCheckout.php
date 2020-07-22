<?php

namespace Drupal\Wooppay\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the Wooppay offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "wooppay_redirect_checkout",
 *   label = @Translation("Wooppay (Redirect to wooppay.com)"),
 *   display_label = @Translation("Wooppay"),
 *    forms = {
 *     "offsite-payment" = "Drupal\wooppay\PluginForm\RedirectCheckoutForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "mastercard", "visa",
 *   },
 * )
 */
class RedirectCheckout extends OffsitePaymentGatewayBase
{

	public function defaultConfiguration()
	{
		return [
				'api_url' => 'https://www.test.wooppay.com/api/wsdl',
				'api_username' => '',
				'api_password' => '',
				'service_name' => '',
				'order_prefix' => '',
			] + parent::defaultConfiguration();
	}

	public function buildConfigurationForm(array $form, FormStateInterface $form_state)
	{
		$form = parent::buildConfigurationForm($form, $form_state);

		$form['api_url'] = [
			'#type' => 'textfield',
			'#title' => $this->t('API URL'),
			'#description' => $this->t('This is api url from the Wooppay manager.'),
			'#default_value' => $this->configuration['api_url'],
			'#required' => TRUE,
		];

		$form['api_username'] = [
			'#type' => 'textfield',
			'#title' => $this->t('API Username'),
			'#description' => $this->t('This is api username from tbe Wooppay manager.'),
			'#default_value' => $this->configuration['api_username'],
			'#required' => TRUE,
		];

		$form['api_password'] = [
			'#type' => 'textfield',
			'#title' => $this->t('API Password'),
			'#description' => $this->t('This is api password from tbe Wooppay manager.'),
			'#default_value' => $this->configuration['api_password'],
			'#required' => TRUE,
		];

		$form['service_name'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Service Name'),
			'#description' => $this->t('This is service name from tbe Wooppay manager.'),
			'#default_value' => $this->configuration['service_name'],
			'#required' => TRUE,
		];

		$form['order_prefix'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Order Prefix'),
			'#description' => $this->t('You can customize your order from this site by prefix.'),
			'#default_value' => $this->configuration['order_prefix'],
			'#required' => TRUE,
		];

		return $form;
	}

	public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
	{
		parent::submitConfigurationForm($form, $form_state);
		$values = $form_state->getValue($form['#parents']);

		$this->configuration['api_url'] = $values['api_url'];
		$this->configuration['api_username'] = $values['api_username'];
		$this->configuration['api_password'] = $values['api_password'];
		$this->configuration['service_name'] = $values['service_name'];
		$this->configuration['order_prefix'] = $values['order_prefix'];
	}
}