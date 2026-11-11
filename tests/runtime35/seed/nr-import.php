<?php
/**
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 *
 * LOCAL TEST STACK ONLY. Native XML import from the command line with a signed-in user (OJS 3.5).
 * Command-line imports log file events with user id 0, which the event_log foreign key rejects.
 * For this local import only, foreign key checks are relaxed on this connection and the log rows
 * are then credited to the importing user. OJS core is not modified.
 *
 *   php tools/nrImport.php <file.xml> <journalPath> <username>
 */

require(dirname(__FILE__) . '/bootstrap.php');

use APP\facades\Repo;
use Illuminate\Support\Facades\DB;
use PKP\cliTool\CommandLineTool;
use PKP\core\Registry;
use PKP\plugins\PluginRegistry;

class nrImport extends CommandLineTool
{
    public function execute()
    {
        [$file, $journalPath, $username] = array_pad($this->argv, 3, '');
        $user = Repo::user()->getByUsername($username, true);
        if (!$user) {
            echo "Unknown user {$username}\n";
            exit(1);
        }
        Registry::set('user', $user);
        PluginRegistry::loadCategory('importexport');
        $plugin = PluginRegistry::getPlugin('importexport', 'NativeImportExportPlugin');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            $args = ['import', $file, $journalPath, $username];
            $plugin->executeCLI($this->scriptName, $args);
        } finally {
            DB::table('event_log')->where('user_id', 0)->update(['user_id' => (int) $user->getId()]);
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}

$tool = new nrImport($argv ?? []);
$tool->execute();
