const $ = window.$;

$(() => {
  const Grid = window.prestashop.component.Grid;
  const {
    ReloadListExtension,
    ExportToSqlManagerExtension,
    FiltersResetExtension,
    SortingExtension,
    LinkRowActionExtension,
    BulkActionCheckboxExtension,
    SubmitRowActionExtension,
    FiltersSubmitButtonEnablerExtension,
    SubmitBulkActionExtension,
  } = window.prestashop.component.GridExtensions;

  const searchLogGrid = new Grid('search_log');

  searchLogGrid.addExtension(new ReloadListExtension());
  searchLogGrid.addExtension(new ExportToSqlManagerExtension());
  searchLogGrid.addExtension(new FiltersResetExtension());
  searchLogGrid.addExtension(new SortingExtension());
  searchLogGrid.addExtension(new LinkRowActionExtension());
  searchLogGrid.addExtension(new SubmitBulkActionExtension());
  searchLogGrid.addExtension(new BulkActionCheckboxExtension());
  searchLogGrid.addExtension(new SubmitRowActionExtension());
  searchLogGrid.addExtension(new FiltersSubmitButtonEnablerExtension());
});
