<?php namespace ZackKitzmiller;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class TinyGenerateCommand extends Command {

    protected $name = 'tiny:generate';

    protected $description = "Generate a valid key";

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function fire()
    {
        list($path, $contents) = $this->getKeyFile();

        $key = Tiny::generate_set();

        $contents = str_replace($this->laravel['config']['zackkitzmiller/tiny::key'], $key, $contents);

        $this->files->put($path, $contents);

        $this->laravel['config']['zackkitzmiller/tiny::key'] = $key;

        $this->info("Tiny key [$key] has been set.");
    }

    protected function getKeyFile()
    {
        $env = $this->option('env') ? $this->option('env').'/' : '';

        $contents = $this->files->get($path = $this->laravel['path']."/config/packages/zackkitzmiller/tiny/{$env}config.php");

        return array($path, $contents);
    }

}
