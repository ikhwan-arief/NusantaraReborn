<?php
/**
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Code license: GPL-3.0-or-later.
 *
 * TEST FIXTURE ONLY (never packaged). Prints visible markers through the frontend hooks a theme
 * must keep, so automated tests can prove each insertion point still renders exactly once.
 */
import('lib.pkp.classes.plugins.GenericPlugin');

class NrHookFixturePlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {
            $hooks = [
                'Templates::Index::journal' => 'NR-HOOK-INDEX',
                'Templates::Article::Main' => 'NR-HOOK-ARTICLE-MAIN',
                'Templates::Article::Details' => 'NR-HOOK-ARTICLE-DETAILS',
                'Templates::Article::Footer::PageFooter' => 'NR-HOOK-ARTICLE-FOOTER',
                'Templates::Common::Footer::PageFooter' => 'NR-HOOK-PAGE-FOOTER',
            ];
            foreach ($hooks as $hook => $marker) {
                HookRegistry::register($hook, function ($hookName, $args) use ($marker) {
                    $args[2] .= '<div class="nr-hook-fixture" data-marker="' . $marker . '">' . $marker . '</div>';
                    return false;
                });
            }
            HookRegistry::register('TemplateManager::display', function ($hookName, $args) {
                $args[0]->addHeader('nrHookFixtureHead', '<meta name="nr-hook-fixture" content="NR-HOOK-HEAD">');
                return false;
            });
        }
        return $success;
    }

    public function getDisplayName()
    {
        return 'NusantaraReborn hook fixture (test only)';
    }

    public function getDescription()
    {
        return 'Prints markers through frontend hooks for automated theme tests.';
    }
}
