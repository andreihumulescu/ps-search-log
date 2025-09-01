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

namespace PrestaShop\Module\SearchLog\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DisableableLinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractFilterableGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\BulkDeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\DeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class SearchLogDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'search_log';

    /**
     * SearchLogDefinitionFactory constructor.
     *
     * @param HookDispatcherInterface $dispatcher
     * @param string $contextDateFormat
     */
    public function __construct(
        HookDispatcherInterface $dispatcher,
        private string $contextDateFormat
    ) {
        parent::__construct($dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Search Log', [], 'Modules.Searchlog.Admin');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('search_log_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_search_log',
                    ])
            )
            ->add(
                (new DataColumn('id_search_log'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_search_log',
                    ])
            )
            ->add(
                (new DisableableLinkColumn('customer'))
                    ->setName($this->trans('Customer', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'customer',
                        'disabled_field' => 'deleted_customer',
                        'route' => 'admin_customers_view',
                        'route_param_name' => 'customerId',
                        'route_param_field' => 'id_customer',
                        'target' => '_blank',
                    ])
            )
            ->add(
                (new DataColumn('keywords'))
                    ->setName($this->trans('Keywords', [], 'Modules.Searchlog.Admin'))
                    ->setOptions([
                        'field' => 'keywords',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Date', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'date_add',
                        'format' => $this->contextDateFormat,
                        'clickable' => true,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                $this->buildDeleteAction(
                                    'search_log_delete',
                                    'searchLogId',
                                    'id_search_log'
                                )
                            ),
                    ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_search_log', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_search_log')
            )
            ->add(
                (new Filter('customer', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search customer', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('customer')
            )
            ->add(
                (new Filter('keywords', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search message', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('keywords')
            )
            ->add(
                (new Filter('date_add', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('date_add')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'search_log_index',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('search_log_bulk_delete')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
        ;
    }
}
