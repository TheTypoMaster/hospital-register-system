@extends('layouts.master')

@section('title')
    查看地图
@stop

@section('css')
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/dist/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/dist/css/hospital/traffic_guide.css" />
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?type=quick&v=1.0&ak={{{ $baidu_map_app_key }}}"></script>
@stop

@section('js-common')
@stop

@section('js-specify')
    <script type="text/javascript">
        $(document).ready(function(){
            var map_width = parseInt( $("#baidu-map").css( "width" ) );
            $("#baidu-map").css( "height", map_width * 0.65 + "px" );

            var get_location_callback = function( response ){
                var map_level = 15;
                var map = new BMap.Map("baidu-map");
                var user_point = new BMap.Point( response.longitude, response.latitude );
                var dest_point = new BMap.Point( {{{ result['longitude'], result['latitude'] }}});
                map.centerAndZoom( user_point, map_level );
                map.addControl(new BMap.ZoomControl({ anchor: BMAP_ANCHOR_TOP_LEFT }));

                var my_geo = new BMap.Geocoder();
                my_geo.getLocation( 
                    user_point,
                    function( result ){
                        if ( result ){
                            $("#current-pos").html( result.address );
                        }
                    }
                );

                var routeSearch=new BMap.RouteSearch();  
                var start = { latlng: user_point };
                var end = { latlng: dest_point, , name = {{{ result['name'] }}} };
                var opt = { mode:BMAP_MODE_NAVIGATION };
                var ss = new BMap.RouteSearch();
                routeSearch.routeCall( start, end, );
            }
            
            wx.config( {
                debug: false,
                appId: '{{{ $app_id }}}',
                timestamp: '{{{ $sign_package["timestamp"] }}}',
                nonceStr: '{{{ $sign_package["nonce_str"] }}}',
                signature: '{{{ $sign_package["signature"] }}}',
                jsApiList: [
                    "getLocation"
                ]
            });
            
            wx.ready(function(){
                wx.getLocation({
                    type: 'wgs84',
                    success: get_location_callback
                });
            });
        });
        
    </script>
@stop

@section('body-title')
    <img src="/images/icons/location_flag.png">当前定位：<span id="current-pos">无</span>
@stop

@section('body-main')

	<div class="map-wrap">
	    <div id="baidu-map"></div>
	</div>

    <div class="para-wrap">

        {{ $traffic_intro }}
        {{ $traffic_guide }}
        <p>
            联系电话：{{{ $phone }}}
        </p>
    </div>
@stop


