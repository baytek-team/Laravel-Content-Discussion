<?php
namespace Baytek\Laravel\Content\Types\Discussion\Seeders;

use Baytek\Laravel\Content\Seeder;

class DiscussionSeeder extends Seeder
{
    private $data = [
        [
            'key' => 'discussion',
            'title' => 'Discussion',
            'content' => \Baytek\Laravel\Content\Types\Discussion\Models\Discussion::class,
            'relations' => [
                ['parent-id', 'content-type']
            ]
        ],
        [
            'key' => 'discussion-topic',
            'title' => 'Discussion Topic',
            'content' => \Baytek\Laravel\Content\Types\Discussion\Models\Topic::class,
            'relations' => [
                ['parent-id', 'content-type'],
            ]
        ],
        [
            'key' => 'discussion-menu',
            'title' => 'Discussion Navigation Menu',
            'content' => '',
            'relations' => [
                ['content-type', 'menu'],
                ['parent-id', 'admin-menu'],
            ]
        ],
        [
            'key' => 'discussion-index',
            'title' => 'Discussions',
            'content' => 'discussion.topic.index',
            'meta' => [
                'type' => 'route',
                'class' => 'item',
                'append' => '</span>',
                'prepend' => '<i class="talk left icon"></i><span class="collapseable-text">',
            ],
            'relations' => [
                ['content-type', 'menu-item'],
                ['parent-id', 'discussion-menu'],
            ]
        ]
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
