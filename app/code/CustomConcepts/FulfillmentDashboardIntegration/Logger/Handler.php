<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::NOTICE;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/cc_dashboard_integration.log';
}
