<?php
namespace Baytek\Laravel\Content\Types\Discussion\Seeders;

use Baytek\Laravel\Content\Seeder;

class DiscussionSeeder extends Seeder
{
    private $data = [
        [
            'key' => 'discussion',
            'title' => 'Discussion',
            'content' => Baytek\Laravel\Content\Types\Discussion\Models\Discussion::class,
            'relations' => [
                ['parent-id', 'content-type']
            ]
        ],
        [
            'key' => 'discussion-topic',
            'title' => 'Discussion Topic',
            'content' => Baytek\Laravel\Content\Types\Discussion\Models\Topic::class,
            'relations' => [
                ['parent-id', 'content-type'],
            ]
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedStructure($this->data);
    }
}
