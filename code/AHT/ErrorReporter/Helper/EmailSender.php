<?php
namespace Gssi\ErrorReporter\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Gssi\ErrorReporter\Helper\Config as ConfigHelper;

class EmailSender extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $inlineTranslation;

    private $escaper;

    private $transportBuilder;

    private $logger;

    protected $scopeConfig;

    private $_configHelper;

    const STORE_SCOPE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    const SECTION_ORDERXML = 'gssi_orderxml';
    

    const XML_CONFIG_ORDER_XML_ERROR_REPORT_EMAIL_TEMPLATE_PATH = 'gssi_orderxml_error_reporter_email_template';

    /**
     * @param Context $context
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        ConfigHelper $configHelper
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_configHelper = $configHelper;
        $this->logger = $context->getLogger();
    }

    /**
     * Unified function used for sending email
     * 
     * @param string $template
     * @param string $to
     * @param array $from
     * @param array $var
     * 
     * @return void
     */
    private function sendEmail(
        string $template,
        string $to,
        $copyTo = '',
        $copyMethod,
        array $from = [],
        array $var = []
    ) {
        $copyToEmails = array();
        if ($copyTo != '') {
            $copyToEmails = explode(',', $copyTo);
        }

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($var)
            ->setFrom($from)
            ->addTo(
                $this->escaper->escapeHtml($to)
            );

        switch ($copyMethod) {
            case 'bcc':
                $transport = $this->transportBuilder->addBcc($copyToEmails);
                break;
            case 'cc':
                $transport = $this->transportBuilder->addCc($copyToEmails);
                break;
            default:
                break;
        }
        
        $transport = $this->transportBuilder->getTransport();

        $transport->sendMessage();
    }

    /**
     * Send Order History Update Cron Failed Email to Admin email address
     * 
     * @param array $var
     * 
     * @return void
     */
    public function sendOrderXmlErrorReportEmail(array $var = [])
    {
        if ($this->_configHelper->isEnabled(self::SECTION_ORDERXML)) {
            try {
                $this->inlineTranslation->suspend();
    
                $configSenderName = $this->_configHelper->getSenderName(self::SECTION_ORDERXML);
                $configSenderEmail = $this->_configHelper->getSenderEmail(self::SECTION_ORDERXML);
    
                $sender = [
                    'name' => ($configSenderName) ? $configSenderName : $this->scopeConfig->getValue(
                        'trans_email/ident_general/name',
                        self::STORE_SCOPE
                    ),
                    'email' => ($configSenderEmail) ? $configSenderEmail : $this->scopeConfig->getValue(
                        'trans_email/ident_general/email',
                        self::STORE_SCOPE
                    )
                ];

                $to = $this->_configHelper->getRecipientEmail(self::SECTION_ORDERXML);

                if ($to) {
                    $this->sendEmail(
                        self::XML_CONFIG_ORDER_XML_ERROR_REPORT_EMAIL_TEMPLATE_PATH,
                        $to,
                        $this->_configHelper->getSendCopyTo(self::SECTION_ORDERXML),
                        $this->_configHelper->getSendCopyMethod(self::SECTION_ORDERXML),
                        $sender,
                        $var
                    );
                }
    
                $this->inlineTranslation->resume();
            }
            catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }
   
}
