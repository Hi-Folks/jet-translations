<?php

namespace HiFolks\JetTranslations\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class JetTranslationsExtractor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jet-trans:extract
    {--language=it : Language files (en or it or de or...)}
    {--path-views=vendor/laravel/jetstream/stubs/livewire/resources/views : path for views file (blade.php files)}
    {--path-lang=vendor/jet-translations : path for lang file ( <lang>.json file)}
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
    public static function findFiles(string $pattern, $flags = 0, $walkIntoDirectories = true)
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

        if ($path === "resources/views") {
            $path = resource_path() . DIRECTORY_SEPARATOR . "views";
        } elseif (!is_dir($path)) {
            $path = resource_path() . DIRECTORY_SEPARATOR . "views";
        }
        // $path = "vendor/laravel/jetstream/stubs/livewire/resources/views";
        $prefixLangPath = "";
        if (!$useCustomLangPath) {
            $prefixLangPath = App::langPath().  DIRECTORY_SEPARATOR;
        }
        $langPathDestination = $prefixLangPath . $pathLang;

        $filePattern = "/*.blade.php";

        $this->info("Extracting strings from this path :" . $path);
        $this->info("Views pattern files               :" . $filePattern);
        $this->info("Path for localization files       :" . $langPathDestination);
        $this->info("Language                          :" . $language);
        $this->info("Want to save json                 :" . $saveJson);


        $files = self::findFiles($path . $filePattern);
        $this->info("Views files                        :" . sizeof($files));
        $trans_keys = [];
        $functions = [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice',
            '__',
            '$trans.get',
        ];
        $stringPattern =
            "[^\w]" .                                     // Must not have an alphanum before real method
            '(' . implode('|', $functions) . ')' .             // Must start with one of the functions
            "\(\s*" .                                       // Match opening parenthesis
            "(?P<quote>['\"])" .                            // Match " or ' and store in {quote}
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)" . // Match any string that can be {quote} escaped
            "\k{quote}" .                                   // Match " or ' previously matched
            "\s*[\),]";                                    // Close parentheses or new parameter

        $lh = 'trans|trans_choice|Lang::get|Lang::choice|Lang::trans|Lang::transChoice|@lang|@choice|__|$trans.get';
        $stringPattern = '[^\w](' . $lh . ')\((?P<quote>[\'"])(?P<string>(?:\\k{quote}|(?!\k{quote}).)*)\k{quote}[\),]';
        $regexp = '/' . $stringPattern . '/siU';
        foreach ($files as $f) {
            //$this->info($regexp);
            preg_match_all($regexp, file_get_contents($f), $keys);
            $count = 0;
            $keys["string"] = str_replace("\\'", "'", $keys["string"], $count);
            if ($count > 0) {
                //dd($keys["string"]);
            }

            $trans_keys = array_merge($trans_keys, $keys["string"]);
            //$this->info("Resource path: {$f}!");
        }
        $this->info("Keys found                        :" . sizeof($trans_keys));

        //$jsonFile = App::langPath() . DIRECTORY_SEPARATOR . "{$language}.json";



        $jsonFile = $langPathDestination . DIRECTORY_SEPARATOR . "{$language}.json";
        $this->info("Language file: " . $jsonFile);

        $wannaCreate = false;
        if (file_exists($jsonFile)) {
            $string = file_get_contents($jsonFile);
            $json_a = json_decode($string, true);
            $this->info("Language file contains some strings: " . sizeof($json_a));
        } else {
            $wannaCreate = true;
            $json_a = [];
        }
        //dd($json_a);

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
                $this->info($v);
                $d[$v] = "###";
            }
        }
        $this->info("Keys found     : " . $countKeysFound);
        $this->info("Keys NOT found : " . $countKeysNotFound);

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


        return 0;
    }
}
