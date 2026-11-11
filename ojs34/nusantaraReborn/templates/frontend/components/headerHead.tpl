{**
 * NusantaraReborn theme for OJS 3.4 — frontend <head>.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from lib/pkp/templates/frontend/components/headerHead.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *
 * The journal favicon and all metadata come from OJS (load_header). The theme adds no favicon.
 *}
<head>
	<meta charset="{$defaultCharset|escape}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	{if $nrMetaColour}<meta name="theme-color" content="{$nrMetaColour|escape}">{/if}
	<title>
		{$pageTitleTranslated|strip_tags}
		{* Add the journal name to the end of page titles *}
		{if $requestedPage|escape|default:"index" != 'index' && $currentContext && $currentContext->getLocalizedName()}
			| {$currentContext->getLocalizedName()|escape}
		{/if}
	</title>

	{load_header context="frontend"}
	{load_stylesheet context="frontend"}
</head>
