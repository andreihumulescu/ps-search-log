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

namespace PrestaShop\Module\SearchLog\Search\Filters;

use PrestaShop\Module\SearchLog\Grid\Definition\Factory\SearchLogDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SearchLogFilters extends ShopFilters
{
    protected $filterId = SearchLogDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'id_search_log',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
