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
 * @copyright Since 2024 Andrei H
 * @license   MIT
 */
$autoloader = dirname(__FILE__) . '/vendor/autoload.php';

if (is_readable($autoloader)) {
    include_once $autoloader;
}

use PrestaShop\Module\SearchLog\Entity\SearchLog as SearchLogEntity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SearchLog extends Module
{
    private const HOOKS = ['actionSearch'];

    public $tabs = [
        [
            'name' => [
                'en' => 'Search Log',
            ],
            'class_name' => 'AdminSearchLogList',
            'route_name' => 'search_log_index',
            'parent_class_name' => 'SELL',
            'wording' => 'Search Log',
            'wording_domain' => 'Modules.Searchlog.Admin',
            'icon' => 'search',
        ],
    ];

    /**
     * SearchLog constructor.
     */
    public function __construct()
    {
        $this->name = 'searchlog';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Andrei H';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Search Log', [], 'Modules.Searchlog.Admin');
        $this->description = $this->trans('PrestaShop Module that logs the search keywords.', [], 'Modules.Searchlog.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Searchlog.Admin');
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->registerHook(self::HOOKS)
            && $this->createTable();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        $this->dropTable();

        return parent::uninstall();
    }

    private function createTable()
    {
        return Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'search_log` (
            `id_search_log` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` INTEGER UNSIGNED NOT NULL DEFAULT 1,
            `id_customer` INTEGER UNSIGNED NOT NULL DEFAULT 0,
            `keywords` VARCHAR(255) NOT NULL,
            `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(`id_search_log`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
    }

    private function dropTable()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'search_log');
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hookActionSearch(array $data)
    {
        try {
            $entityManager = $this->get('doctrine.orm.entity_manager');

            $searchLog = new SearchLogEntity();
            $searchLog
                ->setShopId($this->context->shop->id)
                ->setCustomerId($this->context->customer->id ?? 0)
                ->setKeywords($data['expr'])
            ;

            $entityManager->persist($searchLog);
            $entityManager->flush();
        } catch (Exception $e) {
        }
    }
}
