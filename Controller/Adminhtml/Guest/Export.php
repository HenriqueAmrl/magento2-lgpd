<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Adminhtml\Guest;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use HenriqueAmrl\Lgpd\Api\ActionInterface;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;
use HenriqueAmrl\Lgpd\Controller\Adminhtml\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ContextBuilder;
use HenriqueAmrl\Lgpd\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use HenriqueAmrl\Lgpd\Model\Config;

class Export extends AbstractAction
{
    public const ADMIN_RESOURCE = 'HenriqueAmrl_Lgpd::order_export';

    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->fileFactory = $fileFactory;
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($context, $config);
    }

    protected function executeAction()
    {
        $entityId = (int) $this->getRequest()->getParam('id');

        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => $entityId,
            ArgumentReader::ENTITY_TYPE => 'order'
        ]);

        try {
            $result = $this->action->execute($this->actionContextBuilder->create())->getResult();
            /** @var ExportEntityInterface $exportEntity */
            $exportEntity = $result[ExportArgumentReader::EXPORT_ENTITY];

            return $this->fileFactory->create(
                'guest_privacy_data_' . $entityId . '.zip',
                [
                    'type' => 'filename',
                    'value' => $exportEntity->getFilePath(),
                ],
                DirectoryList::TMP
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
