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
    	$content_type = content('content-type/discussion-topic', false);

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
    	$content_type = content('content-type/discussion', false);
    	$topics = Topic::all();
    	$members = User::all();
    	$discussions = collect([]);

        $earliest_date = time() - (7*24*60*60); //1 week ago
        $latest_date = time(); //Now

    	foreach(range(1,$total) as $index) {
            $discussion = (factory(Discussion::class)->make());

            //Vary the dates, make some appear edited and some appear deleted
            $discussion->created_at = $this->randomDate($earliest_date, $latest_date);
            $discussion->updated_at = rand(0,5) ? $discussion->created_at : date('Y-m-d H:i:s', $latest_date);

            if (rand(0,$total/20)) {
                $discussion->status = Discussion::DELETED;
            }

            //Save the discussion
    		$discussion->save();

            //Choose a parent at random
            //Can choose from discussions if any exist
            if ($index > $total/10 && rand(0,2)) {
                $parent = $discussions->random();
            }
            else {
                $parent = $topics->random();
            }

    		//Add relationships
    		$discussion->saveRelation('content-type', $content_type);
    		$discussion->saveRelation('parent-id', $parent->id);

    		//Add metadata
    		$discussion->saveMetadata('author_id', $members->random()->id);

    		//Store the discussion IDs that can be used as parent_ids
    		$discussions->push($discussion);

            //Update the earliest date so all discussions will be created after it
            $earliest_date = strtotime($discussion->created_at);
    	}
    }

    protected function randomDate($start, $end)
    {
        return date('Y-m-d H:i:s', rand($start, $end));
    }
}
