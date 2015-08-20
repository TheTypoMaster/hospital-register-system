<?php

class WeixinPay extends \Eloquent{

    protected $table = 'weixin_pay';

    protected $fillable = array(
        'open_id',
        'trade_no',
        'time_start',
        'time_end',
        'time_expire',
        'attach',
        'total_fee',
        'result_code',
        'error_code',
        'error_message',
        'user_id',
        'record_id'
    );
}