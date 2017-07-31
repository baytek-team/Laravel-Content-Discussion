<?php

$categoryRegex = '[\w|-]+\/?[\w|-]+';

Route::group([
	'prefix' => '/discussions',
], function () use ($categoryRegex) {

	// Gets a list of all approved discussions
	Route::get('/', 'DiscussionController@index');

	// Get all approved top-level discussions (for the dashboard)
	Route::get('/top', 'DiscussionController@top');
	Route::get('/top/options/{options?}', 'DiscussionController@top')
		->where('options', '.*?');

	Route::get('/dashboard', 'DiscussionController@dashboard');

	// Search for a discussion given a query
	Route::get('/search', 'DiscussionController@search');

	// Search results and have all the options
	Route::get('/search/options/{options?}', 'DiscussionController@search')
		->where('options', '.*?');

	// Gets a list of all discussions regardless of status
	Route::get('/all', 'DiscussionController@all');

	// Gets a list of latest approved discussions
	Route::get('/latest', 'DiscussionController@latest');

	// Gets a list of oldest approved discussions
	Route::get('/oldest', 'DiscussionController@oldest');

	// Gets a list of approved discussion topics
	Route::get('/topics', 'TopicController@index');

	// Gets a list of discussion topics regardless of status
	Route::get('/topics/all', 'TopicController@all');

	// Get a discussions for a specific discussion topic
	Route::get('/{topic}', 'TopicController@get');

	// Get a list of discussions for a specific discussion topic
	Route::get('/{topic}', 'DiscussionController@topicDiscussions')
		->where(['topic' => $categoryRegex]);

	// Get resources with all sorting, offsetting and limiting options
	Route::get('/{topic}/options/{options?}', 'DiscussionController@topicDiscussions')
		->where(['topic' => $categoryRegex])
		->where('options', '.*?');

	// Get a specific discussion
	Route::get('/{topic}/discussion/{discussion}', 'DiscussionController@get')
		->where(['topic' => $categoryRegex]);

	// Share a discussion
	Route::post('/{topic}/discussion/{discussion}/share', 'DiscussionController@share')
		->where(['topic' => $categoryRegex]);

	// // Create a discussion
	Route::post('/', 'DiscussionController@create');

	// Respond to a discussion
	Route::post('/{discussion}/reply', 'DiscussionController@reply');

	// Delete a discussion
	Route::post('/{discussion}/delete', 'DiscussionController@delete');

	// Edit a discussion or response
	Route::post('/{discussion}/save', 'DiscussionController@save');

	// Mark a discussion a favourite
	Route::post('/{topic}/discussion/{discussion}/favourite', 'DiscussionController@favourite')
		->where('topic', $categoryRegex);
});