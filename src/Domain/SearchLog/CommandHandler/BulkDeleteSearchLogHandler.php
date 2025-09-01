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
use PrestaShop\Module\SearchLog\Domain\SearchLog\Command\BulkDeleteSearchLogCommand;
use PrestaShop\Module\SearchLog\Domain\SearchLog\Exception\CannotDeleteSearchLogException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class BulkDeleteSearchLogHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function handle(BulkDeleteSearchLogCommand $command): void
    {
        $searchLogIds = array_map(
            fn ($searchLogId) => $searchLogId->getValue(),
            $command->getSearchLogIds()
        );

        try {
            $qb = $this->entityManager->createQueryBuilder();

            $qb->delete('PrestaShop\Module\SearchLog\Entity\SearchLog', 's')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $searchLogIds);

            $qb->getQuery()->execute();
        } catch (\Exception $e) {
            $message = sprintf('Failed to delete search logs with ids: %s', implode(', ', $searchLogIds));

            throw new CannotDeleteSearchLogException($message, 0, $e);
        }
    }
}
