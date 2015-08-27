
import tornado.web
import tornado.httpserver
import tornado.ioloop

import MySQLdb
import config.mysql
import config.app
from chat_handler import chat_handler

class Application( tornado.web.Application ):

    def __init__( self ):

        self.database = MySQLdb.connect( 
            host    = config.mysql.host, 
            user    = config.mysql.user, 
            passwd  = config.mysql.password,
            db      = config.mysql.database,
            charset = config.mysql.charset,
            port    = config.mysql.port )

        handlers = [ (r'/chat/(\w+)', chat_handler ) ]

        settings = {
            'cookie_secret': config.app.cookie_secret,
            'template_path': 'templates',
            'static_path': 'static',
            'debug': True
        }

        tornado.web.Application.__init__( self, handlers, **settings )

if __name__ == '__main__':
    app = Application()

    server = tornado.httpserver.HTTPServer( app )
    server.listen( 8080 )
    tornado.ioloop.IOLoop.instance().start()