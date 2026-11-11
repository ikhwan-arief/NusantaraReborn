{**
 * NusantaraReborn theme for OJS 3.3 — site header, navigation and inner-page banner.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from lib/pkp/templates/frontend/components/header.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *
 * Keeps the OJS menu areas, ids and classes so Default Theme JS, custom menus and plugins work.
 * Identity comes only from OJS: the journal logo when uploaded, otherwise the journal name as text.
 *
 * @uses $isFullWidth bool Page-level override to hide the sidebar column.
 * @uses $nrBannerTitle, $nrBannerText, $nrBannerCrumbs Optional inner-page banner content.
 *}
{strip}
	{assign var="showingLogo" value=true}
	{if !$displayPageHeaderLogo}
		{assign var="showingLogo" value=false}
	{/if}
	{assign var="nrIsHome" value=false}
	{if !$requestedPage || $requestedPage === 'index'}
		{assign var="nrIsHome" value=true}
	{/if}
	{if $currentContext}
		{assign var="nrSiteName" value=$currentContext->getLocalizedName()}
	{elseif $siteTitle}
		{assign var="nrSiteName" value=$siteTitle}
	{elseif $displayPageHeaderTitle}
		{assign var="nrSiteName" value=$displayPageHeaderTitle}
	{else}
		{assign var="nrSiteName" value=$applicationName}
	{/if}
{/strip}
<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{if !$pageTitleTranslated}{capture assign="pageTitleTranslated"}{translate key=$pageTitle}{/capture}{/if}
{include file="frontend/components/headerHead.tpl"}
<body class="pkp_page_{$requestedPage|escape|default:"index"} pkp_op_{$requestedOp|escape|default:"index"} nr_theme nr_scheme_{$nrColourScheme|escape} nr_ornaments_{$nrOrnaments|escape} nr_fonts_{$nrFontSource|escape}{if $nrIsHome && $currentContext} nr_is_home{/if}{if $showingLogo} has_site_logo{/if}" dir="{$currentLocaleLangDir|escape|default:"ltr"}">
<div class="pkp_structure_page">
	<header class="pkp_structure_head nr_header" id="headerNavigationContainer" role="banner">
		{include file="frontend/components/skipLinks.tpl"}

		{capture assign="homeUrl"}{url page="index" router=$smarty.const.ROUTE_PAGE}{/capture}
		<div class="nr_header_top">
			<div class="nr_header_inner">
				<div class="nr_brand_cluster{if !$showingLogo} is-text-only{/if}">
					{if $showingLogo}
						<a href="{$homeUrl}" class="nr_brand_logo">
							<img src="{$publicFilesDir}/{$displayPageHeaderLogo.uploadName|escape:"url"}" alt="{if $displayPageHeaderLogo.altText != ''}{$displayPageHeaderLogo.altText|escape}{else}{$nrSiteName|escape}{/if}"{if $displayPageHeaderLogo.width} width="{$displayPageHeaderLogo.width|escape}" height="{$displayPageHeaderLogo.height|escape}"{/if} decoding="async">
						</a>
					{/if}
					{if $nrSiteName}
						<div class="nr_journal_identity">
							<div class="nr_journal_name"><a href="{$homeUrl}">{$nrSiteName|escape}</a></div>
							{if $currentContext && $nrTagline}<div class="nr_header_tagline">{$nrTagline|escape}</div>{/if}
							{if $currentContext && $nrIssnLine}<div class="nr_header_issn">{$nrIssnLine|escape}</div>{/if}
						</div>
					{/if}
				</div>

				<div class="nr_header_actions">
					{if $currentContext && $requestedPage !== 'search'}
						<a href="{url page="search"}" class="nr_search_link nr_search_mobile" aria-label="{translate|escape key="common.search"}">
							<span class="fa fa-search" aria-hidden="true"></span>
						</a>
					{/if}
					{if $currentContext}
						<a class="nr_btn nr_btn_primary nr_header_submit" href="{url page="submission"}">
							<span class="fa fa-paper-plane" aria-hidden="true"></span>
							{translate key="plugins.themes.nusantaraReborn.home.submitManuscript"}
						</a>
					{/if}
					<button class="pkp_site_nav_toggle nr_nav_toggle" aria-label="{translate|escape key="plugins.themes.nusantaraReborn.nav.openMenu"}" aria-controls="nrSiteNav">
						<span>{translate key="plugins.themes.nusantaraReborn.nav.openMenu"}</span>
					</button>
				</div>
			</div>
		</div>

		{capture assign="primaryMenu"}
			{load_menu name="primary" id="navigationPrimary" ulClass="pkp_navigation_primary"}
		{/capture}
		<nav class="pkp_site_nav_menu nr_nav" id="nrSiteNav" aria-label="{translate|escape key="common.navigation.site"}">
			<a id="siteNav"></a>
			<div class="nr_nav_inner">
				<div class="pkp_navigation_primary_row">
					<div class="pkp_navigation_primary_wrapper">
						{if $currentContext}
							<a href="{$homeUrl}" class="nr_nav_home{if $nrIsHome} nr_current{/if}"{if $nrIsHome} aria-current="page"{/if}>{translate key="plugins.themes.nusantaraReborn.nav.home"}</a>
						{/if}
						{$primaryMenu}
					</div>
				</div>
				<div class="pkp_navigation_user_wrapper nr_utility" id="navigationUserWrapper">
					{if $currentContext && $requestedPage !== 'search'}
						<a href="{url page="search"}" class="nr_search_link nr_search_desktop">
							<span class="fa fa-search" aria-hidden="true"></span>
							<span>{translate key="common.search"}</span>
						</a>
					{/if}
					{load_menu name="user" id="navigationUser" ulClass="pkp_navigation_user" liClass="profile"}
				</div>
			</div>
		</nav>
	</header>

	{* Inner-page banner: journal identity over the preset background or the uploaded Homepage Image. *}
	{if !$nrIsHome && $currentContext}
		<section class="nr_page_banner{if !$nrUsingCustomHero} is-preset{/if}">
			<div class="nr_page_banner__media" aria-hidden="true">
				<img src="{$nrHeroImageUrl|escape}"{if $nrHeroImageSrcset} srcset="{$nrHeroImageSrcset|escape}" sizes="100vw"{/if} alt="" width="1600" height="900" decoding="async">
			</div>
			<div class="nr_page_banner__inner">
				{if $nrBannerCrumbs}<div class="nr_page_banner__crumbs">{$nrBannerCrumbs}</div>{/if}
				<div class="nr_page_banner__title">{if $nrBannerTitle}{$nrBannerTitle|escape}{else}{$currentContext->getLocalizedName()|escape}{/if}</div>
				{if $nrBannerText}
					<p>{$nrBannerText|escape}</p>
				{elseif !$nrBannerTitle && $nrHeroDescription}
					<p>{$nrHeroDescription|escape}</p>
				{/if}
			</div>
			{if $nrHeroCaption}<p class="nr_hero_caption">{$nrHeroCaption|escape}</p>{/if}
		</section>
	{/if}

	{if $isFullWidth}{assign var=hasSidebar value=0}{/if}
	<div class="pkp_structure_content{if $hasSidebar} has_sidebar{/if}">
		<div class="pkp_structure_main" role="main">
			<a id="pkp_content_main"></a>
