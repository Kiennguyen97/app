<?php
namespace Gssi\ErrorReporter\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    const STORE_SCOPE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    const XML_CONFIG_MODULE_ENABLE_PATH = 'error_reporter/enabled';

    const XML_CONFIG_SENDER_NAME = 'error_reporter/sender_name';

    const XML_CONFIG_SENDER_EMAIL_ADDRESS = 'error_reporter/sender_email_address';

    const XML_CONFIG_RECIPIENT_EMAIL_ADDRESS = 'error_reporter/recipient_email_address';

    const XML_CONFIG_COPY_TO = 'error_reporter/copy_to';
    const XML_CONFIG_COPY_METHOD = 'error_reporter/copy_method';
    
    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get various config value, if you got the right config key
     * 
     * @param string $configSection
     * @param string $fieldId
     * 
     * @return string|bool
     */
    public function getConfig(string $configSection, string $fieldId)
    {
        return $this->scopeConfig->getValue(
            $configSection . '/error_reporter' . '/' . $fieldId,
            self::STORE_SCOPE
        );
    }

    /**
     * Check if feature is enabled
     * 
     * @param string $configSection
     * 
     * @return bool
     */
    public function isEnabled(string $configSection) : bool
    {
        return $this->scopeConfig->getValue(
            $configSection . '/' . self::XML_CONFIG_MODULE_ENABLE_PATH,
            self::STORE_SCOPE
        );
    }

    /**
     * Acquire sender name in system configuration
     * 
     * @param string $configSection
     * 
     * @return string|null
     */
    public function getSenderName(string $configSection)
    {
        return $this->scopeConfig->getValue(
            $configSection . '/' . self::XML_CONFIG_SENDER_NAME,
            self::STORE_SCOPE
        );
    }

    /**
     * Acquire sender email address in system configuration
     * 
     * @param string $configSection
     * 
     * @return string|null
     */
    public function getSenderEmail(string $configSection)
    {
        return $this->scopeConfig->getValue(
            $configSection . '/' . self::XML_CONFIG_SENDER_EMAIL_ADDRESS,
            self::STORE_SCOPE
        );
    }

    /**
     * Acquire recipient email address in system configuration
     * 
     * @param string $configSection
     * 
     * @return string
     */
    public function getRecipientEmail(string $configSection) : string
    {
        return $this->scopeConfig->getValue(
            $configSection . '/' . self::XML_CONFIG_RECIPIENT_EMAIL_ADDRESS,
            self::STORE_SCOPE
        );
    }

    /**
     * Acquire cc/bcc emails
     * 
     * @param string $configSection
     * 
     * @return string|null
     */
    public function getSendCopyTo(string $configSection)
    {
        return $this->scopeConfig->getValue(
            $configSection . '/' . self::XML_CONFIG_COPY_TO,
            self::STORE_SCOPE
        );
    }

    /**
     * Acquire send email copy method
     * 
     * @param string $configSection
     * 
     * @return string|null
     */
    public function getSendCopyMethod(string $configSection)
    {
        return $this->scopeConfig->getValue(
            $configSection . '/' . self::XML_CONFIG_COPY_METHOD,
            self::STORE_SCOPE
        );
    }
}