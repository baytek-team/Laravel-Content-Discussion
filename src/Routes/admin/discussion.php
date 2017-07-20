<?php

// Discussion Routes
Route::group(['as' => 'discussion.'], function () {
    Route::get('/discussions/topic/pending', 'TopicController@pending')->name('topic.pending');
    Route::get('/discussions/topic/deleted', 'TopicController@deleted')->name('topic.deleted');
    Route::post('/discussions/topic/{topic}/approve', 'TopicController@approve')->name('topic.approve');
    Route::post('/discussions/topic/{topic}/decline', 'TopicController@decline')->name('topic.decline');

    Route::resource('/discussions/topic', TopicController::class);

    // Route::group(['as' => 'topic.'], function () {
    //     Route::get('/discussions/topic/{topic}/discussion', DiscussionController::class);
    // });
});

Route::get('/discussions/discussion/deleted', 'DiscussionController@deleted')->name('discussion.deleted');
Route::post('/discussions/discussion/{discussion}/approve', 'DiscussionController@approve')->name('discussion.approve');
Route::post('/discussions/discussion/{discussion}/decline', 'DiscussionController@decline')->name('discussion.decline');

Route::get('/discussions/{discussion}/children', 'DiscussionController@children')->name('discussion.children');

//Discussion response routes
Route::get('/discussions/response/{discussion}/edit', 'DiscussionController@editResponse')->name('discussion.editResponse');
Route::put('/discussions/response/{discussion}/update', 'DiscussionController@updateResponse')->name('discussion.updateResponse');

Route::resource('/discussions/discussion', DiscussionController::class);
