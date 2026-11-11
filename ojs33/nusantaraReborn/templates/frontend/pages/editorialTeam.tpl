{**
 * NusantaraReborn theme for OJS 3.3 — editorial Team. Content stays in OJS (Settings > Journal > Masthead); the theme script
 * turns editor lists into profile cards and tables. Without JavaScript the native content shows.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from lib/pkp/templates/frontend/pages/editorialTeam.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *}
{include file="frontend/components/header.tpl" pageTitle="about.editorialTeam" isFullWidth=true}

<div class="nr_two_col">
	<div class="page page_editorial_team">
		{include file="frontend/components/breadcrumbs.tpl" currentTitleKey="about.editorialTeam"}
		<h1 class="nr_title">{translate key="about.editorialTeam"}</h1>
		{include file="frontend/components/editLink.tpl" page="management" op="settings" path="context" anchor="masthead" sectionTitleKey="about.editorialTeam"}
		{if $nrEditorialIntro}<div class="nr_lead">{$nrEditorialIntro|strip_unsafe_html}</div>{/if}
		<div class="nr_masthead">{$nrEditorialBody}</div>
		<script type="application/json" class="nr-country-map">{$nrCountryMapJson nofilter}</script>
	</div>

	<aside class="nr_side">
		<section class="nr_box">
			<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.home.journalInformation"}</h2>
			<dl class="nr_fact_list">
				{if $currentContext->getData('onlineIssn') || $currentContext->getData('printIssn')}
					<div><dt><span class="fa fa-book" aria-hidden="true"></span>{if $currentContext->getData('onlineIssn')}E-ISSN {$currentContext->getData('onlineIssn')|escape}{/if}{if $currentContext->getData('onlineIssn') && $currentContext->getData('printIssn')}<br>{/if}{if $currentContext->getData('printIssn')}P-ISSN {$currentContext->getData('printIssn')|escape}{/if}</dt></div>
				{/if}
				{if $currentContext->getData('publisherInstitution')}<div><dt><span class="fa fa-university" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.editorial.publishedBy"}</dt><dd>{$currentContext->getData('publisherInstitution')|escape}</dd></div>{/if}
				{if $nrSubjectAreaLabel}<div><dt><span class="fa fa-database" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.editorial.subjectArea"}</dt><dd>{$nrSubjectAreaLabel|escape}</dd></div>{/if}
				{if $nrLanguageLabel}<div><dt><span class="fa fa-language" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.language"}</dt><dd>{$nrLanguageLabel|escape}</dd></div>{/if}
				{if $nrPublicationFrequencyLabel}<div><dt><span class="fa fa-calendar" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.frequency"}</dt><dd>{$nrPublicationFrequencyLabel|escape}</dd></div>{/if}
				{if $currentContext->getData('publishingMode') == $smarty.const.PUBLISHING_MODE_OPEN}<div><dt><span class="fa fa-unlock-alt" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.openAccess"}</dt><dd>{translate key="plugins.themes.nusantaraReborn.editorial.openAccessText"}</dd></div>{/if}
			</dl>
		</section>
		<section class="nr_box nr_office">
			<h2 class="nr_title">{translate key="plugins.themes.nusantaraReborn.editorial.office"}</h2>
			<p><strong>{$currentContext->getLocalizedName()|escape}</strong>{if $currentContext->getData('publisherInstitution')}<br>{$currentContext->getData('publisherInstitution')|escape}{/if}</p>
			<ul class="nr_office_contact">
				{if $currentContext->getData('contactEmail')}<li><span class="fa fa-envelope-o" aria-hidden="true"></span><a href="mailto:{$currentContext->getData('contactEmail')|escape}">{$currentContext->getData('contactEmail')|escape}</a></li>{/if}
				{if $currentContext->getData('contactPhone')}<li><span class="fa fa-phone" aria-hidden="true"></span>{$currentContext->getData('contactPhone')|escape}</li>{/if}
				{if $currentContext->getData('mailingAddress')}<li><span class="fa fa-map-marker" aria-hidden="true"></span><span class="nr_address">{$currentContext->getData('mailingAddress')|strip_tags|escape}</span></li>{/if}
			</ul>
		</section>
	</aside>
</div>

{include file="frontend/components/footer.tpl" isFullWidth=true}
