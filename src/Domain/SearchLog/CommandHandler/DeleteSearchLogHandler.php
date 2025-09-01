<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT Free License
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/mit
 *
 * @author    Andrei H
 * @copyright Since 2025 Andrei H
 * @license   MIT
 */

declare(strict_types=1);

namespace PrestaShop\Module\SearchLog\Domain\SearchLog\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PrestaShop\Module\SearchLog\Domain\SearchLog\Command\DeleteSearchLogCommand;
use PrestaShop\Module\SearchLog\Domain\SearchLog\Exception\CannotDeleteSearchLogException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DeleteSearchLogHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EntityRepository $entityRepository
    ) {
    }

    public function handle(DeleteSearchLogCommand $command): void
    {
        try {
            $searchLog = $this->entityRepository->find($command->getSearchLogId()->getValue());

            $this->entityManager->remove($searchLog);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $message = sprintf('Failed to delete the request with id "%s".', $command->getSearchLogId()->getValue());

            throw new CannotDeleteSearchLogException($message);
        }
    }
}
