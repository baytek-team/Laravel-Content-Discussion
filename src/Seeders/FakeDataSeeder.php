<?php

namespace Baytek\Laravel\Content\Types\Discussion\Seeders;

use Illuminate\Database\Seeder;

use Baytek\Laravel\Content\Types\Discussion\Models\Discussion;
use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Users\User;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->generateDiscussionTopics();
        $this->generateDiscussions();
    }

    public function generateDiscussionTopics($total = 10)
    {
        $content_type = content_id('content-type/discussion-topic');

        foreach(range(1,$total) as $index) {
            $topic = (factory(Topic::class)->make());
            $topic->save();

            //Add relationships
            $topic->saveRelation('content-type', $content_type);
            $topic->saveRelation('parent-id', $content_type);

            //Add metadata
            $topic->saveMetadata('author_id', 1);
        }
    }

    public function generateDiscussions($total = 100)
    {
        //Generate discussions
        //Assign them to a topic or an existing discussion
        $content_type = content_id('content-type/discussion');
        $topics = Topic::all();
        $members = User::all();
        $discussions = collect([]);
        $ancestor_ids = collect([]);

        $earliest_date = time() - (7*24*60*60); //1 week ago
        $latest_date = time(); //Now

        foreach(range(1,$total) as $index) {
            $discussion = (factory(Discussion::class)->make());

            //Vary the dates, make some appear edited
            $discussion->created_at = $this->randomDate($earliest_date, $latest_date);
            $discussion->updated_at = rand(0,5) ? $discussion->created_at : date('Y-m-d H:i:s', $latest_date);

            //Save the discussion
            $discussion->save();
            $ancestor = null;

            //Choose a parent at random
            //Can choose from discussions if any exist
            if ($index > $total/10 && rand(0,2)) {
                $parent = $discussions->random();

                //Update the respnose count
                $ancestor = $parent;
                $parent_relation_id = content_id('parent-id');

                while ($ancestor_ids->search($ancestor->id) === false) {
                    foreach($ancestor->relations()->get() as $relation) {
                        if ($relation->relation_type_id == $parent_relation_id ) {
                            $ancestor = $discussions->where('id', $relation->relation_id)->first();
                        }
                    }
                }

                $ancestor->load('meta');
                $response_count = (int)$ancestor->getMeta('response_count');

                $ancestor->saveMetadata('response_count', (int)$ancestor->getMeta('response_count') + 1);
            }
            else {
                $parent = $topics->random();
                $ancestor_ids->push($discussion->id);
            }

            //Add relationships
            $discussion->saveRelation('content-type', $content_type);
            $discussion->saveRelation('parent-id', $parent->id);

            //Add metadata
            $discussion->saveMetadata('author_id', $members->random()->id);
            $discussion->saveMetadata('response_count', 0);

            //Store the discussion IDs that can be used as parent_ids
            //But only if the discussion is top level or a reply
            if (!$ancestor || $ancestor->id == $parent->id) {
              $discussions->push($discussion);
            }

            //Update the earliest date so all discussions will be created after it
            $earliest_date = strtotime($discussion->created_at);
        }
    }

    protected function randomDate($start, $end)
    {
        return date('Y-m-d H:i:s', rand($start, $end));
    }
}
