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

namespace PrestaShop\Module\SearchLog\Controller\Admin;

use PrestaShop\Module\SearchLog\Domain\SearchLog\Command\BulkDeleteSearchLogCommand;
use PrestaShop\Module\SearchLog\Domain\SearchLog\Command\DeleteSearchLogCommand;
use PrestaShop\Module\SearchLog\Search\Filters\SearchLogFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SearchLogController extends FrameworkBundleAdminController
{
    /**
     * Show list of search logs.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param SearchLogFilters $filters
     *
     * @return Response
     */
    public function indexAction(SearchLogFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.module.search_log.grid.factory.search_log');
        $grid = $gridFactory->getGrid($filters);

        return $this->render('@Modules/searchlog/views/templates/admin/index.html.twig', [
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * Delete single search log.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="search_log_index"
     * )
     *
     * @param int $searchLogId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $searchLogId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteSearchLogCommand($searchLogId));

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('search_log_index');
    }

    /**
     * Delete search logs in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="search_log_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        try {
            $searchLogIds = array_map(
                fn ($searchLogId) => (int) $searchLogId,
                $request->request->get('search_log_search_log_bulk')
            );

            $this->getCommandBus()->handle(new BulkDeleteSearchLogCommand($searchLogIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('search_log_index');
    }
}
