<?php

namespace HiFolks\JetTranslations\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class JetTranslationsExtractor extends Command
{

    const DEFAULT_VIEWS_PATH = 'vendor/laravel/jetstream/stubs/livewire/resources/views';
    const DEFAULT_LANG_PATH = 'vendor/jet-translations';
    const DEFAULT_LANG = 'it';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jet-trans:extract
    {--language=' . self::DEFAULT_LANG . ' : Language files (en or it or de or...)}
    {--path-views=' . self::DEFAULT_VIEWS_PATH . ' : path for views file (blade.php files)}
    {--path-lang=' . self::DEFAULT_LANG_PATH . ' : path for lang file ( <lang>.json file)}
    {--use-custom-lang-path : use custom Lang Path instead of default one}
    {--save-json : create or update json lang file}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract translation key from Jetstream blade templates/componen';

    /**
     * array of "info" to show at the end of the execution in table format.
     *
     * @var array<array>
     */
    private $report = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $pattern search files following the $pattern. For example "*.blade.php"
     * @param int $flags , flags according with glob php function
     * @param bool $walkIntoDirectories
     * @return array<string>|false
     */
    private static function findFiles(string $pattern, $flags = 0, $walkIntoDirectories = true)
    {
        $files = glob($pattern, $flags);
        if ($walkIntoDirectories) {
            foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
                $files = array_merge(
                    $files,
                    self::findFiles($dir . '/' . basename($pattern), $flags, $walkIntoDirectories)
                );
            }
        }
        return $files;
    }

    private function report($label, $value)
    {
        $item = [];
        $item[0] = $label;
        $item[1] = $value;
        $this->report[] = $item;
    }

    private function printReport()
    {
        $this->table(["Info", "Value"], $this->report);
    }

    private static function stripAbsolutePath($path)
    {
        $curDir = getcwd();
        if (Str::length($curDir) > 3) {
            if (Str::startsWith($path, $curDir)) {
                return "." . Str::after($path, $curDir);
            }
        }
        return $path;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $language = $this->option('language');
        $path = $this->option('path-views');
        $pathLang = $this->option('path-lang');
        $useCustomLangPath = $this->option('use-custom-lang-path');
        $saveJson = $this->option('save-json');

        if (!is_dir($path)) {
            $path = resource_path() . DIRECTORY_SEPARATOR . "views";
        }

        $prefixLangPath = "";
        if (!$useCustomLangPath) {
            $prefixLangPath = App::langPath() .  DIRECTORY_SEPARATOR;
        }
        $langPathDestination = $prefixLangPath . $pathLang;

        $filePattern = "/*.blade.php";
        $jsonFile = $langPathDestination . DIRECTORY_SEPARATOR . "{$language}.json";

        $files = self::findFiles($path . $filePattern);

        $this->report("Views path", self::stripAbsolutePath($path));
        $this->report("Views files", $filePattern);
        $this->report("Views files found", sizeof($files));
        $this->report("Lang path", self::stripAbsolutePath($langPathDestination));
        $this->report("Language", $language);
        $this->report("Language file", self::stripAbsolutePath($jsonFile));
        $this->report("Save Json", ($saveJson ? "Save" : "Don't save"));


        $trans_keys = [];
        $lh = 'trans|trans_choice|Lang::get|Lang::choice|Lang::trans|Lang::transChoice|@lang|@choice|__|$trans.get';
        $stringPattern = '[^\w](' . $lh . ')\((?P<quote>[\'"])(?P<string>(?:\\k{quote}|(?!\k{quote}).)*)\k{quote}[\),]';
        $regexp = '/' . $stringPattern . '/siU';
        $reportViewFiles = [];
        foreach ($files as $f) {
            preg_match_all($regexp, file_get_contents($f), $keys);
            $keys["string"] = str_replace("\\'", "'", $keys["string"]);

            $fileCount = [];
            $fileCount[0] = sizeof($keys["string"]);
            $fileCount[1] = $f;
            $reportViewFiles[] = $fileCount;
            $trans_keys = array_merge($trans_keys, $keys["string"]);
        }
        $this->table([ "Keys","File"], $reportViewFiles);
        $this->report("Keys found", sizeof($trans_keys));




        $wannaCreate = false;
        if (file_exists($jsonFile)) {
            $string = file_get_contents($jsonFile);
            $json_a = json_decode($string, true);
            $this->info("Language file contains some strings: " . sizeof($json_a));
        } else {
            $wannaCreate = true;
            $json_a = [];
        }

        $d = [];
        $countKeysFound = 0;
        $countKeysNotFound = 0;
        foreach ($trans_keys as $v) {
            if (key_exists($v, $json_a)) {
                $countKeysFound++;
                $d[$v] = $json_a[$v];
            } else {
                //$d[$v] = "## {$v} ##";
                $countKeysNotFound++;
                //$this->info($v);
                $d[$v] = "###";
            }
        }
        $this->report("Keys matched", $countKeysFound);
        $this->report("Keys missed in json", $countKeysNotFound);


        $string = stripslashes(json_encode($d, JSON_PRETTY_PRINT));
        //$this->info($string);
        if ($saveJson) {
            if (!file_exists($langPathDestination)) {
                $this->info("Lang path directory was not exist so, I'm going to create it");

                mkdir($langPathDestination, 0777, true);
            }
            $this->info("I'm going to create: ${jsonFile}");
            file_put_contents($jsonFile, $string);
        }

        $this->printReport();


        return 0;
    }
}
