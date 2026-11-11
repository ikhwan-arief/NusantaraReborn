{**
 * NusantaraReborn theme for OJS 3.4 — article landing page. Built from the OJS 3.4 article_details.tpl:
 * every core hook, the usage statistics chart, data availability and ORCID (orcidProfile plugin)
 * remain OJS-rendered. The Citation Style Language block is inserted by its plugin
 * through Templates::Article::Details (never duplicated here).
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from templates/frontend/objects/article_details.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *}
<article class="obj_article_details nr_article">
	{if $publication->getData('status') !== \PKP\submission\PKPSubmission::STATUS_PUBLISHED}
		<div class="cmp_notification notice">
			{capture assign="submissionUrl"}{url page="workflow" op="access" path=$article->getId()}{/capture}
			{translate key="submission.viewingPreview" url=$submissionUrl}
		</div>
	{elseif $currentPublication->getId() !== $publication->getId()}
		<div class="cmp_notification notice">
			{capture assign="latestVersionUrl"}{url page="article" op="view" path=$article->getBestId()}{/capture}
			{translate key="submission.outdatedVersion" datePublished=$publication->getData('datePublished')|date_format:$dateFormatShort urlRecentVersion=$latestVersionUrl|escape}
		</div>
	{/if}

	<div class="nr_article_grid">
		<div class="nr_article_main">
			{if $section}<span class="nr_badge">{$section->getLocalizedTitle()|escape}</span>{/if}
			<h1 class="page_title">{$publication->getLocalizedTitle(null, 'html')|strip_unsafe_html}</h1>
			{if $publication->getLocalizedData('subtitle')}<h2 class="subtitle">{$publication->getLocalizedSubTitle(null, 'html')|strip_unsafe_html}</h2>{/if}

			{if $nrAuthors}
				<section class="item authors">
					<h2 class="pkp_screen_reader">{translate key="article.authors"}</h2>
					<ul class="authors nr_authors">
						{foreach from=$nrAuthors item=nrAuthor name=nrAuthors}
							<li><span class="name">{$nrAuthor.name|escape}</span>{if $nrAuthor.affText}<sup>{$nrAuthor.affText|escape}</sup>{/if}{if $nrAuthor.userGroup}<span class="nr_user_group">({$nrAuthor.userGroup|escape})</span>{/if}{if $nrAuthor.orcid}<a class="nr_orcid_link" href="{$nrAuthor.orcid|escape}" target="_blank" rel="noopener" title="ORCID {$nrAuthor.orcidDisplay|escape}">{if $orcidIcon}{$orcidIcon}{else}<span class="nr_orcid_badge{if !$nrAuthor.orcidVerified} is-unverified{/if}" aria-hidden="true">iD</span>{/if}<span class="pkp_screen_reader">ORCID {$nrAuthor.orcidDisplay|escape}</span></a>{/if}{if $nrAuthor.isPrimary && $nrCorresponding.email}<a class="nr_mail" href="mailto:{$nrCorresponding.email|escape}" title="{translate|escape key="plugins.themes.nusantaraReborn.article.correspondingAuthor"}"><span class="fa fa-envelope-o" aria-hidden="true"></span><span class="pkp_screen_reader">{translate key="plugins.themes.nusantaraReborn.article.correspondingAuthor"}</span></a>{/if}{if !$smarty.foreach.nrAuthors.last}<span class="nr_sep">,</span>{/if}</li>
						{/foreach}
					</ul>
					{if $nrAffiliations}
						<ol class="nr_affiliations">
							{foreach from=$nrAffiliations item=nrAff}
								<li>{if $nrAffiliations|@count > 1}<span class="nr_aff_num">{$nrAff.index}</span>{/if}<span>{$nrAff.name|escape}</span>{if $nrAff.flag} <span class="nr_flag" role="img" aria-label="{$nrAff.country|escape}" title="{$nrAff.country|escape}">{$nrAff.flag}</span>{/if}</li>
							{/foreach}
						</ol>
					{/if}
				</section>
			{/if}

			{if $nrDates}
				<dl class="nr_dates">
					{if $nrDates.received}<div><dt>{translate key="plugins.themes.nusantaraReborn.article.received"}</dt><dd>{$nrDates.received|date_format:$dateFormatLong}</dd></div>{/if}
					{if $nrDates.revised}<div><dt>{translate key="plugins.themes.nusantaraReborn.article.revised"}</dt><dd>{$nrDates.revised|date_format:$dateFormatLong}</dd></div>{/if}
					{if $nrDates.accepted}<div><dt>{translate key="plugins.themes.nusantaraReborn.article.accepted"}</dt><dd>{$nrDates.accepted|date_format:$dateFormatLong}</dd></div>{/if}
					{if $nrDates.published}<div><dt>{translate key="submissions.published"}</dt><dd>{$nrDates.published|date_format:$dateFormatLong}</dd></div>{/if}
				</dl>
			{/if}

			{if $nrDoiUrl}
				<div class="item doi nr_doi_line"><span class="nr_doi_icon" aria-hidden="true">doi</span><a href="{$nrDoiUrl|escape}">{$nrDoiUrl|escape}</a></div>
			{/if}

			{if $publication->getLocalizedData('abstract')}
				<section class="item abstract nr_box">
					<h2 class="label nr_title">{translate key="article.abstract"}</h2>
					<div class="value">{$publication->getLocalizedData('abstract')|strip_unsafe_html}</div>
				</section>
			{/if}

			{if $nrKeywords}
				<section class="item keywords">
					<h2 class="label nr_title">{translate key="plugins.themes.nusantaraReborn.article.keywords"}</h2>
					<p class="value">{foreach name="nrKeywords" from=$nrKeywords item="nrKeyword"}{$nrKeyword|escape}{if !$smarty.foreach.nrKeywords.last}; {/if}{/foreach}</p>
				</section>
			{/if}

			{call_hook name="Templates::Article::Main"}

			{if $activeTheme && $activeTheme->getOption('displayStats') != 'none'}
				{$activeTheme->displayUsageStatsGraph($article->getId())}
				<section class="item downloads_chart nr_box nr_stats_chart">
					<h2 class="label nr_title">{translate key="plugins.themes.default.displayStats.downloads"}</h2>
					<div class="value">
						<canvas class="usageStatsGraph" data-object-type="Submission" data-object-id="{$article->getId()|escape}"></canvas>
						<div class="usageStatsUnavailable" data-object-type="Submission" data-object-id="{$article->getId()|escape}">
							{translate key="plugins.themes.default.displayStats.noStats"}
						</div>
					</div>
				</section>
			{/if}

			{if $parsedCitations || (string) $publication->getData('citationsRaw')}
				<section class="item references nr_references" id="nrReferences">
					<div class="nr_section_head">
						<h2 class="label nr_title">{translate key="plugins.themes.nusantaraReborn.article.referencesPreview"}</h2>
						<button type="button" class="nr_link_button" data-nr-references-toggle aria-controls="nrReferenceList" aria-expanded="false">{translate key="plugins.themes.nusantaraReborn.article.viewAllReferences"} <span class="fa fa-arrow-right" aria-hidden="true"></span></button>
					</div>
					<div class="value">
						{if $parsedCitations}
							<ol id="nrReferenceList" class="nr_reference_list is-collapsed">
								{foreach from=$parsedCitations item="parsedCitation"}
									<li>{$parsedCitation->getCitationWithLinks()|strip_unsafe_html} {call_hook name="Templates::Article::Details::Reference" citation=$parsedCitation}</li>
								{/foreach}
							</ol>
						{else}
							<div id="nrReferenceList" class="nr_raw_references">{$publication->getData('citationsRaw')|escape|nl2br}</div>
						{/if}
					</div>
					<button type="button" class="nr_link_button nr_references_more" data-nr-references-toggle aria-controls="nrReferenceList" aria-expanded="false">{translate key="plugins.themes.nusantaraReborn.article.viewAllReferences"} <span class="fa fa-arrow-right" aria-hidden="true"></span></button>
				</section>
			{/if}

			{if $publication->getLocalizedData('dataAvailability')}
				<section class="item dataAvailability nr_data_availability" id="data-availability-statement">
					<h2 class="label nr_title">{translate key="submission.dataAvailability"}</h2>
					<div class="value">{$publication->getLocalizedData('dataAvailability')|strip_unsafe_html}</div>
				</section>
			{/if}

			{assign var="hasBiographies" value=0}
			{foreach from=$publication->getData('authors') item=author}
				{if $author->getLocalizedData('biography')}{assign var="hasBiographies" value=$hasBiographies+1}{/if}
			{/foreach}
			{if $hasBiographies}
				<section class="item author_bios">
					<h2 class="label nr_title">{if $hasBiographies > 1}{translate key="submission.authorBiographies"}{else}{translate key="submission.authorBiography"}{/if}</h2>
					{foreach from=$publication->getData('authors') item=author}
						{if $author->getLocalizedData('biography')}
							<section class="sub_item">
								<h3 class="label">{$author->getFullName()|escape}</h3>
								<div class="value">{$author->getLocalizedData('biography')|strip_unsafe_html}</div>
							</section>
						{/if}
					{/foreach}
				</section>
			{/if}
		</div>

		<aside class="entry_details nr_article_side">
			<div class="nr_box nr_side_actions">
				{if $nrGalley}
					<a class="nr_btn nr_btn_primary nr_download" href="{url page="article" op="download" path=$article->getBestId()|to_array:$nrGalley->getBestGalleyId()}">
						<span class="fa fa-file-text-o" aria-hidden="true"></span>
						<span>{if $nrGalley->isPdfGalley()}{translate key="plugins.themes.nusantaraReborn.article.downloadPdf"}{else}{$nrGalley->getGalleyLabel()|escape}{/if}</span>
						<span class="fa fa-download" aria-hidden="true"></span>
					</a>
					<div class="nr_download_meta">{$nrGalley->getGalleyLabel()|escape}{if $nrGalleySize} ({$nrGalleySize|escape}){/if} · <a href="{url page="article" op="view" path=$article->getBestId()|to_array:$nrGalley->getBestGalleyId()}">{translate key="plugins.themes.nusantaraReborn.article.readOnline"}</a></div>
				{/if}
				{if $primaryGalleys|@count > 1}
					<ul class="value galleys_links nr_more_galleys">
						{foreach from=$primaryGalleys item=galley}
							{if !$nrGalley || $galley->getId() != $nrGalley->getId()}
								<li>{include file="frontend/objects/galley_link.tpl" parent=$article publication=$publication galley=$galley purchaseFee=$currentJournal->getData('purchaseArticleFee') purchaseCurrency=$currentJournal->getData('currency')}</li>
							{/if}
						{/foreach}
					</ul>
				{/if}
				<div class="nr_side_buttons is-single">
					{capture assign="nrArticleUrl"}{url page="article" op="view" path=$article->getBestId()}{/capture}
					{capture assign="nrShareTitle"}{$publication->getLocalizedTitle()|strip_tags}{/capture}
					<details class="nr_dropdown nr_share">
						<summary class="nr_btn nr_btn_outline"><span class="fa fa-share-alt" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.article.share"}<span class="fa fa-angle-down" aria-hidden="true"></span></summary>
						<ul>
							<li><button type="button" data-nr-copy="{$nrArticleUrl|escape}"><span class="fa fa-link" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.article.copyLink"}</button></li>
							<li><a href="https://www.linkedin.com/sharing/share-offsite/?url={$nrArticleUrl|escape:"url"}" target="_blank" rel="noopener"><span class="fa fa-linkedin" aria-hidden="true"></span>LinkedIn</a></li>
							<li><a href="https://twitter.com/intent/tweet?url={$nrArticleUrl|escape:"url"}&amp;text={$nrShareTitle|escape:"url"}" target="_blank" rel="noopener"><span class="fa fa-twitter" aria-hidden="true"></span>X</a></li>
							<li><a href="https://www.facebook.com/sharer/sharer.php?u={$nrArticleUrl|escape:"url"}" target="_blank" rel="noopener"><span class="fa fa-facebook" aria-hidden="true"></span>Facebook</a></li>
							<li><a href="mailto:?subject={$nrShareTitle|escape:"url"}&amp;body={$nrArticleUrl|escape:"url"}"><span class="fa fa-envelope" aria-hidden="true"></span>{translate key="user.email"}</a></li>
						</ul>
					</details>
				</div>
			</div>

			<section class="nr_box nr_article_info">
				<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.article.information"}</h2>
				<dl class="nr_fact_list">
					{if $section}<div><dt><span class="fa fa-book" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.article.type"}</dt><dd>{$section->getLocalizedTitle()|escape}</dd></div>{/if}
					{if $issue}<div><dt><span class="fa fa-database" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.article.volumeIssuePages"}</dt><dd><a href="{url page="issue" op="view" path=$issue->getBestIssueId()}">{$issue->getIssueIdentification()|strip_tags|escape}</a>{if $publication->getData('pages')}, {translate key="plugins.themes.nusantaraReborn.article.pagesShort" pages=$publication->getData('pages')|escape}{/if}</dd></div>{/if}
					{if $publication->getData('datePublished')}
						<div><dt><span class="fa fa-calendar" aria-hidden="true"></span>{translate key="submissions.published"}</dt><dd>
							{if $firstPublication->getId() === $publication->getId()}{$firstPublication->getData('datePublished')|date_format:$dateFormatLong}{else}{translate key="submission.updatedOn" datePublished=$firstPublication->getData('datePublished')|date_format:$dateFormatShort dateUpdated=$publication->getData('datePublished')|date_format:$dateFormatShort}{/if}
						</dd></div>
					{/if}
					{if $nrDoi}<div><dt><span class="nr_doi_icon" aria-hidden="true">doi</span>DOI</dt><dd><a href="{$nrDoiUrl|escape}">{$nrDoi|escape}</a></dd></div>{/if}
					{if $nrViews !== null}<div><dt><span class="fa fa-eye" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.article.views"}</dt><dd>{$nrViews|escape}</dd></div>{/if}
					{if $nrDownloads !== null}<div><dt><span class="fa fa-download" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.article.downloads"}</dt><dd>{$nrDownloads|escape}</dd></div>{/if}
					{foreach from=$pubIdPlugins item=pubIdPlugin}
						{if $pubIdPlugin->getPubIdType() == 'doi'}{continue}{/if}
						{assign var=pubId value=$publication->getStoredPubId($pubIdPlugin->getPubIdType())}
						{if $pubId}<div class="pubid"><dt><span class="fa fa-link" aria-hidden="true"></span>{$pubIdPlugin->getPubIdDisplayType()|escape}</dt><dd>{if $pubIdPlugin->getResolvingURL($currentJournal->getId(), $pubId)|escape}<a id="pub-id::{$pubIdPlugin->getPubIdType()|escape}" href="{$pubIdPlugin->getResolvingURL($currentJournal->getId(), $pubId)|escape}">{$pubId|escape}</a>{else}{$pubId|escape}{/if}</dd></div>{/if}
					{/foreach}
					{if $categories}<div><dt><span class="fa fa-tags" aria-hidden="true"></span>{translate key="category.category"}</dt><dd>{foreach from=$categories item=category name=nrCats}<a href="{url router=\PKP\core\PKPApplication::ROUTE_PAGE page="catalog" op="category" path=$category->getPath()|escape}">{$category->getLocalizedTitle()|escape}</a>{if !$smarty.foreach.nrCats.last}, {/if}{/foreach}</dd></div>{/if}
				</dl>
				{if $supplementaryGalleys}
					<h3 class="label">{translate key="submission.additionalFiles"}</h3>
					<ul class="supplementary_galleys_links">
						{foreach from=$supplementaryGalleys item=galley}
							<li>{include file="frontend/objects/galley_link.tpl" parent=$article publication=$publication galley=$galley isSupplementary="1"}</li>
						{/foreach}
					</ul>
				{/if}
				{if count($article->getPublishedPublications()) > 1}
					<h3 class="label">{translate key="submission.versions"}</h3>
					<ul class="versions">
						{foreach from=array_reverse($article->getPublishedPublications()) item=iPublication}
							{capture assign="name"}{translate key="submission.versionIdentity" datePublished=$iPublication->getData('datePublished')|date_format:$dateFormatShort version=$iPublication->getData('version')}{/capture}
							<li>{if $iPublication->getId() === $publication->getId()}{$name}{elseif $iPublication->getId() === $currentPublication->getId()}<a href="{url page="article" op="view" path=$article->getBestId()}">{$name}</a>{else}<a href="{url page="article" op="view" path=$article->getBestId()|to_array:"version":$iPublication->getId()}">{$name}</a>{/if}</li>
						{/foreach}
					</ul>
				{/if}
			</section>

			{if $nrCorresponding}
				<section class="nr_box nr_corresponding">
					<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.article.correspondingAuthor"}</h2>
					<div class="nr_corresponding_body">
						<span class="fa fa-envelope-o" aria-hidden="true"></span>
						<div>
							<strong>{$nrCorresponding.name|escape}</strong>
							{if $nrCorresponding.affiliation}<div class="nr_address">{$nrCorresponding.affiliation|escape}</div>{/if}
							{if $nrCorresponding.email}<div>{translate key="user.email"}: <a href="mailto:{$nrCorresponding.email|escape}">{$nrCorresponding.email|escape}</a></div>{/if}
						</div>
					</div>
				</section>
			{/if}

			{if $currentContext->getLocalizedData('licenseTerms') || $publication->getData('licenseUrl')}
				<section class="nr_box item copyright nr_license">
					<h2 class="nr_title">{translate key="submission.license"}</h2>
					<div class="nr_license_body">
						{if $currentContext->getData('publishingMode') == \APP\journal\Journal::PUBLISHING_MODE_OPEN}<span class="nr_oa_icon nr_oa_large" aria-hidden="true"></span>{/if}
						<div>
							{if $nrLicenseName}<p>{translate key="plugins.themes.nusantaraReborn.article.licenseText" license=$nrLicenseName}</p>{/if}
							{if $publication->getLocalizedData('copyrightHolder')}<p>{translate|escape key="submission.copyrightStatement" copyrightHolder=$publication->getLocalizedData('copyrightHolder') copyrightYear=$publication->getData('copyrightYear')}</p>{/if}
						</div>
					</div>
					{if $publication->getData('licenseUrl')}
						<div class="nr_license_links">
							{if $ccLicenseBadge}{$ccLicenseBadge}{/if}
							<a href="{$publication->getData('licenseUrl')|escape}" rel="license">{translate key="plugins.themes.nusantaraReborn.article.viewLicense"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
						</div>
					{/if}
					{$nrLicenseTerms}
				</section>
			{/if}

			{call_hook name="Templates::Article::Details"}
		</aside>
	</div>

	{if $nrRelated}
		<section class="nr_related">
			<div class="nr_section_head">
				<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.article.related"}</h2>
				<a href="{url page="issue" op="archive"}">{translate key="plugins.themes.nusantaraReborn.article.moreArticles"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
			</div>
			<ul class="nr_cards">
				{foreach from=$nrRelated item=nrItem}
					{include file="frontend/objects/nr_article_card.tpl" nrItem=$nrItem nrCardIssue=null nrCardFull=false}
				{/foreach}
			</ul>
		</section>
	{/if}
</article>
