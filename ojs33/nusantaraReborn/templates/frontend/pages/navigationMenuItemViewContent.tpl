{**
 * NusantaraReborn theme for OJS 3.3 — custom page (Settings > Website > Navigation > Custom Page), e.g. journal policies.
 * The theme script builds "On this page" and numbers the sections; content is unchanged without it.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Derived from lib/pkp/templates/frontend/pages/navigationMenuItemViewContent.tpl:
 * Copyright (c) 2014-2021 Simon Fraser University. Copyright (c) 2003-2021 John Willinsky.
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE-GPL-3.0.txt.
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title isFullWidth=true}

{include file="frontend/components/breadcrumbs.tpl" currentTitle=$title}

<div class="nr_policy_layout">
	<aside class="nr_policy_nav">
		<nav class="nr_box nr_toc" aria-labelledby="nrTocTitle" hidden>
			<h2 id="nrTocTitle">{translate key="plugins.themes.nusantaraReborn.policy.onThisPage"}</h2>
			<ol></ol>
		</nav>
		<div class="nr_box nr_related_info">
			<h2>{translate key="plugins.themes.nusantaraReborn.policy.relatedInformation"}</h2>
			<ul>
				<li><a href="{url page="about" op="submissions"}"><span class="fa fa-file-text-o" aria-hidden="true"></span>{translate key="about.authorGuidelines"}</a></li>
				<li><a href="{url page="submission"}"><span class="fa fa-paper-plane-o" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.home.submitManuscript"}</a></li>
				<li><a href="{url page="about" op="contact"}"><span class="fa fa-envelope-o" aria-hidden="true"></span>{translate key="plugins.themes.nusantaraReborn.policy.journalContact"}</a></li>
			</ul>
		</div>
	</aside>
	<div class="page nr_policy_page">
		<h1 class="page_title nr_title">{$title|escape}</h1>
		{$content}
	</div>
</div>

{include file="frontend/components/footer.tpl" isFullWidth=true}
