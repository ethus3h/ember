"""Magic Square Module."""
"""Contains class and command line main function for magic square encryption."""

import math

#-------------------------------------------------------------------------------
# class:    MagicSquare
#-------------------------------------------------------------------------------

class MagicSquare:
    """Magic Square Class."""
    """Contains data and functions for magic square encryption."""
    
    #---------------------------------------------------------------------------
    
    def __init__( self, message = "" ):
        """Constructor.  Sets up the length properties of the magic square"""
        """and pads the message tail with spaces."""
            
        self._edge = int( math.ceil( math.sqrt( len( message ) ) ) )
        self._end = int( self._edge**2 )
        self._msg = message.ljust( self._end ).replace( " ", "_" )
        
    #---------------------------------------------------------------------------

    def __str__( self ):
        """Function providing formatting for the built-in print function."""

        return self._msg
        
    #---------------------------------------------------------------------------

    def encodeDecode( self ):
        """Function which encodes/decodes--i.e. transposes--the magic square."""
        
        msg = ""
        
        for column in range( self._edge ):
            for columnItems in range ( column, self._end, self._edge ):
                msg += self._msg[ columnItems ]
        
        self._msg = msg

    #---------------------------------------------------------------------------

    def format( self ):
        """Function which formats the message string as a magic square."""

        formatted = ""
        
        firstRowStart = 0
        lastRowStart = self._end - self._edge
        rowLength = self._edge
        
        for rowStart in range( firstRowStart, lastRowStart + 1, rowLength ):
            formatted += "\n"
            
            for char in self._msg[ rowStart : rowStart + rowLength ]:
                formatted += char + " "
        
        return formatted

#-------------------------------------------------------------------------------
    
def main():
    """Function which allows command line use of MagicSquare class."""
    
    print "\n\nWelcome to the Magic Square Encryption program!"
    print "\n\nFirst written by scotland. Version 3, 2 September 2012. Edited for Wreathe 7: Elegy."
    print "\n\n(Caveat: It's not exactly secure...)"
    
    while True:
        userInput = str( raw_input( "\nInput a message, [Enter] to encode/decode, or [Q]uit:  " ) )
        
        if userInput == "Q" or userInput == "q":
            break
        
        if len( userInput ) > 0:
            ms = MagicSquare( userInput )
        
        print "\nEncoding/decoding the magic square message..."
        print ms.format()           # formatted as a magic square

        ms.encodeDecode()
        print "\n%s" % ms           # raw message
    
    print "\nThank you for using the Magic Square Encryption program.\n"
    
#-------------------------------------------------------------------------------

if __name__ == "__main__":
    main()
