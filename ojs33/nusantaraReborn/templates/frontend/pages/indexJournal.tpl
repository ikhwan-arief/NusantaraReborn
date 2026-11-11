{**
 * NusantaraReborn theme for OJS 3.3 — journal homepage.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from templates/frontend/pages/indexJournal.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *
 * Uses native OJS context, issue, announcement and article data. Rows without data are omitted;
 * no policy, review or indexing claim is shown unless the journal configured it.
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$currentJournal->getLocalizedName() isFullWidth=true}

<div class="page_index_journal nr_home">
	{call_hook name="Templates::Index::journal"}

	{* HERO *}
	<section class="nr_hero{if !$nrUsingCustomHero} is-preset{/if}" aria-labelledby="nrJournalTitle">
		<div class="nr_hero_media" aria-hidden="true">
			<img src="{$nrHeroImageUrl|escape}"{if $nrHeroImageSrcset} srcset="{$nrHeroImageSrcset|escape}" sizes="100vw"{/if} alt="" width="1600" height="900" loading="eager" fetchpriority="high" decoding="async">
		</div>
		<div class="nr_hero_overlay" aria-hidden="true"></div>
		<div class="nr_hero_inner">
			<div class="nr_hero_copy">
				<h1 id="nrJournalTitle">{$currentJournal->getLocalizedName()|escape}</h1>
				{if $nrHeroDescription}<p class="nr_hero_description">{$nrHeroDescription|escape}</p>{/if}
				{if $currentContext->getData('publishingMode') == $smarty.const.PUBLISHING_MODE_OPEN || $currentContext->getData('printIssn') || $currentContext->getData('onlineIssn')}
					<ul class="nr_hero_meta">
						{if $currentContext->getData('publishingMode') == $smarty.const.PUBLISHING_MODE_OPEN}
							<li><span class="nr_oa_icon" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.openAccess"}</li>
						{/if}
						{if $currentContext->getData('printIssn')}<li>p-ISSN {$currentContext->getData('printIssn')|escape}</li>{/if}
						{if $currentContext->getData('onlineIssn')}<li>e-ISSN {$currentContext->getData('onlineIssn')|escape}</li>{/if}
					</ul>
				{/if}
				<div class="nr_hero_actions">
					<a class="nr_btn nr_btn_primary" href="{url page="submission"}">
						<span class="fa fa-paper-plane" aria-hidden="true"></span>
						{translate key="plugins.themes.nusantaraReborn.home.submitManuscript"}
					</a>
					{if $issue}
						<a class="nr_btn nr_btn_ghost" href="{url page="issue" op="current"}">
							<span class="fa fa-book" aria-hidden="true"></span>
							{translate key="plugins.themes.nusantaraReborn.home.viewCurrentIssue"}
						</a>
					{else}
						<a class="nr_btn nr_btn_ghost" href="{url page="about" op="submissions"}">
							<span class="fa fa-file-text-o" aria-hidden="true"></span>
							{translate key="about.submissions"}
						</a>
					{/if}
				</div>
			</div>
		</div>
		{if $nrHeroCaption}<p class="nr_hero_caption">{$nrHeroCaption|escape}</p>{/if}
	</section>

	{* ABOUT + JOURNAL INFORMATION *}
	{capture assign="nrFacts"}{strip}
		{if $nrPublicationFrequencyLabel}<div><dt><span class="fa fa-calendar" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.frequency"}</dt><dd>{$nrPublicationFrequencyLabel|escape}</dd></div>{/if}
		{if $nrPeerReviewLabel}<div><dt><span class="fa fa-users" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.peerReview"}</dt><dd>{$nrPeerReviewLabel|escape}</dd></div>{/if}
		{if $nrApcLabel}<div><dt><span class="fa fa-money" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.apc"}</dt><dd>{$nrApcLabel|escape}</dd></div>{/if}
		{if $nrLanguageLabel}<div><dt><span class="fa fa-language" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.language"}</dt><dd>{$nrLanguageLabel|escape}</dd></div>{/if}
		{if $nrIndexingLabel}<div><dt><span class="fa fa-search" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.indexing"}</dt><dd>{$nrIndexingLabel|escape}</dd></div>{/if}
		{if $nrArchivingLabel}<div><dt><span class="fa fa-archive" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.archiving"}</dt><dd>{$nrArchivingLabel|escape}</dd></div>{/if}
	{/strip}{/capture}
	{if $nrAboutExcerpt || $nrFeatureHighlights || $nrFacts}
		<div class="nr_band">
			<div class="nr_wrap nr_home_intro{if !$nrFacts || !($nrAboutExcerpt || $nrFeatureHighlights)} is-single{/if}">
				{if $nrAboutExcerpt || $nrFeatureHighlights}
					<section class="nr_overview" id="homepageAbout">
						<h2 class="nr_title">{translate key="about.aboutContext"}</h2>
						{if $nrAboutExcerpt}
							<div class="nr_richtext"><p>{$nrAboutExcerpt|escape}</p></div>
							<p class="nr_more"><a href="{url page="about"}">{translate key="plugins.themes.nusantaraReborn.home.readMore"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a></p>
						{/if}
						{if $nrFeatureHighlights}
							<ul class="nr_features">
								{foreach from=$nrFeatureHighlights item=nrFeature}
									<li><span class="fa {$nrFeature.icon|escape}" aria-hidden="true"></span><span>{$nrFeature.label|escape}</span></li>
								{/foreach}
							</ul>
						{/if}
					</section>
				{/if}

				{if $nrFacts}
					<section class="nr_box nr_facts">
						<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.home.journalInformation"}</h2>
						<dl class="nr_fact_list">{$nrFacts}</dl>
					</section>
				{/if}
			</div>
		</div>
	{/if}

	{* CURRENT ISSUE *}
	{if $issue}
		<div class="nr_band">
			<section class="nr_wrap nr_current" id="homepageIssue">
				<div class="nr_section_head">
					<h2 class="nr_title">{translate key="journal.currentIssue"}</h2>
					<a href="{url page="issue" op="archive"}">{translate key="plugins.themes.nusantaraReborn.home.viewAllIssues"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
				</div>
				<div class="nr_current_grid">
					{assign var=currentIssueCover value=$issue->getLocalizedCoverImageUrl()}
					<a class="nr_current_cover" href="{url page="issue" op="view" path=$issue->getBestIssueId()}" tabindex="-1" aria-hidden="true">
						<img src="{if $currentIssueCover}{$currentIssueCover|escape}{else}{$nrDefaultCoverUrl|escape}{/if}" alt="" loading="lazy" decoding="async">
					</a>
					<div class="nr_current_body">
						<h3><a href="{url page="issue" op="view" path=$issue->getBestIssueId()}">{$issue->getIssueIdentification()|strip_unsafe_html}</a></h3>
						{if $issue->getDatePublished()}<div class="nr_current_date">{translate key="submissions.published"}: {$issue->getDatePublished()|date_format:$dateFormatLong}</div>{/if}
						{if $issue->getLocalizedDescription()}<div class="nr_current_description">{$issue->getLocalizedDescription()|strip_unsafe_html}</div>{/if}
						<a class="nr_btn nr_btn_dark" href="{url page="issue" op="current"}">{translate key="plugins.themes.nusantaraReborn.home.viewCurrentIssue"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
					</div>
					{if $nrHomeArticles}
						<div class="nr_box nr_in_issue">
							<h3>{translate key="plugins.themes.nusantaraReborn.home.inThisIssue"}</h3>
							<ul>
								{foreach from=$nrHomeArticles item=nrItem}
									<li><a href="{url page="article" op="view" path=$nrItem.article->getBestId()}"><span>{$nrItem.publication->getLocalizedTitle()|escape}</span><span class="fa fa-angle-right" aria-hidden="true"></span></a></li>
								{/foreach}
							</ul>
						</div>
					{/if}
				</div>
			</section>
		</div>
	{/if}

	{* LATEST ARTICLES *}
	{if $nrHomeArticles}
		<div class="nr_band">
			<section class="nr_wrap nr_latest">
				<div class="nr_section_head">
					<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.home.latestArticles"}</h2>
					<a href="{url page="issue" op="current"}">{translate key="plugins.themes.nusantaraReborn.home.viewAllArticles"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
				</div>
				<ul class="nr_cards">
					{foreach from=$nrHomeArticles item=nrItem}
						{include file="frontend/objects/nr_article_card.tpl" nrItem=$nrItem nrCardIssue=$issue nrCardFull=true}
					{/foreach}
				</ul>
			</section>
		</div>
	{/if}

	{* ANNOUNCEMENTS + EDITOR IN CHIEF *}
	{if ($numAnnouncementsHomepage && $announcements|@count) || $nrEditorInChiefName}
		<div class="nr_band">
			<div class="nr_wrap nr_home_bottom{if !($numAnnouncementsHomepage && $announcements|@count) || !$nrEditorInChiefName} is-single{/if}">
				{if $numAnnouncementsHomepage && $announcements|@count}
					<section class="nr_announcements" id="homepageAnnouncements">
						<div class="nr_section_head">
							<h2 class="nr_title">{translate key="announcement.announcements"}</h2>
							<a href="{url page="announcement"}">{translate key="plugins.themes.nusantaraReborn.home.viewAllUpdates"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
						</div>
						{foreach name=nrAnn from=$announcements item=nrAnnouncement}
							{if $smarty.foreach.nrAnn.iteration > $numAnnouncementsHomepage}{break}{/if}
							{if $smarty.foreach.nrAnn.first}
								<a class="nr_box nr_feature_announcement" href="{url page="announcement" op="view" path=$nrAnnouncement->getId()}">
									<span class="fa fa-bullhorn" aria-hidden="true"></span>
									<span class="nr_feature_copy">
										<span class="nr_feature_label">{if $nrAnnouncement->getAnnouncementTypeName()}{$nrAnnouncement->getAnnouncementTypeName()|escape}{else}{translate key="announcement.announcements"}{/if}</span>
										<span class="nr_feature_title">{$nrAnnouncement->getLocalizedTitle()|escape}</span>
										<time class="nr_feature_date" datetime="{$nrAnnouncement->getDatePosted()|date_format:"%Y-%m-%d"}">{$nrAnnouncement->getDatePosted()|date_format:$dateFormatShort}</time>
										{if $nrAnnouncement->getLocalizedDescriptionShort()}<span class="nr_feature_summary">{$nrAnnouncement->getLocalizedDescriptionShort()|strip_tags|escape}</span>{/if}
									</span>
									<span class="nr_round_arrow fa fa-arrow-right" aria-hidden="true"></span>
								</a>
								{if !$smarty.foreach.nrAnn.last && $numAnnouncementsHomepage > 1}<ul class="nr_ann_list">{/if}
							{else}
								<li><a href="{url page="announcement" op="view" path=$nrAnnouncement->getId()}"><span class="fa fa-bullhorn" aria-hidden="true"></span><span class="nr_ann_title">{$nrAnnouncement->getLocalizedTitle()|escape}</span><time datetime="{$nrAnnouncement->getDatePosted()|date_format:"%Y-%m-%d"}">{$nrAnnouncement->getDatePosted()|date_format:$dateFormatShort}</time><span class="fa fa-angle-right" aria-hidden="true"></span></a></li>
							{/if}
							{if !$smarty.foreach.nrAnn.first && ($smarty.foreach.nrAnn.last || $smarty.foreach.nrAnn.iteration == $numAnnouncementsHomepage)}</ul>{/if}
						{/foreach}
					</section>
				{/if}

				{if $nrEditorInChiefName}
					<section class="nr_editor">
						<h2 class="nr_title">{$nrEditorInChiefRole|escape}</h2>
						<div class="nr_editor_profile">
							{if $nrEditorInChiefPhoto}
								<img class="nr_editor_photo" src="{$nrEditorInChiefPhoto|escape}" alt="{$nrEditorInChiefName|escape}" loading="lazy">
							{else}
								<div class="nr_editor_photo nr_editor_avatar" aria-hidden="true"><span class="fa fa-user"></span></div>
							{/if}
							<div class="nr_editor_text">
								<strong>{$nrEditorInChiefName|escape}</strong>
								{if $nrEditorInChiefAffiliation}<div>{$nrEditorInChiefAffiliation|escape}</div>{/if}
								{if $nrEditorInChiefBio}<p>{$nrEditorInChiefBio|strip_tags|escape}</p>{/if}
								<a class="nr_btn nr_btn_outline" href="{url page="about" op="editorialTeam"}">{translate key="plugins.themes.nusantaraReborn.home.viewEditorialTeam"} <span class="fa fa-arrow-right" aria-hidden="true"></span></a>
							</div>
						</div>
					</section>
				{/if}
			</div>
		</div>
	{/if}

	{if $additionalHomeContent}
		<div class="nr_band">
			<section class="nr_wrap nr_additional_content">{$additionalHomeContent}</section>
		</div>
	{/if}
</div>

{include file="frontend/components/footer.tpl" isFullWidth=true}
