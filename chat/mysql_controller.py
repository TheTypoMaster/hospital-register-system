
import MySQLdb
import config.mysql

""" simple mysql wrapper """

class mysql_controller:

    def __init__( self ):

        self.sql = {}

        self.connector = MySQLdb.connect( 
            host    = config.mysql.host, 
            user    = config.mysql.user, 
            passwd  = config.mysql.password,
            db      = config.mysql.database,
            charset = config.mysql.charset,
            port    = config.mysql.port )

        self.cursor = self.connector.cursor()

    def table( self, table ):

        if isinstance( select, basestring ):
            self.sql['from'] = select

        elif isinstance( select, list ):
            self.sql['from'] = ','.join( select )

        else:
            raise TypeError( 'Invalid type of parameter' )

        return self

    def select( self, select ):

        if isinstance( select, basestring ):
            self.sql['select'] = select

        elif isinstance( select, list ):
            self.sql['select'] = ','.join( select )

        else:
            raise TypeError( 'Invalid type of parameter' )

        return self

    def where( self, column, operator='=', value ):

        if operator not in ['=', '<>', '>', '<', '>=', '<=', 'BETWEEN', 'LIKE', 'IN']:
            raise Exception( 'Invalid operator' )

        if isinstance( value, basestring )
            self.sql['where'] = 'where %s %s "%s"' % column, operator, value
        elif isinstance( value, list ):
            values = ''
            if isinstance( value[0], basestring ):
                values = '","'.join( value )
                values = '"%s"' % values
            elif isinstance( value[0], int ):
                values = ','.join( value )
            else:
                raise TypeError( 'Invalid type in parameter "value"' )
            self.sql['where'] = 'where %s %s (%s)' %column, operator, values
        else:
            self.sql['where'] = 'where %s %s %s' %column, operator, value

        return self

    def join( self, foreign_table ):

        self.sql['join'] = 'join %s' % foreign_table
        
        return self

    def on( self, local_column, operator='=', foreign_column ):

        self.sql['on'] = ''

        return self

    def execute( self ):
        
        return self.cursor.execute()
