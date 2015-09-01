<?php

class Message extends \Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */

    public $timestamps = false;

    protected $table = 'messages';

    protected $fillable = array(
        'from_uid',
        'to_uid',
        'content',
        'timestamp',
        'status'
    );
}