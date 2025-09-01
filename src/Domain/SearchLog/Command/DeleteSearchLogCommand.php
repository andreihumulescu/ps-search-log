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

namespace PrestaShop\Module\SearchLog\Domain\SearchLog\Command;

use PrestaShop\Module\SearchLog\Domain\SearchLog\ValueObject\SearchLogId;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DeleteSearchLogCommand
{
    private SearchLogId $searchLogId;

    public function __construct(int $searchLogId)
    {
        $this->searchLogId = new SearchLogId($searchLogId);
    }

    public function getSearchLogId(): SearchLogId
    {
        return $this->searchLogId;
    }
}
