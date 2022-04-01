<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ManishJoy\DisableFrontend\Observer;

use ManishJoy\DisableFrontend\Helper\Data as DisableFrontendHelper;

class ApplyDisableRule implements \Magento\Framework\Event\ObserverInterface
{
    protected $logger;
    protected $redirect;
    protected $request;
    protected $_scopeConfig;
    protected $disableFrontendHelper;
    protected $_urlInterface;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        DisableFrontendHelper $disableFrontendHelper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->redirect = $redirect;
        $this->_scopeConfig = $scopeConfig;
        $this->disableFrontendHelper = $disableFrontendHelper;
        $this->_urlInterface = $urlInterface;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
            $disableEnabled = boolval($this->disableFrontendHelper->getStoreConfigValue('enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            if (!$disableEnabled) {
                return $this;
            }
            
            $currentUrl = $this->getCurrentUrl();
            /* Check if disabled for specific URL actions */
            $isDisableSpecific = boolval($this->disableFrontendHelper->getStoreConfigValue('disable_specific', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            if ($isDisableSpecific) {
                $disabledUrlFieldData = $this->disableFrontendHelper->getStoreConfigValue('disabled_urls', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $disabledUrls = !empty($disabledUrlFieldData) && strlen(trim($disabledUrlFieldData)) ? array_map('trim', explode(',', $disabledUrlFieldData)) : [];
                if (!in_array($currentUrl, $disabledUrls)) {
                    return $this;
                }
            }

            $extemptedUrlFieldData = $this->disableFrontendHelper->getStoreConfigValue('exempted_urls', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $extemptedUrls = !empty($extemptedUrlFieldData) && strlen(trim($extemptedUrlFieldData)) ? array_map('trim', explode(',', $extemptedUrlFieldData)) : [];
            if (in_array($currentUrl, $extemptedUrls)) {
                return $this;
            }
            if (
                $this->request->getFullActionName() == 'dfrontend_index_index' ||
                $this->request->getFullActionName() == 'dfrontend_error_index'
            ) {
                return $this;
            }
            $controller = $observer->getControllerAction();
            $frontendRedirectUrl = strval($this->disableFrontendHelper->getStoreConfigValue('frontend_base_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            if (empty(trim($frontendRedirectUrl))) {
                $this->redirect->redirect($controller->getResponse(), 'dfrontend/error');    
            } else {
                $this->redirect->redirect($controller->getResponse(), 'dfrontend');
            }
    
            return $this;
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
        }
    }

    public function getCurrentUrl()
    {
        $currentUrl = $this->_urlInterface->getCurrentUrl();
        if(strpos($currentUrl, '?___from_store=') === true){
            $urlExplode = explode('?___from_store=', $currentUrl);
            $currentUrl = $urlExplode[0];
        }
        if(strpos($currentUrl, '?___store=') === true){
            $urlExplode = explode('?___store=', $currentUrl);
            $currentUrl = $urlExplode[0];
        }
        return $currentUrl;
    }
}

