{**
 * NusantaraReborn theme for OJS 3.5 — sidebar area and site footer.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from lib/pkp/templates/frontend/components/footer.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *
 * The sidebar hook always runs. Full-width pages show the configured blocks as a band below the
 * content, so block plugins stay visible wherever OJS places them. Footer content, scripts and
 * the page footer hook remain in their core positions.
 *}
{strip}
	{if $currentContext}{assign var="nrSiteName" value=$currentContext->getLocalizedName()}
	{elseif $siteTitle}{assign var="nrSiteName" value=$siteTitle}
	{elseif $displayPageHeaderTitle}{assign var="nrSiteName" value=$displayPageHeaderTitle}
	{else}{assign var="nrSiteName" value=$applicationName}{/if}
{/strip}
		</div><!-- pkp_structure_main -->

		{capture assign="sidebarCode"}{call_hook name="Templates::Common::Sidebar"}{/capture}
		{if empty($isFullWidth) && $sidebarCode}
			<div class="pkp_structure_sidebar left" role="complementary" aria-label="{translate|escape key="common.navigation.sidebar"}">
				{$sidebarCode}
			</div>
		{/if}
	</div><!-- pkp_structure_content -->

	{if !empty($isFullWidth) && $sidebarCode}
		<div class="nr_sidebar_band">
			<div class="pkp_structure_sidebar nr_wrap" role="complementary" aria-label="{translate|escape key="common.navigation.sidebar"}">
				{$sidebarCode}
			</div>
		</div>
	{/if}

	<footer class="pkp_structure_footer_wrapper nr_footer" role="contentinfo">
		<a id="pkp_content_footer"></a>
		<div class="nr_footer_inner">
			<div class="nr_footer_journal">
				<strong class="nr_footer_name">{$nrSiteName|escape}</strong>
				{if $currentContext}
					{if $nrPublisherLine}
						<div class="nr_footer_publisher">{if $nrPublisherUrl}<a href="{$nrPublisherUrl|escape}" rel="noopener">{$nrPublisherLine|escape}</a>{else}{$nrPublisherLine|escape}{/if}</div>
					{/if}
					{if $nrIssnLine}<div class="nr_footer_issn">{$nrIssnLine|escape}</div>{/if}
					{if $currentContext->getData('mailingAddress')}<div class="nr_address">{$currentContext->getData('mailingAddress')|strip_tags|escape}</div>{/if}
				{/if}
			</div>

			{if $currentContext}
				{assign var=nrContactEmail value=$currentContext->getData('contactEmail')}
				{assign var=nrContactPhone value=$currentContext->getData('contactPhone')}
				{if $nrContactEmail || $nrContactPhone}
					<div class="nr_footer_contact_wrap">
						<div class="nr_footer_label">{translate key="about.contact"}</div>
						<ul class="nr_footer_contact">
							{if $nrContactEmail}<li><span class="fa fa-envelope-o" aria-hidden="true"></span> <a href="mailto:{$nrContactEmail|escape}">{$nrContactEmail|escape}</a></li>{/if}
							{if $nrContactPhone}<li><span class="fa fa-phone" aria-hidden="true"></span> <a href="tel:{$nrContactPhone|regex_replace:"/[^0-9+]/":""|escape}">{$nrContactPhone|escape}</a></li>{/if}
						</ul>
					</div>
				{/if}
			{/if}

			{if $nrIndexedIn}
				<div class="nr_footer_indexed">
					<div class="nr_footer_label">{translate key="plugins.themes.nusantaraReborn.footer.indexedIn"}</div>
					<ul>
						{foreach from=$nrIndexedIn item=nrIndex}
							<li>{if $nrIndex.url}<a href="{$nrIndex.url|escape}" rel="noopener">{$nrIndex.name|escape}</a>{else}{$nrIndex.name|escape}{/if}</li>
						{/foreach}
					</ul>
				</div>
			{/if}

			{if $nrSocial}
				<div class="nr_footer_follow">
					<div class="nr_footer_label">{translate key="plugins.themes.nusantaraReborn.footer.followUs"}</div>
					<ul>
						{if $nrSocial.website}<li><a href="{$nrSocial.website|escape}" rel="noopener" aria-label="{translate|escape key="plugins.themes.nusantaraReborn.footer.website"}"><span class="fa fa-globe" aria-hidden="true"></span></a></li>{/if}
						{if $nrSocial.instagram}<li><a href="{$nrSocial.instagram|escape}" rel="noopener" aria-label="Instagram"><span class="fa fa-instagram" aria-hidden="true"></span></a></li>{/if}
						{if $nrSocial.x}<li><a href="{$nrSocial.x|escape}" rel="noopener" aria-label="X"><span class="fa fa-twitter" aria-hidden="true"></span></a></li>{/if}
						{if $nrSocial.youtube}<li><a href="{$nrSocial.youtube|escape}" rel="noopener" aria-label="YouTube"><span class="fa fa-youtube-play" aria-hidden="true"></span></a></li>{/if}
						{if $nrSocial.linkedin}<li><a href="{$nrSocial.linkedin|escape}" rel="noopener" aria-label="LinkedIn"><span class="fa fa-linkedin" aria-hidden="true"></span></a></li>{/if}
					</ul>
				</div>
			{/if}
		</div>

		{if $pageFooter}
			<div class="nr_footer_custom">
				<div class="nr_footer_custom_inner pkp_footer_content">{$pageFooter}</div>
			</div>
		{/if}

		<div class="nr_footer_bottom">
			<div class="nr_footer_bottom_inner">
				<div class="nr_footer_copy">&copy; {$nrYear} {$nrSiteName|escape}</div>
				{if $currentContext}
					<nav class="nr_footer_links" aria-label="{translate|escape key="plugins.themes.nusantaraReborn.footer.links"}">
						<a href="{url page="about" op="privacy"}">{translate key="plugins.themes.nusantaraReborn.footer.privacy"}</a>
						{if $nrTermsUrl}<a href="{$nrTermsUrl|escape}">{translate key="plugins.themes.nusantaraReborn.footer.terms"}</a>{/if}
						<a href="{url page="about" op="contact"}">{translate key="about.contact"}</a>
					</nav>
				{/if}
				<div class="nr_footer_platform">
					<a href="{url page="about" op="aboutThisPublishingSystem"}">{translate key="plugins.themes.nusantaraReborn.footer.platform" version=$nrOjsVersion}</a>
					<span>{translate key="plugins.themes.nusantaraReborn.footer.themeCredit"}</span>
				</div>
			</div>
		</div>
	</footer>
</div><!-- pkp_structure_page -->

{load_script context="frontend"}

{call_hook name="Templates::Common::Footer::PageFooter"}
</body>
</html>
