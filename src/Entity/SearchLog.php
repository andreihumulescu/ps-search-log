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

namespace PrestaShop\Module\SearchLog\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShop\Module\SearchLog\Repository\SearchLogRepository")
 */
class SearchLog
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_search_log", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_shop", type="integer")
     */
    private int $shopId;

    /**
     * @ORM\Column(name="id_customer", type="integer")
     */
    private int $customerId;

    /**
     * @ORM\Column(name="keywords", type="string", length=255)
     */
    private string $keywords;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): static
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }
}
