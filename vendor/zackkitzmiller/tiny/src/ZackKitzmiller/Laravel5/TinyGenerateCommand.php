<?php namespace ZackKitzmiller\Laravel5;

use ZackKitzmiller\Tiny;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class TinyGenerateCommand extends Command {

    protected $name = 'tiny:generate';

    protected $description = "Generate a valid key";

    public function fire()
    {
        $key = Tiny::generate_set();

        $path = base_path('.env');

        if (file_exists($path) and getenv('LEAGUE_TINY_KEY') !== false) {
            $originalContent = file_get_contents($path);
            $content = str_replace('LEAGUE_TINY_KEY='.getenv('LEAGUE_TINY_KEY'), 'LEAGUE_TINY_KEY='.$key, $originalContent);

            file_put_contents($path, $content);
        } else {
            $fp = fopen($path, 'a');
            fwrite($fp, "\nLEAGUE_TINY_KEY=$key\n");
            fclose($fp);
        }

        $this->info("Tiny key [$key] has been set.");
    }

}
