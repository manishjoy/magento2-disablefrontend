<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ManishJoy\DisableFrontend\Controller\Index;

use ManishJoy\DisableFrontend\Helper\Data as DisableFrontendHelper;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultRedirectFactory;
    protected $disableFrontendHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory,
        DisableFrontendHelper $disableFrontendHelper
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->disableFrontendHelper = $disableFrontendHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $frontendBaseUrl = $this->disableFrontendHelper->getStoreConfigValue('frontend_base_url');
        return $this->redirectExternal($frontendBaseUrl);
    }

    public function redirectExternal($redirectLink)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($redirectLink);
        return $resultRedirect;
    }
}
