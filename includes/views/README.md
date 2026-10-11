Page structure
==============

Migrated pages (`infoWelcome.php`, `infoSummary.php`) use this sequence:

1. Load `includes/config.php` before output. It processes requests and redirects.
2. Load helper declarations and prepare page data.
3. Set `$pageName` and `$contentTemplate` to a fixed local file path.
4. Include `includes/layout.php` to render the complete document.

The layout owns the document, header, main container, and footer. It includes
partials for navigation, title, alerts, footer content, and scripts. Keep every
partial's HTML tags balanced within that partial.

Views render page content using variables prepared by their page. They do not
include the layout, bootstrap, header, or footer, and do not dispatch requests.
Existing rendering helpers can still query the database; separating those
queries is a later change.

Use `includes/pageFunctions.php` for shared rendering helpers and
`includes/summaryFunctions.php` for the summary's existing helpers. Helper files
contain declarations rather than output executed when they are included.

The layout supports existing options such as `$hidePageTitle`, `$hideEventNav`,
`$hideFooter`, `$jsIncludes`, and `$createSortableDataTable`. Declare page options
before including the layout. The footer is hidden when `$hideFooter` is truthy.

Other pages still use `includes/header.php` and `includes/footer.php`. These are
content-capture adapters: the header buffers navigation and begins capturing page
content; the footer finishes that capture and invokes the complete layout. Neither
adapter opens or closes HTML containers. Keep them until those pages are migrated. Preserve alert rendering before and
after page content, because rendering helpers can create additional alerts.
