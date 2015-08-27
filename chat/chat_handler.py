import json
import time
import hashlib
import urllib
import collections
import tornado.web
import tornado.gen
import tornado.ioloop

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

        sql = 'select id, nickname, photo from users where id <> {current}'.format( current = user_id )

        print sql

        self.mysql_cursor.execute( sql )

        return self.mysql_cursor.fetchall()

    def __get_user_info( self, user_id ):

        sql = 'select id, nickname, photo from users where id = {user_id}'.format( user_id = user_id )

        self.mysql_cursor.execute( sql )

        return self.mysql_cursor.fetchone()

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

    def __retrieve_message( self ):

        sql = 'select id, from_uid, content, timestamp from messages \
               where to_uid = {user_id} and status = 0'.format( user_id = self.current_user )
        count = self.mysql_cursor.execute( sql )
            
        if count:
            messages = self.mysql_cursor.fetchall()
            sql = 'update messages set status = 1 where id in ({ids})'.format( ids = ','.join( [ str( m[0] ) for m in messages ] ) )
            print sql

            try:
                self.mysql_cursor.execute( sql )
                self.application.database.commit()
                return messages
            except:
                self.application.database.rollback()
        
        return None

    def prepare( self ):

        self.mysql_cursor = self.application.database.cursor()

        self.method = {
            'get':{
                'recieve': self.recieve,
                'record': self.record,
                'user_info': self.user_info,
                'validate_login': self.validate_login
            },
            'post': {
                'send': self.send
            }
        }

    @tornado.gen.coroutine
    def get( self, sub_uri ):
        
        invoke_method = self.__match_method( 'get', sub_uri )

        result = yield invoke_method()

        self.finish( result )

    @tornado.gen.coroutine
    def post( self, sub_uri ):
        
        invoke_method = self.__match_method( 'post', sub_uri )

        result = yield invoke_method()

        self.finish( result )

    @tornado.gen.coroutine
    def validate_login( self ):

        user_id     = self.get_argument( 'uid', '' )
        timestamp   = self.get_argument( 'time', '' )
        origin_sign = self.get_argument( 'sign', '' )

        sign_package = {
            'token': 'ziruikeji',
            'user_id': user_id,
            'timestamp': timestamp
        }
        sorted_sign_package = collections.OrderedDict(sorted(sign_package.items(), key=lambda t:t[0]))
        sign_string = urllib.urlencode( sorted_sign_package )

        sha1 = hashlib.sha1()
        sha1.update( sign_string )
        check_sign = sha1.hexdigest()

        if check_sign != origin_sign:
            message = {
                'error_code': 1,
                'message': 'Invalid login'
            }
            raise tornado.gen.Return( json.dumps( message ) ) 
        else:
            users = self.__find_users( user_id )

            self.set_secure_cookie( 'user', user_id )
            raise tornado.gen.Return( self.render_string( 'index.html', users = users ) )

    @tornado.gen.coroutine
    def send( self ):

        from_uid = self.current_user
        to_uid   = self.get_argument( 'to_uid' )
        content  = self.get_argument( 'content' )

        message = {
            'error_code': 0,
            'message': 'ok'
        }

        try:
            self.__send_message( from_uid, to_uid, content )
        except:
            message['error_code'] = 1
            message['message'] = 'Fail'

        raise tornado.gen.Return( json.dumps( message ) )

    @tornado.gen.coroutine
    def recieve( self ):

        start_timestamp     = int( time.time() )
        current_timestamp   = int( time.time() )

        results = []

        print 'user id: {user_id}, start time: {time}'.format( user_id=self.current_user, time=start_timestamp )
        
        while ( current_timestamp - start_timestamp ) < 30 :
            
            messages = self.__retrieve_message()
            
            if messages is not None:
                results = list( messages )
                break

            yield tornado.gen.sleep( 0.005 )
            current_timestamp = int( time.time() )

        print 'user id: {user_id}, end time: {time}'.format( user_id=self.current_user, time=current_timestamp )

        results = [ { 'from_uid': m[1], 'content': m[2], 'timestamp': m[3] } for m in results ]

        raise tornado.gen.Return( json.dumps( { 'error_code': 0, 'messages': results } ) )

    def record( self ):

        pass

    def user_info( self ):

        pass

    def not_found( self ):

        return 'Not Found'
