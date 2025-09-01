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

namespace PrestaShop\Module\SearchLog\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class SearchLogQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     * @param int $contextShopId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
        private int $contextShopId,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria);
        $qb
            ->addSelect('cu.`id_customer` IS NULL as `deleted_customer`')
            ->addSelect($this->getCustomerField() . ' AS `customer`');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria);
        $qb->select('COUNT(id_search_log)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('id_search_log, sl.id_customer, keywords, sl.date_add, sl.id_shop')
            ->from($this->dbPrefix . 'search_log', 'sl')
            ->leftJoin('sl', $this->dbPrefix . 'customer', 'cu', 'sl.id_customer = cu.id_customer')
            ->where('sl.id_shop = :context_shop_id')
            ->setParameter('context_shop_id', $this->contextShopId)
        ;

        $this->applyFilters($searchCriteria->getFilters(), $qb);

        return $qb;
    }

    /**
     * Apply filters to search log query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb): void
    {
        $strictComparisonFilters = [
            'id_search_log' => 'id_search_log',
        ];

        $likeComparisonFilters = [
            'keywords' => 'keywords',
            'customer' => $this->getCustomerField(),
        ];

        $dateComparisonFilters = [
            'date_add' => 'sl.`date_add`',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (isset($strictComparisonFilters[$filterName])) {
                $alias = $strictComparisonFilters[$filterName];

                $qb->andWhere("$alias = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if (isset($likeComparisonFilters[$filterName])) {
                $alias = $likeComparisonFilters[$filterName];

                $qb->andWhere("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if (isset($dateComparisonFilters[$filterName])) {
                $alias = $dateComparisonFilters[$filterName];

                if (isset($filterValue['from'])) {
                    $name = sprintf('%s_from', $filterName);

                    $qb->andWhere("$alias >= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['from'], '0:0:0'));
                }

                if (isset($filterValue['to'])) {
                    $name = sprintf('%s_to', $filterName);

                    $qb->andWhere("$alias <= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['to'], '23:59:59'));
                }

                continue;
            }
        }
    }

    /**
     * @return string
     */
    private function getCustomerField(): string
    {
        return 'CONCAT(LEFT(cu.`firstname`, 1), \'. \', cu.`lastname`)';
    }
}
