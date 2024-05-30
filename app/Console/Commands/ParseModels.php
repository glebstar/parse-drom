<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Dom\Node\Collection;
use App\Models\Model;

class ParseModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parse-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсит модели Audio с Drom.ru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('models')->delete();

        $dom = new Dom;
        $dom->loadFromUrl('https://www.drom.ru/catalog/audi/');

        $models = $dom->find('._501ok20');
        foreach ($models as $model) {
            /**
             * @var HtmlNode $model
             */
            $name = $model->innerHtml();
            $dom2 = new Dom;
            $dom2->loadStr($name);

            /**
             * @var Collection $svg;
             */
            $svg = $dom2->getElementsByTag('svg');

            if ($svg->count()) {
                $svg[0]->delete();
                unset($svg);
                $name = $dom2->outerHtml;
            }

            Model::create([
                'name' => $name,
                'url' => $model->getAttribute('href'),
            ]);
        }
    }
}
