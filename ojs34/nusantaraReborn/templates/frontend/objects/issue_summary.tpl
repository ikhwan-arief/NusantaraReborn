{**
 * NusantaraReborn theme for OJS 3.4 — archive issue card: cover, title, month, description, article count, page range and link.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from templates/frontend/objects/issue_summary.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *}
{if $issue->getShowTitle()}{assign var=issueTitle value=$issue->getLocalizedTitle()}{/if}
{assign var=issueSeries value=$issue->getIssueSeries()}
{assign var=issueCover value=$issue->getLocalizedCoverImageUrl()}
{assign var=nrIssueStats value=$nrArchiveStats[$issue->getId()]}
{capture assign=nrIssueUrl}{url op="view" path=$issue->getBestIssueId()}{/capture}
<div class="obj_issue_summary nr_issue_card">
	<a class="cover" href="{$nrIssueUrl}" tabindex="-1" aria-hidden="true">
		<img src="{if $issueCover}{$issueCover|escape}{else}{$nrDefaultCoverUrl|escape}{/if}" alt="" loading="lazy" decoding="async">
	</a>
	<div class="nr_issue_card_body">
		<h3><a class="title" href="{$nrIssueUrl}">{if $issueTitle}{$issueTitle|escape}{else}{$issueSeries|escape}{/if}</a></h3>
		{if $issueTitle && $issueSeries}<div class="series">{$issueSeries|escape}</div>{/if}
		{if $issue->getDatePublished()}<div class="nr_issue_card_date">{$issue->getDatePublished()|date_format:"%B %Y"}</div>{/if}
		{if $issue->getLocalizedDescription()}<div class="description">{$issue->getLocalizedDescription()|strip_unsafe_html}</div>{/if}
		{if $nrIssueStats && $nrIssueStats.articles}
			<div class="nr_issue_card_stats">
				<span class="fa fa-file-text-o" aria-hidden="true"></span>
				<span>{if $nrIssueStats.articles == 1}{translate key="plugins.themes.nusantaraReborn.archive.oneArticle"}{else}{translate key="plugins.themes.nusantaraReborn.archive.manyArticles" num=$nrIssueStats.articles}{/if}</span>
				{if $nrIssueStats.firstPage !== null}<span class="nr_sep">|</span><span>{translate key="plugins.themes.nusantaraReborn.article.pagesShort" pages="`$nrIssueStats.firstPage`–`$nrIssueStats.lastPage`"}</span>{/if}
			</div>
		{/if}
		<a class="nr_btn nr_btn_dark nr_btn_small" href="{$nrIssueUrl}">{translate key="plugins.themes.nusantaraReborn.archive.viewIssue"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
	</div>
</div>
