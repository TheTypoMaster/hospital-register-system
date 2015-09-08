# -*- coding:utf-8 -*-v

import json
import time
import hashlib
import urllib
import collections
import tornado.web
import tornado.gen
import tornado.ioloop

import config.app
import config.mysql

class base_handler( tornado.web.RequestHandler ):

    def get_current_user( self ):
        return self.get_secure_cookie('user')

class chat_handler( base_handler ):

    def __match_method( self, httpmethod, method ):

        for k, v in self.method[httpmethod].iteritems():
            if k == method:
                return v
        return self.not_found

    def __find_users( self, user_id ):

        sql = 'select id, real_name, photo from users where id <> {current}'.format( current = user_id )

        self.mysql_cursor.execute( sql )

        return self.mysql_cursor.fetchall()

    def __get_user_info( self, user_id ):

        sql = 'select id, photo, real_name from users where id = {user_id}'.format( user_id = user_id )

        print sql

        self.mysql_cursor.execute( sql )

        return self.mysql_cursor.fetchone()

    def __get_recent_chat_users( self, to_uid, limit = 20 ):

        sql = 'select distinct from_uid from messages where to_uid = {to_uid} \
               and status in (0, 1) limit {limit}'.format( to_uid = to_uid, limit = limit )

        self.mysql_cursor.execute( sql )

        recent_chat_user_id = [ str( u[0] ) for u in self.mysql_cursor.fetchall() ]

        if len( recent_chat_user_id ):

			sql = 'select id, real_name, photo from users where id in ({users})'.format( users = ','.join( recent_chat_user_id ) )

			self.mysql_cursor.execute( sql )
			return self.mysql_cursor.fetchall()
        return []

    def __search_user_by_real_name( self, name ):

        sql = 'select id, real_name from users where real_name = "{name}"'.format( name = name )

        self.mysql_cursor.execute( sql )

        return self.mysql_cursor.fetchall()

    def __send_message( self, from_uid, to_uid, content ):

        timestamp = int( time.time() )
        init_status = 0

        sql = 'insert into messages (from_uid, to_uid, timestamp, content, status) \
               values ({from_uid}, {to_uid}, {timestamp}, "{content}", {status})'.format( 
                from_uid = from_uid, to_uid = to_uid, timestamp = timestamp, content = content, status = init_status )

        try:
            self.mysql_cursor.execute( sql )
            self.application.database.commit()
        except:
            self.application.database.rollback()
            raise

    def __retrieve_message( self ):

        sql = 'select id, from_uid, content, timestamp from messages \
               where to_uid = {user_id} and status = 0'.format( user_id = self.current_user )
        count = self.mysql_cursor.execute( sql )
            
        if count:
            messages = self.mysql_cursor.fetchall()
            sql = 'update messages set status = 1 where id in ({ids})'.format( ids = ','.join( [ str( m[0] ) for m in messages ] ) )

            try:
                self.mysql_cursor.execute( sql )
                self.application.database.commit()
                return messages
            except:
                self.application.database.rollback()
            else:
                return None
        
        return None

    def __get_records( self, tar_uid, limit = 20 ):

        sql = 'select from_uid, to_uid, timestamp, content from messages \
               where ( ( from_uid = {tar_uid} and to_uid = {cur_uid} ) \
               or ( from_uid = {cur_uid} and to_uid = {tar_uid} ) ) \
               and status = 1 order by timestamp limit {limit}'.format( tar_uid = tar_uid, cur_uid = self.current_user, limit = limit )

        self.mysql_cursor.execute( sql )

        return self.mysql_cursor.fetchall()

    def prepare( self ):

        self.mysql_cursor = self.application.database.cursor()

        self.not_login_message = {
            'error_code': 1,
            'message': 'Please Login'
        }

        self.method = {
            'get':{
                'test': self.test,
                'search': self.search,
                'receive': self.receive,
                'records': self.get_records,
                'user_info': self.get_user_info,
                'validate_login': self.validate_login
            },
            'post': {
                'send': self.send,
                'validate': self.validate
            }
        }

    @tornado.gen.coroutine
    def get( self, sub_uri ):
        
        invoke_method = self.__match_method( 'get', sub_uri )

        if invoke_method != self.validate_login:
            if not self.current_user:
                self.finish( json.dumps( self.not_login_message ) )
                return

        result = yield invoke_method()
        
        self.finish( result )

    @tornado.gen.coroutine
    def post( self, sub_uri ):
        
        invoke_method = self.__match_method( 'post', sub_uri )

        if invoke_method != self.validate_login:
            if not self.current_user:
                self.finish( json.dumps( self.not_login_message ) )
                return

        result = yield invoke_method()

        self.finish( result )

    @tornado.gen.coroutine
    def validate( self ):

        user_id     = self.get_argument( 'uid', '' )
        timestamp   = self.get_argument( 'time', '' )
        origin_sign = self.get_argument( 'sign', '' )

        message = {
            'error_code': 0,
            'message': 'ok'
        }

        if self.__check_sign( user_id, timestamp, origin_sign ):
            self.set_secure_cookie( 'user', user_id )
        else:
            message = {
                'error_code': 1,
                'message': 'Invalid login'
            }

        raise tornado.gen.Return( json.dumps( message ) ) 

    @tornado.gen.coroutine
    def validate_login( self ):

        user_id     = self.get_argument( 'uid', '' )
        timestamp   = self.get_argument( 'time', '' )
        origin_sign = self.get_argument( 'sign', '' )

        if self.__check_sign( user_id, timestamp, origin_sign ):
            users = self.__get_recent_chat_users( user_id )

            self.set_secure_cookie( 'user', user_id )
            raise tornado.gen.Return( self.render_string( 'index.html', users = users, primary_host = config.app.primary_host ) )
        else:
            message = {
                'error_code': 1,
                'message': 'Invalid login'
            }
            raise tornado.gen.Return( json.dumps( message ) )

    def __check_sign( self, user_id, timestamp, origin_sign ):

        sign_package = {
            'token': config.app.sign_token,
            'user_id': user_id,
            'timestamp': timestamp
        }
        sorted_sign_package = collections.OrderedDict(sorted(sign_package.items(), key=lambda t:t[0]))
        sign_string = urllib.urlencode( sorted_sign_package )

        sha1 = hashlib.sha1()
        sha1.update( sign_string )
        check_sign = sha1.hexdigest()

        return check_sign == origin_sign

    @tornado.gen.coroutine
    def send( self ):

        from_uid = self.current_user
        to_uid   = self.get_argument( 'to_uid', '' )
        content  = self.get_argument( 'content', '' )

        message = {
            'error_code': 0,
            'message': 'ok'
        }

        try:
            self.__send_message( from_uid, to_uid, content.encode( config.mysql.character_set_client ) )
        except:
            message['error_code'] = 1
            message['message'] = 'Fail'

        raise tornado.gen.Return( json.dumps( message ) )

    @tornado.gen.coroutine
    def receive( self ):

        start_timestamp     = int( time.time() )
        current_timestamp   = int( time.time() )

        results = []

        print 'user id: {user_id}, start time: {time}'.format( user_id=self.current_user, time=start_timestamp )
        
        while ( current_timestamp - start_timestamp ) < 30 :
            
            messages = self.__retrieve_message()
            
            if messages is not None:
                results = messages
                break

            yield tornado.gen.sleep( 0.05 )
            current_timestamp = int( time.time() )

        print 'user id: {user_id}, end time: {time}'.format( user_id=self.current_user, time=current_timestamp )

        results = [ { 'from_uid': m[1], 'content': m[2], 'timestamp': m[3] } for m in results ]

        raise tornado.gen.Return( json.dumps( { 'error_code': 0, 'messages': results } ) )

    @tornado.gen.coroutine
    def search( self ):

        user_real_name = self.get_argument( 'user_name', '' ).encode( config.mysql.character_set_client  )

        users = self.__search_user_by_real_name( user_real_name )

        results = [ { 'user_id': u[0], 'name': u[1] } for u in users ]

        raise tornado.gen.Return( json.dumps( { 'error_code': 0, 'users': results } ) )

    @tornado.gen.coroutine
    def get_user_info( self ):

        user_info = self.__get_user_info( self.get_argument( 'user_id', '' ) )

        results = {
            'id': user_info[0],
            'photo': config.app.primary_host + user_info[1] if user_info[1] else '/static/images/u70.png',
            'name': user_info[2]
        }

        raise tornado.gen.Return( json.dumps( { 'error_code': 0, 'user_info': results } ) );

    @tornado.gen.coroutine
    def get_records( self ):

        tar_uid = self.get_argument( 'tar_uid', '' )

        records = self.__get_records( tar_uid )

        results = [ { 'type': 'from' if r[0] == int( tar_uid ) else 'self', 
                      'timestamp': r[2], 'content': r[3] } 
                      for r in records ]

        raise tornado.gen.Return( json.dumps( { 'error_code': 0, 'records': results } ) )

    @tornado.gen.coroutine
    def not_found( self ):

        raise tornado.gen.Return( 'Not Found' )

    @tornado.gen.coroutine
    def test( self ):

        raise tornado.gen.Return(  )
