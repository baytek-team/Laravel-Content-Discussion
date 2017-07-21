<?php

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
    	$discussion_ids = collect([]);

    	foreach(range(1,$total) as $index) {
    		//Choose a parent at random
    		//Can choose from discussions if any exist
    		if ($index > $total/10) {
    			$parent_id = rand(0,1) ? $topics->random()->id : $discussion_ids->random();
    		}
    		else {
    			$parent_id = $topics->random()->id;
    		}

    		$discussion = (factory(Discussion::class)->make());
    		$discussion->save();

    		//Add relationships
    		$discussion->saveRelation('content-type', $content_type);
    		$discussion->saveRelation('parent-id', $parent_id);

    		//Add metadata
    		$discussion->saveMetadata('author_id', $members->random()->id);

    		//Store the discussion IDs that can be used as parent_ids
    		$discussion_ids->push($discussion->id);
    	}

    	//Change some of the updated_at timestamps, so the discussions will appear edited
    }
}
