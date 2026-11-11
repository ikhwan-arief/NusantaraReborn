{**
 * NusantaraReborn theme for OJS 3.4 — archives: banner, year filter and archive search, then issues grouped by year.
 * Pagination and the empty state are preserved.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from templates/frontend/pages/issueArchive.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *}
{capture assign="pageTitle"}{if $prevPage}{translate key="archive.archivesPageNumber" pageNumber=$prevPage+1}{else}{translate key="archive.archives"}{/if}{/capture}
{capture assign="nrArchiveIntro"}{translate key="plugins.themes.nusantaraReborn.archive.intro" journal=$currentContext->getLocalizedName()}{/capture}
{capture assign="nrArchiveCrumbs"}<nav class="cmp_breadcrumbs" aria-label="{translate|escape key="navigation.breadcrumbLabel"}"><ol><li><a href="{url page="index" router=\PKP\core\PKPApplication::ROUTE_PAGE}">{translate key="common.homepageNavigationLabel"}</a><span class="separator">{translate key="navigation.breadcrumbSeparator"}</span></li><li class="current" aria-current="page"><span aria-current="page">{$pageTitle|strip|escape}</span></li></ol></nav>{/capture}
{include file="frontend/components/header.tpl" pageTitleTranslated=$pageTitle isFullWidth=true nrBannerTitle=$pageTitle|strip nrBannerText=$nrArchiveIntro nrBannerCrumbs=$nrArchiveCrumbs}

<div class="page page_issue_archive">
	<h1 class="pkp_screen_reader">{$pageTitle|escape}</h1>

	{if empty($issues)}
		<p>{translate key="current.noCurrentIssueDesc"}</p>
	{else}
		<div class="nr_box nr_archive_tools">
			<div class="nr_archive_tool">
				<label for="nrArchiveYear"><span class="fa fa-book" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.archive.browseByYear"}</label>
				<select id="nrArchiveYear" data-nr-year-filter>
					<option value="">{translate key="plugins.themes.nusantaraReborn.archive.allYears"}</option>
					{foreach from=$nrArchiveYears item=nrYear}<option value="{$nrYear.year|escape}">{$nrYear.year|escape}</option>{/foreach}
				</select>
			</div>
			<form class="nr_archive_tool nr_archive_search" action="{url page="search" op="search"}" method="get" role="search">
				<label for="nrArchiveQuery"><span class="fa fa-search" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.archive.search"}</label>
				<div class="nr_archive_search_row">
					<input id="nrArchiveQuery" type="search" name="query" placeholder="{translate|escape key="plugins.themes.nusantaraReborn.archive.searchPlaceholder"}">
					<button type="submit" class="nr_btn nr_btn_dark">{translate key="common.search"}</button>
				</div>
			</form>
		</div>

		{foreach from=$nrArchiveYears item=nrYear}
			{assign var=nrYearCount value=$nrYear.issues|@count}
			<section class="nr_archive_year" data-year="{$nrYear.year|escape}">
				<div class="nr_section_head">
					<h2 class="nr_title">{$nrYear.year|escape}</h2>
					<span class="nr_archive_count">{if $nrYearCount == 1}{translate key="plugins.themes.nusantaraReborn.archive.oneIssue"}{else}{translate key="plugins.themes.nusantaraReborn.archive.manyIssues" num=$nrYearCount}{/if}</span>
				</div>
				<ul class="issues_archive nr_issue_grid">
					{foreach from=$nrYear.issues item="issue"}
						<li>{include file="frontend/objects/issue_summary.tpl"}</li>
					{/foreach}
				</ul>
			</section>
		{/foreach}

		{if $prevPage > 1}
			{capture assign=prevUrl}{url router=\PKP\core\PKPApplication::ROUTE_PAGE page="issue" op="archive" path=$prevPage}{/capture}
		{elseif $prevPage === 1}
			{capture assign=prevUrl}{url router=\PKP\core\PKPApplication::ROUTE_PAGE page="issue" op="archive"}{/capture}
		{/if}
		{if $nextPage}
			{capture assign=nextUrl}{url router=\PKP\core\PKPApplication::ROUTE_PAGE page="issue" op="archive" path=$nextPage}{/capture}
		{/if}
		{include file="frontend/components/pagination.tpl" prevUrl=$prevUrl nextUrl=$nextUrl showingStart=$showingStart showingEnd=$showingEnd total=$total}
	{/if}
</div>

{include file="frontend/components/footer.tpl" isFullWidth=true}
