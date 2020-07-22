<?php

namespace Drupal\wooppay\Soap;

class CashCreateInvoiceByServiceRequest
{
	/**
	 * @var string $referenceId
	 * @soap
	 */
	public $referenceId;
	/**
	 * @var string $backUrl
	 * @soap
	 */
	public $backUrl;
	/**
	 * @var string $requestUrl
	 * @soap
	 */
	public $requestUrl = '';
	/**
	 * @var string $addInfo
	 * @soap
	 */
	public $addInfo;
	/**
	 * @var float $amount
	 * @soap
	 */
	public $amount;
	/**
	 * @var string $deathDate
	 * @soap
	 */
	public $deathDate;
	/**
	 * @var int $serviceType
	 * @soap
	 */
	public $serviceType = 4;
	/**
	 * @var string $description
	 * @soap
	 */
	public $description = '';
	/**
	 * @var int $orderNumber
	 * @soap
	 */
	public $orderNumber = null;
	/**
	 * @var string $userEmail
	 * @soap
	 */
	public $userEmail = null;
	/**
	 * @var string $userPhone
	 * @soap
	 */
	public $userPhone = null;
	/**
	 * @var int $cardForbidden
	 * @soap
	 */
	public $cardForbidden;
	/**
	 * @var string $serviceName
	 * @soap
	 */
	public $serviceName;
}
